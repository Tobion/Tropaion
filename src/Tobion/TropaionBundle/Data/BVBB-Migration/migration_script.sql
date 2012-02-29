/* ######################
Daten-Migration und Daten-Konsolidierung
Microsoft Access Datenbank -> MySQL
Original Access-Datenbank wurde über ODBC nach MySQL exportiert und dort in das neue Datenbankschema transformiert.
Dabei ist dieses Script über User Variables konfigurierbar, um so mehrere Datenbanken saisonübergreifend in eine einzige Datenbank zusammenführen zu können.
-------------------------------------------------
ETL-Prozess als SQL-Script

Nutzt fortgeschrittene Funktionen von MySQL5 (Stored Procedures, User Variables)
Extraktion: Alle Stamm-Daten
Transformation: Normalisierung (Entfernung von Redundanzen und möglichen Inkonsistenzen sowie verständlichere Datenstrukturen durch monothematische Relationen)
Load: INSERT INTO ... SELECT und stored procedures, wenn aus Attributen neue Zeilen generiert werden müssen.
-------------------------------------------------

TODO:
- Fehlende Ergebnisse in 2008-09?!
- Statistik Caching: Berechnete Ligatabelle in teams Tabelle speichern
- beiderseitige Niederlage nicht 0:0 sondern als z.B. -2:-2 speichern -> flexibler, da variabel in der Negativ-Wertung und auch konsistent mit Summenberechnung -> benötigt signed columns
- Verein-2-Verant
- Spieler: Zeit-, Mannschafts- und Disziplinbegrenzung
    - M-Aufstellungen: M-S-frei-Datum-X
    - M-Spieler-Stamm: Spi-ab-Datum / Spi-hin-begrenzt / Spi-hin-nur-HE / Spi-hin-nur-HD
- Wie werden ausgetretene Mitglieder markiert? Mit-aktiv = 2 -> kaum vorhanden
- Sortierung der Tabelle?: Punkte -> Spiele -> Sätze-Diff -> Spielpunkte-Diff (steht nicht explizit in der Satzung)
    - Was bei totalem Gleichstand?
    - Was wenn mehrere Mannschaften zurückziehen? Nach Datum? (gibt kein entsprechendes Feld in der Datenbank) (Scheinbar bisher nach erzielten Punkten)
- Spielberechtigung (eligible to play) von Spielern (Vereinswechsel, Abmeldung, Sperrfristen etc.) und Mannschaften (wird entzogen wenn mehr als 2 mal nicht angetreten) speichern
- 3 Felder jeweils für Datum und Ort: Angesetzter Termin, Verlegter Termin und Gespielter Termin (teilweise erledigt)
	- scheduled_date, scheduled_location
	- moved_date, moved_location
	- performed_date, performed_location
- clubs.contact_person_id = 0 ?! Integrity error
- athletes.country = Integer like 0 instead of 'DE'
- set venues.lontitude, latitude
- no_fight boolean -> smallint indicating the team that did not participate
- tournament.start_date und end_date


DBV-Statistik: 4.164 Mitglieder in Berlin-Brandenburg 2009
 
###################### */

/*
Publishing Database:
1. Copy database (constraints get lost)
2. UPDATE athletes SET birthday = '1970-01-01', country = '', zip = '', city = '', street = '';
3. Export data
4. Combine with database schema

/*


DROP TABLE `hallen-stamm`, `m-aufstellungen`, `m-klasse-staffel`, `m-spieler-stamm`, `m-spieltermine`, `m-spieltermine-stamm`, `mitglieder-stamm`, `verein-stamm`;

TRUNCATE TABLE `games`;
TRUNCATE TABLE `matches`;
TRUNCATE TABLE `teammatches`;
TRUNCATE TABLE `lineups`;
TRUNCATE TABLE `teams`;
TRUNCATE TABLE `clubs`;
TRUNCATE TABLE `leagues`;
TRUNCATE TABLE `venues`;
TRUNCATE TABLE `athletes`;

*/

INSERT INTO `match_types` (`id`, `name`, `x_on_x`, `gender`) VALUES
(1, '1.HD', 2, 1),
(2, 'DD', 2, 2),
(3, '2.HD', 2, 1),
(4, 'DE', 1, 2),
(5, 'GD', 2, 3),
(6, '1.HE', 1, 1),
(7, '2.HE', 1, 1),
(8, '3.HE', 1, 1);

/*
Fehlernummern der Umwertungen am besten in Verursacher (wrongdoer) und Begründung (wrongdoing_reason) aufspalten (siehe Datenbankschema)
Neben diesen globalen Mannschaftsfehlern für die die ganze Mannschaft bestraft wird,
gibt es noch die Aufstellungsfehler in den einzelnen Disziplinen, wobei dann nur diese umgewertet werden.
Deren Begründung wird aber nicht gespeichert.
revised_score in teammatches auch für diese Fälle 
*/

CREATE TABLE IF NOT EXISTS `errormessages` (
  `id` smallint(5) unsigned NOT NULL,
  `message` varchar(255) NOT NULL,
  `revaluation_wrongdoer` smallint(5) unsigned NOT NULL,
  `revaluation_reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Fehlermeldungen';

INSERT INTO `errormessages` (`id`, `message`, `revaluation_wrongdoer`, `revaluation_reason`) VALUES
(1, 'die  H e i m mannschaft ist nicht angetreten; sie hat kampflos verloren !!!', 1, 1),
(2, 'die  G a s t mannschaft ist nicht angetreten; sie hat kampflos verloren !!!', 2, 1),
(3, 'b e i d e  Mannschaften sind nicht angetreten; b e i d e  erhalten 0:8 Punkte !!!', 3, 1),
(4, 'die  H e i m mannschaft hat zurückgezogen; sie fällt aus der Wertung !!!', 1, 7), -- wird gar nicht gesetzt, obwohl es vorkommt
(5, 'die  G a s t mannschaft hat zurückgezogen; sie fällt aus der Wertung !!!', 2, 7), -- wird gar nicht gesetzt, obwohl es vorkommt
(6, 'die Aufstellung der  H e i m mannschaft war falsch; sie hat 0:8 verloren !!!', 1, 2),
(7, 'die Aufstellung der  G a s t mannschaft war falsch; sie hat 0:8 verloren !!!', 2, 2),
(8, 'die Aufstellung beider Mannschaften war falsch; sie haben beide mit 0:8 verloren !!!', 3, 2),
(9, 'die  H e i m mannschaft hatte zu wenig Spieler; sie hat 0:8 verloren !!!', 1, 3),
(10, 'die  G a s t mannschaft hatte zu wenig Spieler; sie hat 0:8 verloren !!!', 2, 3),
(11, 'der Spielbericht wurde vom  H e i m verein nicht abgegeben; er hat 0:8 verloren !!!', 1, 6),
(12, 'die Heimmannschaft hatte zu  v i e l e  Spieler eingesetzt; sie hat 0:8 verloren !!!', 1, 4),
(13, 'die Gastmannschaft hatte zu  v i e l e  Spieler eingesetzt; sie hat 0:8 verloren !!!', 2, 4),
(14, 'beide Mannschaften hatten zu  v i e l e  Spieler eingesetzt; sie haben beide 0:8 verloren !!!', 3, 4),
(15, 'die Heimmanschaft hat den Spielbericht nicht abgegeben; sie hat 0:8 verloren (umgewertet) !!!', 1, 6),
(16, 'Spieler der Heimmannschaft wurden mehr als 2 x höher eingesetzt; sie hat 0:8 verloren !!!', 1, 5),
(17, 'Spieler der Gastmannschaft wurden mehr als 2 x höher eingesetzt; sie hat 0:8 verloren !!!', 2, 5);


CREATE TABLE IF NOT EXISTS `league_classes` (
  `class_abbr` varchar(10) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  PRIMARY KEY (`class_abbr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Namen der Ligaklassen';

INSERT INTO `league_classes` (`class_abbr`, `class_name`) VALUES
('VB', 'Verbandsklasse'),
('BB', 'Berlin-Brandenburg-Liga'),
('LL', 'Landesliga'),
('BZ', 'Bezirksklasse'),
('AK', 'A-Klasse'),
('BK', 'B-Klasse'),
('CK', 'C-Klasse'),
('DK', 'D-Klasse'),
('EK', 'E-Klasse'),
('FK', 'F-Klasse'),
('GK', 'G-Klasse'),
('Ersatz', 'Ersatzspielerliga');


REPLACE INTO `users` (`id`, `username`, `slug`, `full_name`, `email`)
    VALUES (1, 'BVBB', 'BVBB', 'Badminton-Verband Berlin-Brandenburg e.V.', 'info@bvbb.net');
REPLACE INTO `users` (`id`, `username`, `slug`, `full_name`, `email`, `athlete_id`)
    VALUES (2, 'Schuch', 'Schuch', 'Wolfgang Schuch', 'wolfgang.schuch@steuerberater-schuch.de', 800847);

	
REPLACE INTO `tournaments` (`id`, `owner_id`, `host_id`, `short_name`, `full_name`, `season`, `slug`, `sport`) VALUES
(1, 1, NULL, 'BBMM 2002/03', 'Berlin-Brandenburger Mannschaftsmeisterschaft Saison 2002/03', '2002/03', 'BBMM-2002-03', 'Badminton'),
(2, 1, NULL, 'BBMM 2003/04', 'Berlin-Brandenburger Mannschaftsmeisterschaft Saison 2003/04', '2003/04', 'BBMM-2003-04', 'Badminton'),
(3, 1, NULL, 'BBMM 2004/05', 'Berlin-Brandenburger Mannschaftsmeisterschaft Saison 2004/05', '2004/05', 'BBMM-2004-05', 'Badminton'),
(4, 1, NULL, 'BBMM 2005/06', 'Berlin-Brandenburger Mannschaftsmeisterschaft Saison 2005/06', '2005/06', 'BBMM-2005-06', 'Badminton'),
(5, 1, NULL, 'BBMM 2006/07', 'Berlin-Brandenburger Mannschaftsmeisterschaft Saison 2006/07', '2006/07', 'BBMM-2006-07', 'Badminton'),
(6, 1, NULL, 'BBMM 2007/08', 'Berlin-Brandenburger Mannschaftsmeisterschaft Saison 2007/08', '2007/08', 'BBMM-2007-08', 'Badminton'),
(7, 1, NULL, 'BBMM 2008/09', 'Berlin-Brandenburger Mannschaftsmeisterschaft Saison 2008/09', '2008/09', 'BBMM-2008-09', 'Badminton'),
(8, 1, NULL, 'BBMM 2009/10', 'Berlin-Brandenburger Mannschaftsmeisterschaft Saison 2009/10', '2009/10', 'BBMM-2009-10', 'Badminton');


DELIMITER //

DROP PROCEDURE IF EXISTS migrate_leagues //    
    
CREATE PROCEDURE migrate_leagues(IN tournament INT)
BEGIN
	SET @has_promotion_column = 0;
		
	SELECT COUNT(*) INTO @has_promotion_column
		FROM INFORMATION_SCHEMA.COLUMNS
		WHERE TABLE_NAME = 'M-Klasse-Staffel'
			AND COLUMN_NAME = 'Anz-Aufsteiger';
			
	SET @last_league = '';
	SET @last_level = 4;    # erstes eingefuegtes level wird zu @last_level + 1
			
	IF (@has_promotion_column) THEN
		INSERT INTO `leagues` (`tournament_id`, `class_abbr`, `class_name`, `division`, `class_level`, `promoted_number`, `relegated_number`) 
			SELECT tournament, `Klasse`, 
				# Example-Transformation: '  C - K L A S S E   II    ' -> 'C-KLASSE'
				REPLACE(LEFT(
					TRIM(`Klasse-Staffel-Text`), 
					IF(
						LOCATE('  ', TRIM(`Klasse-Staffel-Text`)) = 0,
						CHAR_LENGTH(TRIM(`Klasse-Staffel-Text`)),
						LOCATE('  ', TRIM(`Klasse-Staffel-Text`)) - 1
					)
				), ' ', ''),
				`Staffel`, 
		        IF(`Klasse` <> @last_league AND ((@last_league := `Klasse`) <> ''), 
		            @last_level := @last_level + 1, 
		            @last_level
		        ),
				`Anz-Aufsteiger`, `Anz-Absteiger`
			FROM `M-Klasse-Staffel`
			ORDER BY `Reihenfolge`;
	ELSE
		INSERT INTO `leagues` (`tournament_id`, `class_abbr`, `class_name`, `division`, `class_level`, `promoted_number`, `relegated_number`) 
			SELECT tournament, `Klasse`, 
				# Example-Transformation: '  C - K L A S S E   II    ' -> 'C-KLASSE'
				REPLACE(LEFT(
					TRIM(`Klasse-Staffel-Text`), 
					IF(
						LOCATE('  ', TRIM(`Klasse-Staffel-Text`)) = 0,
						CHAR_LENGTH(TRIM(`Klasse-Staffel-Text`)),
						LOCATE('  ', TRIM(`Klasse-Staffel-Text`)) - 1
					)
				), ' ', ''),
				`Staffel`, 
		        IF(`Klasse` <> @last_league AND ((@last_league := `Klasse`) <> ''), 
		            @last_level := @last_level + 1, 
		            @last_level
		        ),
				0, 0
			FROM `M-Klasse-Staffel`
			ORDER BY `Reihenfolge`;
	END IF;
	
	# alter table version
	#DECLARE CONTINUE HANDLER FOR 1060 BEGIN END;
	#ALTER TABLE `M-Klasse-Staffel` ADD `Anz-Aufsteiger` TINYINT UNSIGNED NOT NULL DEFAULT '0';    
END
//

DROP PROCEDURE IF EXISTS migrate_lineups //    
    
CREATE PROCEDURE migrate_lineups(IN tournament INT)
BEGIN
    DECLARE done TINYINT DEFAULT 0;   
    
    DECLARE 
            team_id, 
            athlete_pos1_ms, athlete_pos2_ms, athlete_pos3_ms, athlete_pos4_ms, athlete_pos5_ms, 
            athlete_pos1_ws, athlete_pos2_ws, athlete_pos3_ws  
        INT UNSIGNED;
    DECLARE stage TINYINT;
    
    DECLARE lineups_cursor CURSOR FOR 
        SELECT t.id, a.`M-S-Hin-Rück`, 
            a.`M-S-1-HE`, a.`M-S-2-HE`, a.`M-S-3-HE`, a.`M-S-4-HE`, a.`M-S-5-HE`, 
            a.`M-S-1-DE`, a.`M-S-2-DE`, a.`M-S-3-DE`
        FROM `M-Aufstellungen` AS a
        INNER JOIN `teams` AS t ON (a.`M-S-Verein` = t.`club_id` AND a.`M-S-Mannschaft` = t.`team_number`) 
        INNER JOIN `leagues` AS l ON (t.`league_id` = l.`id` AND l.`tournament_id` = tournament);
        # WHERE a.`M-S-Staffel` <> '*'; # wegen INNER join mit teams nicht notwendig
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    OPEN lineups_cursor;
    
    ### regular athletes
    
    REPEAT
        FETCH lineups_cursor INTO 
            team_id, stage,
            athlete_pos1_ms, athlete_pos2_ms, athlete_pos3_ms, athlete_pos4_ms, athlete_pos5_ms, 
            athlete_pos1_ws, athlete_pos2_ws, athlete_pos3_ws;
            
        IF NOT done THEN
            IF (athlete_pos1_ms <> 0) THEN   
                INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
                    VALUES (team_id, stage, athlete_pos1_ms, 1);
            END IF;
            IF (athlete_pos2_ms <> 0) THEN   
                INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
                    VALUES (team_id, stage, athlete_pos2_ms, 2);
            END IF;
            IF (athlete_pos3_ms <> 0) THEN   
                INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
                    VALUES (team_id, stage, athlete_pos3_ms, 3);
            END IF;
            IF (athlete_pos4_ms <> 0) THEN   
                INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
                    VALUES (team_id, stage, athlete_pos4_ms, 4);
            END IF;
            IF (athlete_pos5_ms <> 0) THEN   
                INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
                    VALUES (team_id, stage, athlete_pos5_ms, 5);
            END IF;
            IF (athlete_pos1_ws <> 0) THEN   
                INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
                    VALUES (team_id, stage, athlete_pos1_ws, 1);
            END IF;
            IF (athlete_pos2_ws <> 0) THEN   
                INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
                    VALUES (team_id, stage, athlete_pos2_ws, 2);
            END IF;
            IF (athlete_pos3_ws <> 0) THEN   
                INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
                    VALUES (team_id, stage, athlete_pos3_ws, 3);
            END IF;

        END IF;
    UNTIL done END REPEAT;

    CLOSE lineups_cursor;
    
    ### substitute athletes
    
    # first half
    INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
        SELECT t.`id`, 1, s.`Spi-Pass-Nr`, 0
        FROM `M-Spieler-Stamm` AS s
        INNER JOIN `teams` AS t ON ( s.`Spi-hin-Vereins-Nr` = t.`club_id` )
        INNER JOIN `leagues` AS l ON ( t.`league_id` = l.`id` )
        WHERE s.`Spi-hin-Mannschaft` = '99' 
            AND l.`tournament_id` = tournament
            AND l.`level` = '255';
            #AND t.`league_id` = @substitute_league_id
        
    # second half
    INSERT INTO `lineups` (`team_id`, `stage`, `athlete_id`, `position`)
        SELECT t.`id`, 2, s.`Spi-Pass-Nr`, 0
        FROM `M-Spieler-Stamm` AS s
        INNER JOIN `teams` AS t ON ( s.`Spi-rue-Vereins-Nr` = t.`club_id` )
        INNER JOIN `leagues` AS l ON ( t.`league_id` = l.`id` )
        WHERE s.`Spi-rue-Mannschaft` = '99' 
            AND l.`tournament_id` = tournament
            AND l.`level` = '255';
            #AND t.`league_id` = @substitute_league_id

END
//



DROP PROCEDURE IF EXISTS migrate_games //  

CREATE PROCEDURE migrate_games(
    IN match_id INT UNSIGNED,
    IN game1_team1_score SMALLINT UNSIGNED, IN game1_team2_score SMALLINT UNSIGNED, 
    IN game2_team1_score SMALLINT UNSIGNED, IN game2_team2_score SMALLINT UNSIGNED, 
    IN game3_team1_score SMALLINT UNSIGNED, IN game3_team2_score SMALLINT UNSIGNED,
    IN game1_team1_orig_score SMALLINT UNSIGNED, IN game1_team2_orig_score SMALLINT UNSIGNED, 
    IN game2_team1_orig_score SMALLINT UNSIGNED, IN game2_team2_orig_score SMALLINT UNSIGNED, 
    IN game3_team1_orig_score SMALLINT UNSIGNED, IN game3_team2_orig_score SMALLINT UNSIGNED
    )
BEGIN    
    -- Saetze mit 0-0 als Ergebnis brauchen nicht aufgenommen werden, da sie nicht ausgetragen wurden und nur Fassade sind
    -- Ein Match ohne Beteiligung und ohne Ergebnisaenderung hat somit verstaendlicherweise auch keine Saetze
        
    IF (game1_team1_score <> 0 OR game1_team2_score <> 0 OR 
        game2_team1_score <> 0 OR game2_team2_score <> 0 OR
        game3_team1_score <> 0 OR game3_team2_score <> 0
    ) THEN
        INSERT INTO `games` (`match_id`, `game_sequence`, `team1_score`, `team2_score`) 
            VALUES (match_id, 1, game1_team1_score, game1_team2_score); 

        INSERT INTO `games` (`match_id`, `game_sequence`, `team1_score`, `team2_score`) 
            VALUES (match_id, 2, game2_team1_score, game2_team2_score); 

        -- ignoriere Dummy-Werte des dritten Satzes
        IF (game3_team1_score <> 0 OR game3_team2_score <> 0) THEN
            INSERT INTO `games` (`match_id`, `game_sequence`, `team1_score`, `team2_score`) 
                VALUES (match_id, 3, game3_team1_score, game3_team2_score);
        END IF;
    END IF;


        -- ein umgewertetes Spiel kann auch uspruenglich 0-0 ausgegangen sein (bei einem Match ohne Beteiligung)
        -- dieses muss also einfuegt werden, um auf Aenderung pruefen zu koennen 
        -- -> hinfaellig mit der Aufnahme von revised_score in matches
        
        IF (
            (   -- ungleich Standard-Wert; dritter Satz unwichtig, wenn ersten beiden 0:0 ausgegangen
                game1_team1_orig_score <> 0 OR game1_team2_orig_score <> 0 OR 
                game2_team1_orig_score <> 0 OR game2_team2_orig_score <> 0 
            ) AND
            (   -- unterschiedliches urspruengliches und endgueltiges Ergebnis
                -- Ergebnis-geaendert-flag inkonsistent:
                -- - nicht immer gesetzt, obwohl es sollte: 2009-10/BB/0/BerlBrau-2_BG%20ProZ-1
                -- - angebliche Aenderung, wo urspruengliches und endgueltiges Ergebnis uebereinstimmen: 2007-08/BZ/3/Gaselan-2_TSVSCSBF-2 [21:0 21:0 (21:0 21:0)]

                game1_team1_orig_score <> game1_team1_score OR game1_team2_orig_score <> game1_team2_score OR 
                game2_team1_orig_score <> game2_team1_score OR game2_team2_orig_score <> game2_team2_score OR 
                (game3_team1_orig_score <> game3_team1_score AND game3_team1_orig_score <> 99) OR 
                (game3_team2_orig_score <> game3_team2_score AND game3_team2_orig_score <> 99)
            )
        ) THEN
            INSERT INTO `games` (`match_id`, `game_sequence`, `team1_score`, `team2_score`, `annulled`) 
                VALUES (match_id, 1, game1_team1_orig_score, game1_team2_orig_score, 1); 

            INSERT INTO `games` (`match_id`, `game_sequence`, `team1_score`, `team2_score`, `annulled`) 
                VALUES (match_id, 2, game2_team1_orig_score, game2_team2_orig_score, 1); 
      
            -- ignoriere Dummy-Werte des dritten Satzes
            IF (game3_team1_orig_score <> 99 OR game3_team2_orig_score <> 99) THEN
                INSERT INTO `games` (`match_id`, `game_sequence`, `team1_score`, `team2_score`, `annulled`) 
                    VALUES (match_id, 3, game3_team1_orig_score, game3_team2_orig_score, 1);
            END IF; 
        END IF;
END
//

DROP PROCEDURE IF EXISTS migrate_matches //    
    
CREATE PROCEDURE migrate_matches(IN tournament INT)
BEGIN
    DECLARE done TINYINT DEFAULT 0;
    DECLARE last_teammatch_id INT UNSIGNED;
    DECLARE last_match_id INT UNSIGNED;
    
    
    DECLARE performed_at DATETIME;
    DECLARE venue_id, team1_id, team2_id INT UNSIGNED; # team1_vereins_nr, team2_vereins_nr
    #DECLARE team1_team_nr, team2_team_nr SMALLINT UNSIGNED;
    DECLARE team1_score, team2_score SMALLINT UNSIGNED;
	DECLARE stage TINYINT UNSIGNED DEFAULT 1;
    DECLARE score_available, no_fight TINYINT DEFAULT 0;
    DECLARE errormessage VARCHAR(255) DEFAULT NULL;
	DECLARE revaluation_wrongdoer, revaluation_reason TINYINT UNSIGNED;
    
    # 1. HE
    DECLARE ms1_team1_player, ms1_team1_partner, ms1_team2_player, ms1_team2_partner INT UNSIGNED;
    DECLARE 
            ms1_game1_team1_score, ms1_game1_team2_score, 
            ms1_game2_team1_score, ms1_game2_team2_score, 
            ms1_game3_team1_score, ms1_game3_team2_score 
        SMALLINT UNSIGNED; 
    DECLARE ms1_annulled_score TINYINT DEFAULT 0;
    DECLARE 
            ms1_game1_team1_orig_score, ms1_game1_team2_orig_score, 
            ms1_game2_team1_orig_score, ms1_game2_team2_orig_score, 
            ms1_game3_team1_orig_score, ms1_game3_team2_orig_score 
        SMALLINT UNSIGNED;
        
    # 2. HE
    DECLARE ms2_team1_player, ms2_team1_partner, ms2_team2_player, ms2_team2_partner INT UNSIGNED;
    DECLARE 
            ms2_game1_team1_score, ms2_game1_team2_score, 
            ms2_game2_team1_score, ms2_game2_team2_score, 
            ms2_game3_team1_score, ms2_game3_team2_score 
        SMALLINT UNSIGNED; 
    DECLARE ms2_annulled_score TINYINT DEFAULT 0;
    DECLARE 
            ms2_game1_team1_orig_score, ms2_game1_team2_orig_score, 
            ms2_game2_team1_orig_score, ms2_game2_team2_orig_score, 
            ms2_game3_team1_orig_score, ms2_game3_team2_orig_score 
        SMALLINT UNSIGNED;
    
    # 3. HE
    DECLARE ms3_team1_player, ms3_team1_partner, ms3_team2_player, ms3_team2_partner INT UNSIGNED;
    DECLARE 
            ms3_game1_team1_score, ms3_game1_team2_score, 
            ms3_game2_team1_score, ms3_game2_team2_score, 
            ms3_game3_team1_score, ms3_game3_team2_score 
        SMALLINT UNSIGNED; 
    DECLARE ms3_annulled_score TINYINT DEFAULT 0;
    DECLARE 
            ms3_game1_team1_orig_score, ms3_game1_team2_orig_score, 
            ms3_game2_team1_orig_score, ms3_game2_team2_orig_score, 
            ms3_game3_team1_orig_score, ms3_game3_team2_orig_score 
        SMALLINT UNSIGNED;
    
    # DE 
    DECLARE ws_team1_player, ws_team1_partner, ws_team2_player, ws_team2_partner INT UNSIGNED;
    DECLARE 
            ws_game1_team1_score, ws_game1_team2_score, 
            ws_game2_team1_score, ws_game2_team2_score, 
            ws_game3_team1_score, ws_game3_team2_score 
        SMALLINT UNSIGNED; 
    DECLARE ws_annulled_score TINYINT DEFAULT 0;
    DECLARE 
            ws_game1_team1_orig_score, ws_game1_team2_orig_score, 
            ws_game2_team1_orig_score, ws_game2_team2_orig_score, 
            ws_game3_team1_orig_score, ws_game3_team2_orig_score 
        SMALLINT UNSIGNED;
  
    # 1.HD 
    DECLARE md1_team1_player, md1_team1_partner, md1_team2_player, md1_team2_partner INT UNSIGNED;
    DECLARE 
            md1_game1_team1_score, md1_game1_team2_score, 
            md1_game2_team1_score, md1_game2_team2_score, 
            md1_game3_team1_score, md1_game3_team2_score 
        SMALLINT UNSIGNED; 
    DECLARE md1_annulled_score TINYINT DEFAULT 0;
    DECLARE 
            md1_game1_team1_orig_score, md1_game1_team2_orig_score, 
            md1_game2_team1_orig_score, md1_game2_team2_orig_score, 
            md1_game3_team1_orig_score, md1_game3_team2_orig_score 
        SMALLINT UNSIGNED;
    
    # 2.HD 
    DECLARE md2_team1_player, md2_team1_partner, md2_team2_player, md2_team2_partner INT UNSIGNED;
    DECLARE 
            md2_game1_team1_score, md2_game1_team2_score, 
            md2_game2_team1_score, md2_game2_team2_score, 
            md2_game3_team1_score, md2_game3_team2_score 
        SMALLINT UNSIGNED; 
    DECLARE md2_annulled_score TINYINT DEFAULT 0;
    DECLARE 
            md2_game1_team1_orig_score, md2_game1_team2_orig_score, 
            md2_game2_team1_orig_score, md2_game2_team2_orig_score, 
            md2_game3_team1_orig_score, md2_game3_team2_orig_score 
        SMALLINT UNSIGNED;
        
    # DD 
    DECLARE wd_team1_player, wd_team1_partner, wd_team2_player, wd_team2_partner INT UNSIGNED;
    DECLARE 
            wd_game1_team1_score, wd_game1_team2_score, 
            wd_game2_team1_score, wd_game2_team2_score, 
            wd_game3_team1_score, wd_game3_team2_score 
        SMALLINT UNSIGNED; 
    DECLARE wd_annulled_score TINYINT DEFAULT 0;
    DECLARE 
            wd_game1_team1_orig_score, wd_game1_team2_orig_score, 
            wd_game2_team1_orig_score, wd_game2_team2_orig_score, 
            wd_game3_team1_orig_score, wd_game3_team2_orig_score 
        SMALLINT UNSIGNED;
        
    # MX
    DECLARE mx_team1_player, mx_team1_partner, mx_team2_player, mx_team2_partner INT UNSIGNED;
    DECLARE 
            mx_game1_team1_score, mx_game1_team2_score, 
            mx_game2_team1_score, mx_game2_team2_score, 
            mx_game3_team1_score, mx_game3_team2_score 
        SMALLINT UNSIGNED; 
    DECLARE mx_annulled_score TINYINT DEFAULT 0;
    DECLARE 
            mx_game1_team1_orig_score, mx_game1_team2_orig_score, 
            mx_game2_team1_orig_score, mx_game2_team2_orig_score, 
            mx_game3_team1_orig_score, mx_game3_team2_orig_score 
        SMALLINT UNSIGNED;    
  
    # Ueberpruefen von s1.`1-Ergebnisse-geändert` und s2.`1-Ergebnisse-geändert`, da Inkonsistenzen vorhanden sind
    # (auf der einen Seite als geaendert markiert und auf der anderen nicht), 
    # wie z.B. bei http://www.bvbb.net/fileadmin/user_upload/schuch/meisterschaft/spielberichte-vereine/31-03_32-06.HTML (siehe Access-Db dieses Spiels)
    DECLARE matches_cursor CURSOR FOR 
        SELECT v.`id`, ADDTIME(s1.`Datum`, TIME(s1.`Zeit`)), s1.`hin-rück`, t1.id, t2.id,
            (s1.`1-Spiele` + s1.`2-Spiele` + s1.`3-Spiele` + s1.`4-Spiele` + s1.`5-Spiele` + s1.`6-Spiele` + s1.`7-Spiele` + s1.`8-Spiele`),
            (s2.`1-Spiele` + s2.`2-Spiele` + s2.`3-Spiele` + s2.`4-Spiele` + s2.`5-Spiele` + s2.`6-Spiele` + s2.`7-Spiele` + s2.`8-Spiele`),
            IF(s1.`Aufstellung-vorh` = 'J' OR s2.`Aufstellung-vorh` = 'J', 1, 0), 
            IF(s1.`Spiel-kampflos` = 'J' OR s2.`Spiel-kampflos` = 'J', 1, 0), 
            em.message, IFNULL(em.revaluation_wrongdoer, 0), IFNULL(em.revaluation_reason, 0),
            
            NULLIF(s1.`1-Pass-Nr-1`, 0), NULLIF(s1.`1-Pass-Nr-2`, 0), NULLIF(s2.`1-Pass-Nr-1`, 0), NULLIF(s2.`1-Pass-Nr-2`, 0),
            s1.`1-Punkte-1`, s2.`1-Punkte-1`, 
            s1.`1-Punkte-2`, s2.`1-Punkte-2`, 
            s1.`1-Punkte-3`, s2.`1-Punkte-3`,
            IF(s1.`1-Ergebnisse-geändert` = '*' OR s2.`1-Ergebnisse-geändert` = '*', 1, 0),
            s1.`1-ursp-Punkte-1`, s2.`1-ursp-Punkte-1`, 
            s1.`1-ursp-Punkte-2`, s2.`1-ursp-Punkte-2`, 
            s1.`1-ursp-Punkte-3`, s2.`1-ursp-Punkte-3`,
            
            NULLIF(s1.`2-Pass-Nr-1`, 0), NULLIF(s1.`2-Pass-Nr-2`, 0), NULLIF(s2.`2-Pass-Nr-1`, 0), NULLIF(s2.`2-Pass-Nr-2`, 0),
            s1.`2-Punkte-1`, s2.`2-Punkte-1`, 
            s1.`2-Punkte-2`, s2.`2-Punkte-2`, 
            s1.`2-Punkte-3`, s2.`2-Punkte-3`,
            IF(s1.`2-Ergebnisse-geändert` = '*' OR s2.`2-Ergebnisse-geändert` = '*', 1, 0),
            s1.`2-ursp-Punkte-1`, s2.`2-ursp-Punkte-1`, 
            s1.`2-ursp-Punkte-2`, s2.`2-ursp-Punkte-2`, 
            s1.`2-ursp-Punkte-3`, s2.`2-ursp-Punkte-3`,
            
            NULLIF(s1.`3-Pass-Nr-1`, 0), NULLIF(s1.`3-Pass-Nr-2`, 0), NULLIF(s2.`3-Pass-Nr-1`, 0), NULLIF(s2.`3-Pass-Nr-2`, 0),
            s1.`3-Punkte-1`, s2.`3-Punkte-1`, 
            s1.`3-Punkte-2`, s2.`3-Punkte-2`, 
            s1.`3-Punkte-3`, s2.`3-Punkte-3`,
            IF(s1.`3-Ergebnisse-geändert` = '*' OR s2.`3-Ergebnisse-geändert` = '*', 1, 0),
            s1.`3-ursp-Punkte-1`, s2.`3-ursp-Punkte-1`, 
            s1.`3-ursp-Punkte-2`, s2.`3-ursp-Punkte-2`, 
            s1.`3-ursp-Punkte-3`, s2.`3-ursp-Punkte-3`,
            
            NULLIF(s1.`4-Pass-Nr-1`, 0), NULLIF(s1.`4-Pass-Nr-2`, 0), NULLIF(s2.`4-Pass-Nr-1`, 0), NULLIF(s2.`4-Pass-Nr-2`, 0),
            s1.`4-Punkte-1`, s2.`4-Punkte-1`, 
            s1.`4-Punkte-2`, s2.`4-Punkte-2`, 
            s1.`4-Punkte-3`, s2.`4-Punkte-3`,
            IF(s1.`4-Ergebnisse-geändert` = '*' OR s2.`4-Ergebnisse-geändert` = '*', 1, 0),
            s1.`4-ursp-Punkte-1`, s2.`4-ursp-Punkte-1`, 
            s1.`4-ursp-Punkte-2`, s2.`4-ursp-Punkte-2`, 
            s1.`4-ursp-Punkte-3`, s2.`4-ursp-Punkte-3`,
            
            NULLIF(s1.`5-Pass-Nr-1`, 0), NULLIF(s1.`5-Pass-Nr-2`, 0), NULLIF(s2.`5-Pass-Nr-1`, 0), NULLIF(s2.`5-Pass-Nr-2`, 0),
            s1.`5-Punkte-1`, s2.`5-Punkte-1`, 
            s1.`5-Punkte-2`, s2.`5-Punkte-2`, 
            s1.`5-Punkte-3`, s2.`5-Punkte-3`,
            IF(s1.`5-Ergebnisse-geändert` = '*' OR s2.`5-Ergebnisse-geändert` = '*', 1, 0),
            s1.`5-ursp-Punkte-1`, s2.`5-ursp-Punkte-1`, 
            s1.`5-ursp-Punkte-2`, s2.`5-ursp-Punkte-2`, 
            s1.`5-ursp-Punkte-3`, s2.`5-ursp-Punkte-3`,
            
            NULLIF(s1.`6-Pass-Nr-1`, 0), NULLIF(s1.`6-Pass-Nr-2`, 0), NULLIF(s2.`6-Pass-Nr-1`, 0), NULLIF(s2.`6-Pass-Nr-2`, 0),
            s1.`6-Punkte-1`, s2.`6-Punkte-1`, 
            s1.`6-Punkte-2`, s2.`6-Punkte-2`, 
            s1.`6-Punkte-3`, s2.`6-Punkte-3`,
            IF(s1.`6-Ergebnisse-geändert` = '*' OR s2.`6-Ergebnisse-geändert` = '*', 1, 0),
            s1.`6-ursp-Punkte-1`, s2.`6-ursp-Punkte-1`, 
            s1.`6-ursp-Punkte-2`, s2.`6-ursp-Punkte-2`, 
            s1.`6-ursp-Punkte-3`, s2.`6-ursp-Punkte-3`,
            
            NULLIF(s1.`7-Pass-Nr-1`, 0), NULLIF(s1.`7-Pass-Nr-2`, 0), NULLIF(s2.`7-Pass-Nr-1`, 0), NULLIF(s2.`7-Pass-Nr-2`, 0),
            s1.`7-Punkte-1`, s2.`7-Punkte-1`, 
            s1.`7-Punkte-2`, s2.`7-Punkte-2`, 
            s1.`7-Punkte-3`, s2.`7-Punkte-3`,
            IF(s1.`7-Ergebnisse-geändert` = '*' OR s2.`7-Ergebnisse-geändert` = '*', 1, 0),
            s1.`7-ursp-Punkte-1`, s2.`7-ursp-Punkte-1`, 
            s1.`7-ursp-Punkte-2`, s2.`7-ursp-Punkte-2`, 
            s1.`7-ursp-Punkte-3`, s2.`7-ursp-Punkte-3`,
            
            NULLIF(s1.`8-Pass-Nr-1`, 0), NULLIF(s1.`8-Pass-Nr-2`, 0), NULLIF(s2.`8-Pass-Nr-1`, 0), NULLIF(s2.`8-Pass-Nr-2`, 0),
            s1.`8-Punkte-1`, s2.`8-Punkte-1`, 
            s1.`8-Punkte-2`, s2.`8-Punkte-2`, 
            s1.`8-Punkte-3`, s2.`8-Punkte-3`,
            IF(s1.`8-Ergebnisse-geändert` = '*' OR s2.`8-Ergebnisse-geändert` = '*', 1, 0),
            s1.`8-ursp-Punkte-1`, s2.`8-ursp-Punkte-1`, 
            s1.`8-ursp-Punkte-2`, s2.`8-ursp-Punkte-2`, 
            s1.`8-ursp-Punkte-3`, s2.`8-ursp-Punkte-3`
            
        FROM `M-Spieltermine` AS s1
        INNER JOIN `venues` AS v ON (s1.`Halle` = v.`code`) 
        INNER JOIN `M-Spieltermine` AS s2 ON (
            s1.`Vereins-Nr-Gast` = s2.`Vereins-Nr` AND 
            s1.`Mannschaft-Gast` = s2.`Mannschaft` AND
            s1.`Datum` = s2.`Datum` AND 
            s1.`Zeit` = s2.`Zeit`
        )
        INNER JOIN `teams` AS t1 ON (s1.`Vereins-Nr` = t1.`club_id` AND s1.`Mannschaft` = t1.`team_number`) 
        INNER JOIN `leagues` AS l1 ON (t1.`league_id` = l1.`id` AND l1.`tournament_id` = tournament) 
        INNER JOIN `teams` AS t2 ON (s2.`Vereins-Nr` = t2.`club_id` AND s2.`Mannschaft` = t2.`team_number`)
        INNER JOIN `leagues` AS l2 ON (t2.`league_id` = l2.`id` AND l2.`tournament_id` = tournament) 
        LEFT JOIN `errormessages` AS em ON (s1.`Bemerkung` = em.`id`)
        WHERE s1.`Heim-Ausw` = 1;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    OPEN matches_cursor;

    REPEAT
        FETCH matches_cursor INTO 
            venue_id, performed_at, stage,
            team1_id, team2_id,
            team1_score, team2_score,
            score_available, no_fight,
            errormessage, revaluation_wrongdoer, revaluation_reason,
            
            ms1_team1_player, ms1_team1_partner, ms1_team2_player, ms1_team2_partner,            
            ms1_game1_team1_score, ms1_game1_team2_score, 
            ms1_game2_team1_score, ms1_game2_team2_score, 
            ms1_game3_team1_score, ms1_game3_team2_score,
            ms1_annulled_score,
            ms1_game1_team1_orig_score, ms1_game1_team2_orig_score, 
            ms1_game2_team1_orig_score, ms1_game2_team2_orig_score, 
            ms1_game3_team1_orig_score, ms1_game3_team2_orig_score,

            ms2_team1_player, ms2_team1_partner, ms2_team2_player, ms2_team2_partner,            
            ms2_game1_team1_score, ms2_game1_team2_score, 
            ms2_game2_team1_score, ms2_game2_team2_score, 
            ms2_game3_team1_score, ms2_game3_team2_score,
            ms2_annulled_score,
            ms2_game1_team1_orig_score, ms2_game1_team2_orig_score, 
            ms2_game2_team1_orig_score, ms2_game2_team2_orig_score, 
            ms2_game3_team1_orig_score, ms2_game3_team2_orig_score,
            
            ms3_team1_player, ms3_team1_partner, ms3_team2_player, ms3_team2_partner,            
            ms3_game1_team1_score, ms3_game1_team2_score, 
            ms3_game2_team1_score, ms3_game2_team2_score, 
            ms3_game3_team1_score, ms3_game3_team2_score,
            ms3_annulled_score,
            ms3_game1_team1_orig_score, ms3_game1_team2_orig_score, 
            ms3_game2_team1_orig_score, ms3_game2_team2_orig_score, 
            ms3_game3_team1_orig_score, ms3_game3_team2_orig_score,

            ws_team1_player, ws_team1_partner, ws_team2_player, ws_team2_partner,            
            ws_game1_team1_score, ws_game1_team2_score, 
            ws_game2_team1_score, ws_game2_team2_score, 
            ws_game3_team1_score, ws_game3_team2_score,
            ws_annulled_score,
            ws_game1_team1_orig_score, ws_game1_team2_orig_score, 
            ws_game2_team1_orig_score, ws_game2_team2_orig_score, 
            ws_game3_team1_orig_score, ws_game3_team2_orig_score,
            
            md1_team1_player, md1_team1_partner, md1_team2_player, md1_team2_partner,            
            md1_game1_team1_score, md1_game1_team2_score, 
            md1_game2_team1_score, md1_game2_team2_score, 
            md1_game3_team1_score, md1_game3_team2_score,
            md1_annulled_score,
            md1_game1_team1_orig_score, md1_game1_team2_orig_score, 
            md1_game2_team1_orig_score, md1_game2_team2_orig_score, 
            md1_game3_team1_orig_score, md1_game3_team2_orig_score,            

            md2_team1_player, md2_team1_partner, md2_team2_player, md2_team2_partner,            
            md2_game1_team1_score, md2_game1_team2_score, 
            md2_game2_team1_score, md2_game2_team2_score, 
            md2_game3_team1_score, md2_game3_team2_score,
            md2_annulled_score,
            md2_game1_team1_orig_score, md2_game1_team2_orig_score, 
            md2_game2_team1_orig_score, md2_game2_team2_orig_score, 
            md2_game3_team1_orig_score, md2_game3_team2_orig_score,
            
            wd_team1_player, wd_team1_partner, wd_team2_player, wd_team2_partner,            
            wd_game1_team1_score, wd_game1_team2_score, 
            wd_game2_team1_score, wd_game2_team2_score, 
            wd_game3_team1_score, wd_game3_team2_score,
            wd_annulled_score,
            wd_game1_team1_orig_score, wd_game1_team2_orig_score, 
            wd_game2_team1_orig_score, wd_game2_team2_orig_score, 
            wd_game3_team1_orig_score, wd_game3_team2_orig_score,

            mx_team1_player, mx_team1_partner, mx_team2_player, mx_team2_partner,            
            mx_game1_team1_score, mx_game1_team2_score, 
            mx_game2_team1_score, mx_game2_team2_score, 
            mx_game3_team1_score, mx_game3_team2_score,
            mx_annulled_score,
            mx_game1_team1_orig_score, mx_game1_team2_orig_score, 
            mx_game2_team1_orig_score, mx_game2_team2_orig_score, 
            mx_game3_team1_orig_score, mx_game3_team2_orig_score;
        IF NOT done THEN
            IF NOT score_available THEN
                # wenn kein Ergebnis vorliegt [(team1_score = 0 AND team2_score = 0) funktionierte nicht, da es sogar vorkommen kann]
                INSERT INTO `teammatches` (`venue_id`, `scheduled_at`, `performed_at`, `stage`, `team1_id`, `team2_id`, `team1_score`, `team2_score`, `no_fight`, `revaluation_wrongdoer`, `revaluation_reason`, `description`)
                    VALUES (venue_id, performed_at, performed_at, stage, team1_id, team2_id, NULL, NULL, no_fight, revaluation_wrongdoer, revaluation_reason, errormessage);
            ELSE
                INSERT INTO `teammatches` (`venue_id`, `scheduled_at`, `performed_at`, `stage`, `team1_id`, `team2_id`, `team1_score`, `team2_score`, `no_fight`, `revaluation_wrongdoer`, `revaluation_reason`, `description`,
					`submitted_by_id`, `confirmed_by_id`, `approved_by_id`)
                    VALUES (venue_id, performed_at, performed_at, stage, team1_id, team2_id, team1_score, team2_score, no_fight, revaluation_wrongdoer, revaluation_reason, errormessage,
						2, 2, 1);
                
                SELECT LAST_INSERT_ID() INTO last_teammatch_id;
                
                #####################
                # 1. Disziplin (1.HE)
                INSERT INTO `matches` (`teammatch_id`, `match_type_id`, `team1_player_id`, `team1_partner_id`, `team2_player_id`, `team2_partner_id`, `revised_score`) 
                    VALUES (last_teammatch_id, 6, ms1_team1_player, ms1_team1_partner, ms1_team2_player, ms1_team2_partner, ms1_annulled_score);
                
                SELECT LAST_INSERT_ID() INTO last_match_id;
                
                CALL migrate_games(
                    last_match_id,
                    ms1_game1_team1_score, ms1_game1_team2_score, 
                    ms1_game2_team1_score, ms1_game2_team2_score, 
                    ms1_game3_team1_score, ms1_game3_team2_score,
                    ms1_game1_team1_orig_score, ms1_game1_team2_orig_score, 
                    ms1_game2_team1_orig_score, ms1_game2_team2_orig_score, 
                    ms1_game3_team1_orig_score, ms1_game3_team2_orig_score
                );
                
                #####################
                # 2. Disziplin (2.HE)
                INSERT INTO `matches` (`teammatch_id`, `match_type_id`, `team1_player_id`, `team1_partner_id`, `team2_player_id`, `team2_partner_id`, `revised_score`)  
                    VALUES (last_teammatch_id, 7, ms2_team1_player, ms2_team1_partner, ms2_team2_player, ms2_team2_partner, ms2_annulled_score);
                
                SELECT LAST_INSERT_ID() INTO last_match_id;
                
                CALL migrate_games(
                    last_match_id,
                    ms2_game1_team1_score, ms2_game1_team2_score, 
                    ms2_game2_team1_score, ms2_game2_team2_score, 
                    ms2_game3_team1_score, ms2_game3_team2_score,
                    ms2_game1_team1_orig_score, ms2_game1_team2_orig_score, 
                    ms2_game2_team1_orig_score, ms2_game2_team2_orig_score, 
                    ms2_game3_team1_orig_score, ms2_game3_team2_orig_score
                );
                
                #####################
                # 3. Disziplin (3.HE)
                INSERT INTO `matches` (`teammatch_id`, `match_type_id`, `team1_player_id`, `team1_partner_id`, `team2_player_id`, `team2_partner_id`, `revised_score`)  
                    VALUES (last_teammatch_id, 8, ms3_team1_player, ms3_team1_partner, ms3_team2_player, ms3_team2_partner, ms3_annulled_score);
                
                SELECT LAST_INSERT_ID() INTO last_match_id;
                
                CALL migrate_games(
                    last_match_id,
                    ms3_game1_team1_score, ms3_game1_team2_score, 
                    ms3_game2_team1_score, ms3_game2_team2_score, 
                    ms3_game3_team1_score, ms3_game3_team2_score,
                    ms3_game1_team1_orig_score, ms3_game1_team2_orig_score, 
                    ms3_game2_team1_orig_score, ms3_game2_team2_orig_score, 
                    ms3_game3_team1_orig_score, ms3_game3_team2_orig_score
                );
                
                #####################
                # 4. Disziplin (DE)
                INSERT INTO `matches` (`teammatch_id`, `match_type_id`, `team1_player_id`, `team1_partner_id`, `team2_player_id`, `team2_partner_id`, `revised_score`)  
                    VALUES (last_teammatch_id, 4, ws_team1_player, ws_team1_partner, ws_team2_player, ws_team2_partner, ws_annulled_score);
                
                SELECT LAST_INSERT_ID() INTO last_match_id;
                
                CALL migrate_games(
                    last_match_id,
                    ws_game1_team1_score, ws_game1_team2_score, 
                    ws_game2_team1_score, ws_game2_team2_score, 
                    ws_game3_team1_score, ws_game3_team2_score,
                    ws_game1_team1_orig_score, ws_game1_team2_orig_score, 
                    ws_game2_team1_orig_score, ws_game2_team2_orig_score, 
                    ws_game3_team1_orig_score, ws_game3_team2_orig_score
                );
                
                #####################
                # 5. Disziplin (1.HD)
                INSERT INTO `matches` (`teammatch_id`, `match_type_id`, `team1_player_id`, `team1_partner_id`, `team2_player_id`, `team2_partner_id`, `revised_score`)  
                    VALUES (last_teammatch_id, 1, md1_team1_player, md1_team1_partner, md1_team2_player, md1_team2_partner, md1_annulled_score);
                
                SELECT LAST_INSERT_ID() INTO last_match_id;
                
                CALL migrate_games(
                    last_match_id,
                    md1_game1_team1_score, md1_game1_team2_score, 
                    md1_game2_team1_score, md1_game2_team2_score, 
                    md1_game3_team1_score, md1_game3_team2_score,
                    md1_game1_team1_orig_score, md1_game1_team2_orig_score, 
                    md1_game2_team1_orig_score, md1_game2_team2_orig_score, 
                    md1_game3_team1_orig_score, md1_game3_team2_orig_score
                );
                
                #####################
                # 6. Disziplin (2.HD)
                INSERT INTO `matches` (`teammatch_id`, `match_type_id`, `team1_player_id`, `team1_partner_id`, `team2_player_id`, `team2_partner_id`, `revised_score`)  
                    VALUES (last_teammatch_id, 3, md2_team1_player, md2_team1_partner, md2_team2_player, md2_team2_partner, md2_annulled_score);
                
                SELECT LAST_INSERT_ID() INTO last_match_id;
                
                CALL migrate_games(
                    last_match_id,
                    md2_game1_team1_score, md2_game1_team2_score, 
                    md2_game2_team1_score, md2_game2_team2_score, 
                    md2_game3_team1_score, md2_game3_team2_score,
                    md2_game1_team1_orig_score, md2_game1_team2_orig_score, 
                    md2_game2_team1_orig_score, md2_game2_team2_orig_score, 
                    md2_game3_team1_orig_score, md2_game3_team2_orig_score
                );
                
                 #####################
                # 7. Disziplin (DD)
                INSERT INTO `matches` (`teammatch_id`, `match_type_id`, `team1_player_id`, `team1_partner_id`, `team2_player_id`, `team2_partner_id`, `revised_score`)  
                    VALUES (last_teammatch_id, 2, wd_team1_player, wd_team1_partner, wd_team2_player, wd_team2_partner, wd_annulled_score);
                
                SELECT LAST_INSERT_ID() INTO last_match_id;
                
                CALL migrate_games(
                    last_match_id,
                    wd_game1_team1_score, wd_game1_team2_score, 
                    wd_game2_team1_score, wd_game2_team2_score, 
                    wd_game3_team1_score, wd_game3_team2_score,
                    wd_game1_team1_orig_score, wd_game1_team2_orig_score, 
                    wd_game2_team1_orig_score, wd_game2_team2_orig_score, 
                    wd_game3_team1_orig_score, wd_game3_team2_orig_score
                );
                
                #####################
                # 8. Disziplin (Mix)
                INSERT INTO `matches` (`teammatch_id`, `match_type_id`, `team1_player_id`, `team1_partner_id`, `team2_player_id`, `team2_partner_id`, `revised_score`)  
                    VALUES (last_teammatch_id, 5, mx_team1_player, mx_team1_partner, mx_team2_player, mx_team2_partner, mx_annulled_score);
                
                SELECT LAST_INSERT_ID() INTO last_match_id;
                
                CALL migrate_games(
                    last_match_id,
                    mx_game1_team1_score, mx_game1_team2_score, 
                    mx_game2_team1_score, mx_game2_team2_score, 
                    mx_game3_team1_score, mx_game3_team2_score,
                    mx_game1_team1_orig_score, mx_game1_team2_orig_score, 
                    mx_game2_team1_orig_score, mx_game2_team2_orig_score, 
                    mx_game3_team1_orig_score, mx_game3_team2_orig_score
                );
                
            END IF;
        END IF;
    UNTIL done END REPEAT;

    CLOSE matches_cursor;
    
END
//

DELIMITER ;


### SQL-Script-Anfang

SET CHARACTER SET utf8;         # notwendig, um die user variable richtig zu interpretieren
SET @tournament = 1;

# schnellerer Import
SET unique_checks = 0;
SET foreign_key_checks = 0;
# SET SESSION sql_mode = 'TRADITIONAL'; # data integrity: give an error instead of a warning when inserting an incorrect value into a column


# Konvertierung von latin1 zu utf8 funktioniert automatisch beim Einfügen
# Daher nicht notwendig:
# ALTER TABLE `M-Spieltermine-Stamm` CONVERT TO CHARACTER SET utf8;


###
# Folgende Abfragen koennen fuer jede Saison wiederholt werden
# Sie aktualisieren die saisonunabhaengigen Stammdaten bzw. fuegen neue Datensaetze ein
###   


# Es muss dafür gesorgt werden, dass die Stammdaten auf dem aktuellen Stand gebracht werden ohne dabei zwischenzeitlich gelöschte Stammdaten zu entfernt, 
# da alte Ergebnisse noch auf diese verweisen könnten. Die Wahrung dieser Integrität wurde mittels 
# des SQL-Konstrukts „INSERT INTO … ON DUPLICATE KEY UPDATE“ und „REPLACE INTO …“ erreicht.

REPLACE INTO `athletes` (`id`, `last_name`, `first_name`, `gender`, `birthday`, `country`, 
        `club_id`, `is_active`, `zip`, `city`, `street`, 
        `created_at`, `updated_at`) 
    SELECT `Mit-Pass-Nr`, TRIM(`Mit-Name`), TRIM(`Mit-Vorname`), IF(`Mit-Geschlecht` = '2', 'male', 'female'), `Mit-Geburtsdatum`, `Mit-Staats-Nr` , 
        `Mit-Vereins-Nr`, IF(`Mit-aktiv` = '2', 0, 1), `Mit-PLZ`, `Mit-Stadt`, IFNULL(`Mit-Straße-Nr`, ''), 
        `Mit-Aufnahmedatum`, `Mit-Änderungsdatum`
    FROM `Mitglieder-Stamm`;


# REPLACE reallocates new AUTO_INCREMENT because it executes DELETE followed by INSERT
# So we use INSERT INTO ... SELECT ... ON DUPLICATE KEY UPDATE ...
INSERT INTO `venues` (`code`, `zip`, `city`, `street`, `description`)
    SELECT `Hallen-Key`, `Hallen-PLZ`, 
            TRIM(REPLACE(REPLACE(`Hallen-Bezirk`, '(', ''), ')', '')), 
            TRIM(REPLACE(`Hallen-Straße`, '|', ' ')), 
            CONCAT_WS('<br />', 
                TRIM(REPLACE(`Hallen-Zeile2`, '|', ' ')), 
                TRIM(REPLACE(`Hallen-Zeile3`, '|', ' ')), 
                TRIM(REPLACE(`Hallen-Zeile4`, '|', ' ')), 
                TRIM(REPLACE(`Hallen-Zeile5`, '|', ' '))
            )
    FROM `Hallen-Stamm`
    ON DUPLICATE KEY UPDATE 
        `zip` = VALUES(`zip`),
        `city` = VALUES(`city`),
        `street` = VALUES(`street`),
        `description` = VALUES(`description`);


REPLACE INTO `clubs` (`id`, `code`, `name`, `contact_person_id`, `club_syndicate_1`, `club_syndicate_2`, `club_syndicate_3`, `club_syndicate_4`) 
    SELECT `Verein-Key`, `Verein-kurz-Bez`, `Verein-Name`, `Verein-1-Verantw`, `Vereine-zur-Bg-1`, `Vereine-zur-Bg-2`, `Vereine-zur-Bg-3`, `Vereine-zur-Bg-4`
    FROM `Verein-Stamm`;


###
# Folgende Abfragen nur 1-Mal pro Saison ausführen
# Oder vorher alle games, matches, teammatches, lineups, teams, leagues der Saison entfernen
###    
    
CALL migrate_leagues(@tournament);  
    
# Ersatzspielerliga dieser Saison erzeugen, in der die Ersatzspielermannschaften aller Vereine vereint sind
INSERT INTO `leagues` (`tournament_id`, `class_abbr`, `class_name`, `division`, `class_level`, `promoted_number`, `relegated_number`) 
    VALUES (@tournament, 'Ersatz', 'Ersatzspielerliga', '0', '255', '0', '0');

SELECT LAST_INSERT_ID() INTO @substitute_league_id;


INSERT INTO `teams` (`club_id`, `team_number`, `league_id`, `withdrawn`) 
    SELECT `Vereins-Nr`, `Mannschaft`, `leagues`.`id`, IF(`zurück-gezogen` = 'J', 1, 0)
    FROM `M-Spieltermine-Stamm`
    INNER JOIN `leagues` ON (`M-Spieltermine-Stamm`.`Klasse` = `leagues`.`class_abbr` AND `M-Spieltermine-Stamm`.`Staffel` = `leagues`.`division`) 
    WHERE `leagues`.`tournament_id` = @tournament;
    
# fuer jeden Verein eine Ersatzspielermannschaft in der Ersatzmannschaftsliga erzeugen
INSERT INTO `teams` (`club_id`, `team_number`, `league_id`) 
    SELECT t.`club_id`, MAX(t.`team_number`) + 1, @substitute_league_id
    FROM `teams` AS t
    INNER JOIN `leagues` AS l ON t.`league_id` = l.`id`
    WHERE l.`tournament_id` = @tournament
    GROUP BY t.`club_id`;

    
# Migration und Konsolidierung von lineups, teammatches, matches, games in stored procedure ausgelagert
CALL migrate_lineups(@tournament); 
CALL migrate_matches(@tournament);    

DROP TABLE `hallen-stamm`, `m-aufstellungen`, `m-klasse-staffel`, `m-spieler-stamm`, `m-spieltermine`, `m-spieltermine-stamm`, `mitglieder-stamm`, `verein-stamm`;


###
# Folgende Abfragen sollte einmal am Ende der Konsolidierung ausgeführt werden
# Aber es schadet nicht, sie mehrmals hintereinandner durchzuführen 
###

// remove line breaks
UPDATE athletes a
	SET first_name = SUBSTRING_INDEX(first_name, '\r\n', 1), 
		last_name = SUBSTRING_INDEX(last_name, '\r\n', 1)
	WHERE LOCATE('\r\n', first_name) > 0 OR LOCATE('\r\n', last_name) > 0;
	
CALL consolidate_athletes();

UPDATE leagues l 
    INNER JOIN league_classes AS lc ON l.class_abbr = lc.class_abbr
    SET l.class_name = lc.class_name
	WHERE l.class_name = '';
	

# Spiele von zurueckgezogenen Mannschaften annullieren
UPDATE teammatches tm 
    INNER JOIN teams t1 ON tm.team1_id = t1.id
    INNER JOIN teams t2 ON tm.team2_id = t2.id
    SET annulled = 1
    WHERE t1.withdrawn = 1 OR t2.withdrawn = 1;
  
 
/*
SELECT * FROM matches WHERE revised_score AND team1_original_score IS NULL
> 0

SELECT * FROM matches WHERE revised_score AND no_fight and team1_score is null
> 0

SELECT * FROM `matches` WHERE no_fight AND revised_score AND team1_original_score IS NOT NULL
> 0

SELECT * FROM `matches` WHERE NOT no_fight AND revised_score AND team1_original_score IS NULL
= 0

# angeblich geändert, aber scheinbar echtes Spielergebnis vorhanden
SELECT * FROM `matches` WHERE revised_score AND team1_points > 0 AND team2_points > 0 IS NULL
= 0

# nicht geändert, aber usprüngliches vorhanden
SELECT * FROM `matches` WHERE NOT revised_score AND team1_original_score IS NOT NULL
= 0
  
 SELECT * FROM `matches` WHERE 
	team1_player_id IS NULL AND team2_player_id IS NULL AND team1_partner_id IS NULL AND team2_partner_id IS NULL AND
	team1_original_score IS NOT NULL
= 2 (falsches Ergebnisformat)

*/
    
    
# haufig gebrauchte Auswertungen (Anzahl gewonnener Saetze und Spielpunkte), 
# die sich aus den Games ergeben, aus Performance Gruenden in der Matches-Tabelle aufnehmen   

# Setze Flag (no_fight) für kampflose Spiele, aber nur wenn keine Ergebnisaenderung vorliegt
# - kein Gegner (dadurch werden auch alle Matches, die Teil eines kampflosen Teammatches sind, kampflos gemacht, da dort keine gegn. Spieler angetreten sind)
# - Match mit 2 Saetzen, die jeweils zu 0 gewonnen wurden (21:0 und nach alter Regel 15:0 bzw. 11:0) -> scheinbar kampflos
# - no_fight nur setzen, wenn das Originalergebnis kampflos war und nicht bei Ergebnisaenderung,
#   da ein revidiertes Ergebnis zwar kampflos zustande gekommen ist (abfragbar über revised_score), 
#   aber man sonst im Umkehrschluss nicht mehr nachvollziehen kann, ob das Originalergebnis schon kampflos war.
#   Bei Ergebnisaenderung ohne Originalergebnis (ein paar solcher Daten liegen vor)
/*
team1_score IS NULL AND team2_score IS NULL
wenn kein Ergebnis vorliegt (noch nicht gespielt oder eingetragen), was hier in diesem Fall nicht vorkommt
oder wenn match keine games bzw. nur annulled games hat, also ein Spiel ohne Wertung
--> Abfrage mit
SELECT * FROM matches m
LEFT JOIN games g ON m.id = g.match_id AND g.annulled = 0
GROUP BY m.id
HAVING COUNT(g.id) = 0
*/   

CREATE TEMPORARY TABLE match_statistics (
    match_id INT UNSIGNED NOT NULL PRIMARY KEY, 
    count_games TINYINT UNSIGNED NOT NULL,
    team1_games_won TINYINT UNSIGNED NOT NULL,
    team2_games_won TINYINT UNSIGNED NOT NULL,
    team1_score_sum SMALLINT UNSIGNED NOT NULL,
    team2_score_sum SMALLINT UNSIGNED NOT NULL,
    count_annulled_games TINYINT UNSIGNED NOT NULL,
    team1_annulled_games_won TINYINT UNSIGNED NOT NULL,
    team2_annulled_games_won TINYINT UNSIGNED NOT NULL,
    team1_annulled_score_sum SMALLINT UNSIGNED NOT NULL,
    team2_annulled_score_sum SMALLINT UNSIGNED NOT NULL,
	smaller_of_bigger_score_permil_avg SMALLINT UNSIGNED NULL, # SMALLINT permil ; DECIMAL(3,1) original_smaller_to_bigger_score_ratio_avg
	smaller_of_bigger_score_permil_std SMALLINT UNSIGNED NULL,
	annulled_smaller_of_bigger_score_permil_avg SMALLINT UNSIGNED NULL, # SMALLINT permil ; DECIMAL(3,1) original_smaller_to_bigger_score_ratio_avg
	annulled_smaller_of_bigger_score_permil_std SMALLINT UNSIGNED NULL
);

INSERT INTO match_statistics
    SELECT 
		match_id,
        SUM(IF(NOT annulled, 1, 0)) AS count_games,
		SUM(IF(NOT annulled AND team1_score > team2_score, 1, 0)) AS team1_games_won,
		SUM(IF(NOT annulled AND team1_score < team2_score, 1, 0)) AS team2_games_won,
        SUM(IF(NOT annulled, team1_score, 0)) AS team1_score_sum, 
		SUM(IF(NOT annulled, team2_score, 0)) AS team2_score_sum,
        SUM(IF(annulled, 1, 0)) AS count_annulled_games,
        SUM(IF(annulled AND team1_score > team2_score, 1, 0)) AS team1_annulled_games_won,
		SUM(IF(annulled AND team1_score < team2_score, 1, 0)) AS team2_annulled_games_won,
        SUM(IF(annulled, team1_score, 0)) AS team1_annulled_score_sum, 
		SUM(IF(annulled, team2_score, 0)) AS team2_annulled_score_sum,
		ROUND(AVG(IF(NOT annulled, 
			IF(team1_score > team2_score, team2_score / team1_score, team1_score / team2_score), 
			NULL
		)) * 1000) AS smaller_of_bigger_score_permil_avg,
		ROUND(STDDEV_POP(IF(NOT annulled, 
			IF(team1_score > team2_score, team2_score / team1_score, team1_score / team2_score), 
			NULL
		)) * 1000) AS smaller_of_bigger_score_permil_std,
		ROUND(AVG(IF(annulled, 
			IF(team1_score > team2_score, team2_score / team1_score, team1_score / team2_score), 
			NULL
		)) * 1000) AS annulled_smaller_of_bigger_score_permil_avg,
		ROUND(STDDEV_POP(IF(annulled, 
			IF(team1_score > team2_score, team2_score / team1_score, team1_score / team2_score), 
			NULL
		)) * 1000) AS annulled_smaller_of_bigger_score_permil_std
	FROM games
	GROUP BY match_id;

UPDATE matches m 
/*
-- LEFT JOIN mit derived table im Gegensatz zum INNER JOIN extrem langsam (mehrere Minuten) - nicht nachvollziehbarer Weise
-- daher umgeschrieben in temporary table mit entsprechendem primary key, sodass die semantisch gleiche Abfrage nur noch unter 1 Sekunde dauert
LEFT JOIN (
    SELECT 
		match_id,
        SUM(IF(NOT annulled, 1, 0)) AS count_games,
		SUM(IF(NOT annulled AND team1_score > team2_score, 1, 0)) AS team1_games_won,
		SUM(IF(NOT annulled AND team1_score < team2_score, 1, 0)) AS team2_games_won,
        SUM(IF(NOT annulled, team1_score, 0)) AS team1_score_sum, 
		SUM(IF(NOT annulled, team2_score, 0)) AS team2_score_sum,
        SUM(IF(annulled, 1, 0)) AS count_annulled_games,
        SUM(IF(annulled AND team1_score > team2_score, 1, 0)) AS team1_annulled_games_won,
		SUM(IF(annulled AND team1_score < team2_score, 1, 0)) AS team2_annulled_games_won,
        SUM(IF(annulled, team1_score, 0)) AS team1_annulled_score_sum, 
		SUM(IF(annulled, team2_score, 0)) AS team2_annulled_score_sum
	FROM games
	GROUP BY match_id 
) g ON m.id = g.match_id
*/
LEFT JOIN match_statistics g ON m.id = g.match_id
SET 
    m.no_fight = IFNULL(( 
        m.team1_player_id IS NULL OR
        m.team2_player_id IS NULL OR
        g.match_id IS NULL OR -- Spiel ohne einen einzigen Satz (Individualspiele werden vom BVBB erst aufgenommen, wenn ein Detailergebnis vorliegt)
        ( ( NOT m.revised_score OR ( m.revised_score AND g.count_annulled_games = 0 ) ) AND (
            ( g.team1_score_sum = 30   AND g.team2_score_sum = 0  ) OR
            ( g.team1_score_sum = 0    AND g.team2_score_sum = 30 ) OR
            ( g.team1_score_sum = 22   AND g.team2_score_sum = 0  ) OR
            ( g.team1_score_sum = 0    AND g.team2_score_sum = 22 ) OR
            ( g.team1_score_sum = 42   AND g.team2_score_sum = 0  ) OR
            ( g.team1_score_sum = 0    AND g.team2_score_sum = 42 )
        )) OR
        ( g.count_annulled_games > 0 AND (
            ( g.team1_annulled_score_sum = 30   AND g.team2_annulled_score_sum = 0  ) OR
            ( g.team1_annulled_score_sum = 0    AND g.team2_annulled_score_sum = 30 ) OR
            ( g.team1_annulled_score_sum = 22   AND g.team2_annulled_score_sum = 0  ) OR
            ( g.team1_annulled_score_sum = 0    AND g.team2_annulled_score_sum = 22 ) OR
            ( g.team1_annulled_score_sum = 42   AND g.team2_annulled_score_sum = 0  ) OR
            ( g.team1_annulled_score_sum = 0    AND g.team2_annulled_score_sum = 42 )
        )) 
    ), 0),
    m.team1_score = IF(g.count_games = 0, NULL, g.team1_games_won), -- Spiele mit nur annullierten Saetzen oder gar keinem Satz -> score = NULL
    m.team2_score = IF(g.count_games = 0, NULL, g.team2_games_won),
    m.team1_points = IFNULL(g.team1_score_sum, 0),
    m.team2_points = IFNULL(g.team2_score_sum, 0),
    m.revised_score = IFNULL(m.revised_score OR g.count_annulled_games > 0, 0), -- nur für migration möglich, da bvbb kein given_up_by führt und daher auch kein ursprüngliches ergebnis zum zeitpunkt der Aufgabe hat
    m.team1_original_score = IF(g.count_annulled_games = 0, NULL, g.team1_annulled_games_won),
    m.team2_original_score = IF(g.count_annulled_games = 0, NULL, g.team2_annulled_games_won),
	m.team1_original_points = IF(g.count_annulled_games = 0, NULL, g.team1_annulled_score_sum),
    m.team2_original_points = IF(g.count_annulled_games = 0, NULL, g.team2_annulled_score_sum),
	m.avg_orig_smaller_div_bigger_gamescore_permil = IFNULL(g.annulled_smaller_of_bigger_score_permil_avg, g.smaller_of_bigger_score_permil_avg),
    m.std_orig_smaller_div_bigger_gamescore_permil = IFNULL(g.annulled_smaller_of_bigger_score_permil_std, g.smaller_of_bigger_score_permil_std);
 

# Revidierung / Ergebnisänderung / Umwertung überprüfen
# Es wurden vom BVBB fälschlicherweise auch Spiele als geändert markiert, die wegen Spielermangels kampflos abgegeben wurden oder von vorn herein gar nicht in die Wertung einfließen (kein Spieler, Score von beiden NULL)
# Besser geeignet wäre ein neues Flag: unvollständiges Mannschaftsspiel -> incomplete_lineup
# Falsche Kennzeichnung:
# - http://sportservice.tobiserver/league/2005-06/CK/2/BSC-4_GutsM-5#match-17784 (Mix)
# - http://sportservice.tobiserver/league/2005-06/CK/2/MSC-3_BC+Spand-8#match-20399 (DD)


# wenige betroffen
UPDATE matches
SET revised_score = 0
WHERE
	revised_score AND
	team1_player_id IS NULL AND team2_player_id IS NULL AND
	team1_score IS NULL AND team2_score IS NULL AND
	team1_original_score IS NULL AND team2_original_score IS NULL;

# > 1100 betroffen

UPDATE matches
SET revised_score = 0
WHERE
	revised_score AND
	(
		(team1_player_id IS NULL AND team2_player_id IS NOT NULL AND team1_score = 0)
		OR
		(team1_player_id IS NOT NULL AND team2_player_id IS NULL AND team2_score = 0)
	) AND
	team1_original_score IS NULL AND team2_original_score IS NULL;


# revaluation_wrongdoer
UPDATE matches
SET revaluation_wrongdoer = CASE
	WHEN revised_score AND team1_score = 0 AND team2_score = 0 THEN 3
	WHEN revised_score AND team1_score = 0 THEN 1
	WHEN revised_score AND team2_score = 0 THEN 2
	WHEN revised_score AND team1_score IS NULL AND team2_score IS NULL
		AND team1_player_id IS NOT NULL AND team2_player_id IS NOT NULL THEN 3
	WHEN revised_score AND team1_score IS NULL AND team2_score IS NULL
		AND team1_player_id IS NOT NULL AND team2_player_id IS NULL THEN 1
	WHEN revised_score AND team1_score IS NULL AND team2_score IS NULL
		AND team1_player_id IS NULL AND team2_player_id IS NOT NULL THEN 2
	ELSE 0
END;



UPDATE teammatches tm 
LEFT JOIN (
	SELECT 
		m.teammatch_id,
		SUM(revised_score) AS number_revised_matches,
		SUM(team1_player_id IS NULL OR team2_player_id IS NULL) AS number_incomplete_matches,
		SUM(m.team1_score) AS team1_won_games,
		SUM(m.team2_score) AS team2_won_games,
		SUM(m.team1_points) AS team1_won_points,
		SUM(m.team2_points) AS team2_won_points
	FROM matches m 
	GROUP BY m.teammatch_id
) mg ON tm.id = mg.teammatch_id  
SET 
	tm.revised_score = IFNULL(number_revised_matches > 0, 0),
	tm.incomplete_lineup = IFNULL(number_incomplete_matches > 0, 0),
	tm.team1_games = mg.team1_won_games,
	tm.team2_games = mg.team2_won_games,
	tm.team1_points = mg.team1_won_points,
	tm.team2_points = mg.team2_won_points;


###
# Abgebrochene/aufgegebene Spiele herausfinden (z.B. durch Verletzung eines Spielers)
# Schon vor Beginn des Spiels zurückgezogene Spiele sind schon durch no_fight abgedeckt
# Indizien:
# - 3. Satz zu 0 (oder sehr wenig Punkten, wenn 1. und 2. Satz recht knapp war)
# - 2. Satz zu 0 (wenn 1. Satz > 0 [sonst kampfloses 21-0, 21-0 dabei] und kein 3. Satz vorhanden [sonst 21-16, 0-21, 21-19 dabei]) 
# (oder sehr wenig Punkten, wenn 1. Satz recht knapp war)
###   
UPDATE matches m
INNER JOIN games g ON m.id = g.match_id
SET m.given_up_by = IF(g.team1_score > g.team2_score, 2, 1)
WHERE g.game_sequence = 3
	AND (g.team1_score = 0 OR g.team2_score = 0)


SELECT *
FROM games g1
WHERE g1.game_sequence = 3
AND (
	IF(g1.team1_score < g1.team2_score, g1.team1_score, g1.team2_score) / 
	IF(g1.team1_score > g1.team2_score, g1.team1_score, g1.team2_score) 
	< 0.15 
)

SELECT *
FROM games g1
WHERE g1.game_sequence = 3
AND (
	IF(g1.team1_score > g1.team2_score, 
		IF(g1.team1_score < 21, g1.team2_score = 0, g1.team2_score <= 3), 
		IF(g1.team2_score < 21, g1.team1_score = 0, g1.team1_score <= 3)
	)
)
		
SELECT *
FROM games g1
JOIN games g2 ON g1.match_id = g2.match_id
WHERE g1.annulled = g2.annulled 
AND g1.game_sequence = 1
AND (
	IF(g1.team1_score < g1.team2_score, g1.team1_score, g1.team2_score) / 
	IF(g1.team1_score > g1.team2_score, g1.team1_score, g1.team2_score) 
	> 0.60 
)
AND g2.game_sequence = 2
AND (
	IF(g2.team1_score < g2.team2_score, g2.team1_score, g2.team2_score) / 
	IF(g2.team1_score > g2.team2_score, g2.team1_score, g2.team2_score) 
	< 0.15 
)
AND NOT EXISTS (SELECT * FROM games g3 
				WHERE g1.match_id = g3.match_id AND g3.game_sequence = 3)
				
< 0.09 # 1 / 15 ; 1 / 21
< 0.15 # <= 3 / 21 ; 2 / 15 ; 1 / 11
> 0.60 # >= 7 / 11 ; 10 / 15 ; 13 / 21
-> zu viele falsch positive (1-11) bei Damenspielen (vor allem Damendoppel) bis 11

# Da die "wahre" Klassenzugehörigkeit der Spiele nicht bekannt ist und so eine Bewertung des Tests an sich nicht möglich ist, handelt es sich hierbei nicht um eine Ja-Nein-Klassifikation sondern um
einen statistischen Test (http://de.wikipedia.org/wiki/Statistischer_Test)
# Nullhypothese = Spielergebnis nicht durch Abbruch zustande gekommen
# Alternativhypothese = Spiel wurde abgebrochen
-> konservativ Parametrisieren, um möglichst kaum falsch positive treffer/Fehler erster Art (Spiel war kein Abbruch, aber Test hat ihn fäschlicherweise als Abbruch eingestuft) zu erhalten (und dafür ein paar falsch negative/Fehler zweiter Art in Kauf nehmen)
-> nur 15-0 und 11-0 als Indiz, da Ergebnisse wie 11-6 9-11 11-1 relativ oft vorkommen und scheinbar kein Anzeichen eines Abbruchs sind
21-3 und weniger im 3. Satz sind ungewöhnlich aber scheinbar oft auch falsch positiv und außerdem sind sie relativ selten im Vergleich zu 21-0 (21-1 kommt gar nicht vor) -> auch nicht aufnehmen
durch die neue Zählweise sind aber eindeutige Ergebnisse wie 21-13 21-1 schon ein Indiz
Weiteres sehr signifikantes Indiz sind kampflosen Folgespielen (DE, Mix) dieser Spielerinnen


Example:
SELECT *, COUNT(DISTINCT(g2.id))
FROM games g1
JOIN games g2 ON g1.match_id = g2.match_id
where g1.match_id = 6 # 5
group by g1.match_id



DROP TABLE `errormessages`, `league_classes`;


    
/*
Sonderfaelle:

- unvollständige Daten !
http://sportservice.tobiserver/league/2005-06/CK/1/
SELECT * FROM teammatches WHERE (  team1_score IS NULL OR team2_score IS NULL ) AND NOT annulled AND scheduled_at < NOW()

- Match ohne Beteiligung aber mit Umwertung
http://bvbb.net/fileadmin/user_upload/saison0809/meisterschaft/spielberichte-vereine/33-02_31-04.HTML

- Match mit kampflos gewonnenem Spiel, und dann Umwertung nach kein Ergebnis
http://bvbb.net/fileadmin/user_upload/saison0607/meisterschaft/spielberichte-vereine/92-09_20-03.HTML
http://sportservice.tobiserver/league_dev.php/league/2006-07/CK/3/BG%20ProZ-9_MSC-3

- Mannschaft nicht angetreten, Siegerspieler IS NULL (kampflos gewonnen)
http://www.bvbb.net/fileadmin/user_upload/saison0809/meisterschaft/spielberichte-vereine/13-05_18-04.HTML
http://sportservice.tobiserver/league_dev.php/league/2008-09/CK/2/GutsM-5_TusLi-4
(da wo Teammatch Spiel-kampflos = nicht-angetreten = 1, Games loeschen mit annulled = 1)
--> mit annulled games, im Gegensatz zu dieser nicht angetretenen Mannschaft (INKONSISTENZ)
http://www.bvbb.net/fileadmin/user_upload/saison0708/meisterschaft/spielberichte-vereine/65-02_93-02.HTML
http://sportservice.tobiserver/league_dev.php/league/2007-08/BZ/3/Gaselan-2_TSVSCSBF-2
    
- Teammatch, das beide Mannschaften verloren haben (z.B. durch beiderseitge falsche Aufstellung)
http://bvbb.net/fileadmin/user_upload/saison0607/meisterschaft/spielberichte-vereine/40-02_43-05.HTML
http://sportservice.tobiserver/league_dev.php/league/2006-07/LL/2/BerlBrau-2_EBT-5
http://sportservice.tobiserver/league_dev.php/league/2005-06/CK/1/Rotation-2_Dresd-4
SELECT * FROM `teammatches` where team1_score = 0 and team2_score = 0 

- Geaenderte Ergebnisse, bei denen entsprechendes Flag nicht gesetzt ist
http://sportservice.tobiserver/league_dev.php/league/2009-10/BB/0/BerlBrau-2_BG%20ProZ-1
http://sportservice.tobiserver/league_dev.php/league/2005-06/CK/1/Rotation-2_Dresd-4

- Falsche Eingabe:
http://bvbb.net/fileadmin/user_upload/saison0708/meisterschaft/spielberichte-vereine/07-01_46-01.HTML
Bernd Schulz falsch, Tobias Schultze richtig
http://www.bvbb.net/fileadmin/user_upload/schuch/meisterschaft/spielberichte-vereine/91-04_46-02.HTML
Anika Fuchs falsch, Anika Götze richtig

- Für dieses Spiel ist ursprüngliches Ergebnis vorhanden, aber Ergebnis-geändert nicht gesetzt: (inkonsitente Ausnahme?)
http://www.bvbb.net/fileadmin/user_upload/saison0910/meisterschaft/spielberichte-vereine/40-02_92-01.HTML
http://sportservice.tobiserver/league_dev.php/league/2009-10/BB/0/BerlBrau-2_BG%20ProZ-1

- Zurückgezogene Mannschaft mit schon absolvierten Spielen
http://bvbb.net/fileadmin/user_upload/saison0809/meisterschaft/tabellen/uebersicht-07.HTML

-Fehlerhafter Eintrag:
http://bvbb.net/fileadmin/user_upload/schuch/meisterschaft/spielberichte-vereine/93-02_46-03.HTML

- Komplette Umwertung, ohne Beschreibung:
http://sportservice.tobiserver/league/2008-09/BK/2/Gaselan-3_Rotation-2
(Steht in den Original--Daten, überprüfe Migration)
 */
    
    
    
    
# TESTS    
    
/*
# Setze Flag für Ergebnisänderung bei matches, die auch annulled games haben
# nur für migration möglich, da bvbb kein withdrawn_by führt und daher auch kein ursprüngliches ergebnis zum zeitpunkt der Aufgabe hat
UPDATE matches m 
INNER JOIN games g ON (g.match_id = m.id AND g.annulled = 1)
SET m.revised_score = 1;



SELECT *, team1_score + team2_score 
FROM `teammatches` 
WHERE team1_score IS NOT NULL AND annulled = 0
order by (team1_score + team2_score) ASC, performed_at ASC



SELECT * FROM clubs WHERE id NOT IN (SELECT club_id FROM teams);
SELECT * FROM athletes WHERE id NOT IN (SELECT DISTINCT `Mit-Pass-Nr` FROM `mitglieder-stamm`);

SELECT * FROM athletes AS a
LEFT JOIN `mitglieder-stamm` AS m ON a.id = m.`Mit-Pass-Nr`
WHERE m.`Mit-Pass-Nr` IS NULL 

SELECT *
FROM teams AS t
INNER JOIN leagues AS l ON t.league_id = l.id
WHERE l.level <> 255



*/    

/*    
    SELECT l.`id`, s1.`Vereins-Nr`, s1.`Mannschaft`, t1.`id`, s2.`Vereins-Nr`, s2.`Mannschaft`, t2.`id`,
        (s1.`1-Spiele` + s1.`2-Spiele` + s1.`3-Spiele` + s1.`4-Spiele` + s1.`5-Spiele` + s1.`6-Spiele` + s1.`7-Spiele` + s1.`8-Spiele`) AS score1,
        (s2.`1-Spiele` + s2.`2-Spiele` + s2.`3-Spiele` + s2.`4-Spiele` + s2.`5-Spiele` + s2.`6-Spiele` + s2.`7-Spiele` + s2.`8-Spiele`) AS score2
    FROM `M-Spieltermine` AS s1
    INNER JOIN `locations` AS l ON (s1.`Halle` = l.`code`) 
    INNER JOIN `M-Spieltermine` AS s2 ON (
        s1.`Vereins-Nr-Gast` = s2.`Vereins-Nr` AND 
        s1.`Mannschaft-Gast` = s2.`Mannschaft` AND
        s1.`Datum` = s2.`Datum` AND 
        s1.`Zeit` = s2.`Zeit`
    )
    INNER JOIN `teams` AS t1 ON (s1.`Vereins-Nr` = t1.`club_id` AND s1.`Mannschaft` = t1.`team_number`) 
    INNER JOIN `teams` AS t2 ON (s2.`Vereins-Nr` = t2.`club_id` AND s2.`Mannschaft` = t2.`team_number`)
    #INNER JOIN `leagues` AS g ON (t1.`league_id` = g.`id`) 
    WHERE s1.`Heim-Ausw` = 1
    
    
 SELECT ADDTIME('2007-12-31 00:00:00', TIME('0000-00-00 15:30'));
*/
   
/*
SELECT *
FROM `m-spieltermine`
WHERE `3-Ergebnisse-geändert` = '*' and `3-ursp-Punkte-1` = `3-Punkte-1`  




SELECT *
    FROM `M-Spieltermine` AS s1
    INNER JOIN `M-Spieltermine` AS s2 ON (
        s1.`Vereins-Nr-Gast` = s2.`Vereins-Nr` AND 
        s1.`Mannschaft-Gast` = s2.`Mannschaft` AND
        s1.`Datum` = s2.`Datum` AND 
        s1.`Zeit` = s2.`Zeit`
    )
    WHERE s1.`Heim-Ausw` = 1 AND s1.`3-Ergebnisse-geändert` = '*' AND s1.`3-ursp-Punkte-1` = 0 AND s2.`3-ursp-Punkte-1` = 0
    

SELECT *
FROM `games`
ORDER BY `annulled` DESC, match_id, game_sequence

SELECT *
FROM matches
INNER JOIN games ON matches.id = games.match_id
WHERE annulled = 1
ORDER BY `annulled` DESC, match_id, game_sequence

*/
   



