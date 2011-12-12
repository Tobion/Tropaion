# man kann sich die Gegner nicht aussuchen und cheaten ist nicht möglich
# daher hier eher FIDE rating (over-the-board, (OTB), play where it is possible to ensure carefully controlled and monitored conditions)
# statt spezielle online rating systeme, die auch Täuschungs- und Manipulationsversuche herausfiltern zu versuchen, wie z.B.
# http://www.chessworld.net./chessclubs/faq_ratingsystem.asp

# Verteilungen des Quotienten aus Verlierpunkte durch Gewinnerpunkte sind sowohl für 2-Satz-Spiele als auch 3-Satz-Spiele normalverteilt. [losers_points_pct_dodge_histogram.png]
# Die beiden Normalverteilungen überlagern sich zum großen Teil, sodass deren Summe sich ebenfalls gut mittels einer Normalverteilung approximieren lässt.
# Dieser Quotient aus Verlierpunkte durch Gewinnerpunkte ist viel genauer als nur 2/3-Satz-Spiel-Unterscheidung und enthält diese Unterscheidung auch in gewissen Maße,
# denn 3 Satz Spiele sind tendenziell knapper entschieden, wie man oben sehen kann, denn es gibt einen Satz, in dem Verlierer mehr Punkte erziehlt hat als der Gewinner.
# Und es kann sogar vorkommen, das der Verlierer mehr Punkte erzielt hat als der Gewinner (siehe Grafik bei Quotient > 100).
# [Da eine Normalverteilung ist invariant gegenüber der Faltung ist (bzw. Satz von Cramér), ist auch die Summe der beiden Variablen wieder normalverteilt.] 
# [losers_points_pct_stacked_histogram.png]
# Für diese gesamte Normalverteilung wurde der Mittelwert und die Standardabweichung berechnet. [Daten]

# home field advantage: http://mlselo.f2f2s.com/MLS_Elo.pdf
# ähnlich wie FIDE ELO, wo Weiß einen Vorteil hat: http://www.chessbase.com/newsdetail.asp?newsid=562
# kein spezielles team rating notwendig, da beim badminton keine mannschaftssportart ist, 
# sondern einfach nur teilweise im form von mannschaftswettkämpfen ausgetragen wird,
# und die stärke eines teams größtensteils von der stärke der beteiligten spieler abhängt und nicht vom zusammenspiel wie beim fußball

# Verbesserung: Performance based rating für die ersten 10 Spiele (+- 100 ELO), da die erste Default Rating nur geschätzt ist und daher unsicher
# wie bei http://www.chessclub.com/help/ratings

# Alternativen: 
# TrueSkill Rating System
# Implementierung in PHP: http://github.com/moserware/PHPSkills
#
# Whole-History Rating: A Bayesian Rating System for Players of Time-Varying Strength
# http://remi.coulom.free.fr/WHR/
# http://www.edochess.ca/
# Whole History is the best approach. 
# As far as I see it also solves a common problem with normal match data that I useally encounter. I mean you are only working with a subset of matches in this contest (between world class players that are densely connected). But useally you have social communities if you analyze matches as a social network. So partially isolated groups due to separation by location, age, sex or skill, i.e. typical league/division building.
# Besides beeing more accurate, this is one key advantage. No other rating system can deal with it, neither incremental (like ELO) nor simulatenous performance (like Chessmetrics).
# "The weakness of algorithms like Glicko and TrueSkill lies in the inaccuracies of representing the probability distribution with just one value and one variance for every player, ignoring covariance."

# 
# Überprüfen des Rating System durch Ermitteln des Fehlers bei der Vorhersage von Ergebnissen
# Fragestellungen: 
# Doppelkobination unterschiedliches Gewicht, d.h. Herrenrating wichtiger als Frauenrating im Mixed?
# Überhaupt unterschiedliche Rating für Einze, Doppel und Mixed führen, oder alles zusammen? -> Wie Gesamtstärke ermitteln?
# Überprüfen der bisherigen Parameter auf Wirksamkeit.
# -------
# Beschreibung: http://www.chessbase.com/newsdetail.asp?newsid=562
# Wettkampf: http://kaggle.com/chess
# New fitness function -- Instead of month-aggregated RMSE, a weighted least squares fitness function would be applied to the results of each pairing:
# http://kaggle.com/view-postlist/forum-18-chess-ratings-can-we-do-better-than-elo/topic-138-suggestions-for-the-next-chess-ratings-contest/task_id-2447#post_text_content_954

/*
Also FYI, Mark Glickman told me that it's much more conventional with chess game outcomes to use a different criteria than RMSE to judge predictability.  RMSE is more appropriate with normally distributed outcomes, but for binary outcomes (or binary with ties, like chess), it's better to use the mean of

-(y*log(E) + (1-y)*log(1-E))

per game (where y is the game outcome (0, 0.5, 1) and E is the expected/predicted score)
*/

CREATE TABLE IF NOT EXISTS `trueskillhistory` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `athlete_id` INT UNSIGNED NOT NULL,
  `discipline` enum('singles','doubles','mixed') NOT NULL,
  `skill_mean` FLOAT UNSIGNED NOT NULL,
  `skill_std` FLOAT UNSIGNED NOT NULL,
  `created_at` DATE NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created_at_index_idx` (`created_at`),
  KEY `athlete_id_idx` (`athlete_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='TrueSkill Rating der Spieler ueber die Zeit';


CREATE TABLE IF NOT EXISTS `trueskill_evaluation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `predicted_score` DECIMAL(6,5) NOT NULL,
  `actual_result` DECIMAL(2,1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='TrueSkill System Evaluierung';


CREATE TABLE IF NOT EXISTS `rating_evaluation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `predicted_score` DECIMAL(6,5) NOT NULL,
  `actual_result` DECIMAL(2,1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Rating System Evaluierung';


SELECT 
	SQRT(AVG(POW(actual_result - predicted_score, 2))) AS RMSE,
	(- AVG((1 - actual_result) * LOG(1 - predicted_score) + actual_result * LOG(predicted_score))) AS LogLikelihood
FROM trueskill_evaluation
# TrueSkill-Vergleich
-> 0.461940437392944	0.631250367393709 (1400, 1400 / 3, 1400 / 8, 1400 / 280, 0)
-> 0.459284115674585	0.616474518312679 (default parameter, 0 draw prob)

SELECT 
	SQRT(AVG(POW(actual_result - predicted_score, 2))) AS RMSE,
	(- AVG((1 - actual_result) * LOG(1 - predicted_score) + actual_result * LOG(predicted_score))) AS LogLikelihood
FROM rating_evaluation

-> 0.421586440022216	0.528426070588128 (default k = 80)
-> 0.441350065903426	0.573177911242924 (Verlierer-Punkte-Quotient)
-> 0.449297515637661	0.589901256035216 (2/3-Satz-Adjustment)
-> 0.448133218081083	0.587469597639085 (mit heimvorteil)
-> 0.451102715914495	0.593557681495999 (start rating abhängig von der Klasse in der man in dieser Saison die meisten Spiele gemacht hat)
-> 0.463465223200558	0.619055783959049 (standard elo, k = 24 fest, alle start rating von 1600)
-> 0.5		0.69314718055915 (wenn man immer draw/gleiche stärke vorhersagt)

Log-Likelihood in TeX (Wikipedia)
<math>-\frac{1}{n} \sum_{i=1}^n{(1 - a_i)\,log(1 - p_i) + a_i\,log(p_i)}</math>



DELIMITER //

DROP PROCEDURE IF EXISTS calc_ratings //
# gamediff > 0: Sieg von team1; gamediff < 0: Sieg von Team2; gamediff = 0: Unentschieden
# homeadvantage: zu addierende ELO-Punkte der Heimmannschaft (team1), die in die Gewinnerwartung einfliessen, um den Heimvorteil abzubilden
# homeadvantage = NULL / 0, wenn Heimvorteil vernachlässigt werden soll (neutraler Platz)
CREATE PROCEDURE calc_ratings(
    INOUT team1rating1 SMALLINT UNSIGNED, INOUT team1rating2 SMALLINT UNSIGNED, 
    INOUT team2rating1 SMALLINT UNSIGNED, INOUT team2rating2 SMALLINT UNSIGNED, 
    IN gamediff TINYINT, IN losers_points_pct DECIMAL(4,1), IN homeadvantage TINYINT, IN track_evaluation TINYINT
) DETERMINISTIC NO SQL COMMENT 'Berechnet neues ELO-Rating der Parameter anhand der Ergebnisses'
BEGIN
IF (team1rating1 IS NOT NULL OR team1rating2 IS NOT NULL) AND (team2rating1 IS NOT NULL OR team2rating2 IS NOT NULL) THEN
    BEGIN
    DECLARE kfactor TINYINT UNSIGNED DEFAULT 24;
    DECLARE team1result, team2result DECIMAL(2,1);
    DECLARE gamediffadjustment, team1winexpect, team2winexpect FLOAT;
    DECLARE singles_doubles TINYINT UNSIGNED; 
    # 1 = singles, 2 = doubles
    DECLARE team1avgrating, team2avgrating SMALLINT;

    IF gamediff > 0 THEN
        SET team1result = 1;
    ELSEIF gamediff < 0 THEN
        SET team1result = 0;
    ELSE
        SET team1result = 0.5;
    END IF;
    SET team2result = 1 - team1result;

    # gamediffadjustment basierend auf den World Football ELO Ratings: http://www.eloratings.net/system.html
    # Musste aber fuer Badminton angepasst werden, da die Analyse der Ergebnisse ergab, dass die meisten Spiele 2:0 statt 2:1 nach Sätzen gewonnen werden.
    # Der Anteil Zwei-Satz-Spiele auf diesem regionalen Niveau war gleich 71.72%. Auf höherem Niveau sind die Spiele sicher etwas ausgeglichener, sodass
    # man insgesamt ca. von einem 2/3 zu 1/3 Verhältnis von 2-Satz-Spielen zu 3-Satz-Spielen ausgehen kann.
    # Daher ist gamediff = 2 der Normalfall und gamediff = 1 ein "besonders" knappes Ergebnis. 
    # Daher wird für gamediff = 1 dann der kfactor nach unten korrigiert und zwar um [(1/3) / (2/3) = 0.5] 1 / ( 2/3 + 1), wegen dem oben angegeben Verhältnis. 
    # Unentschieden und höhere Ergebnisse kommen zwar im Badminton-Wettkampf nicht vor. Aber bei Trainingspielen und anderen Sportarten wie Tennis können diese Fälle auftreten.
    # Unentschieden (gamediff = 0) bestehen auch mindestens aus 2 Saetzen (1:1) und können somit als sicherer, mit weniger Schwankungen, angesehen werden.
    # Bei noch höheren Ergebnissen wurde die Formel ebenfalls angepasst, da man bei Racket und Rückschlagsportarten meist den Anspruch hat jeden Satz einzeln zu gewinnen.
    # Nicht wie beim Fußball, wo die Mannschaft nach einem 4:0 Rückstand innerlich aufgibt und die nächsten Gegentore noch einfacher fallen.
    /*
	SET gamediff = ABS(gamediff);
    IF gamediff = 0 OR gamediff = 2 THEN
        SET gamediffadjustment = 1;
    ELSEIF gamediff = 1 THEN
        SET gamediffadjustment = 0.5;
    ELSE
        SET gamediffadjustment = (5 + gamediff) / 6;
    END IF;
	*/

	# TODO bei Aufgabe gamediffadjustment auf max. 1 begrenzen
	# Neue Anpassung mittels einer Normalverteilung mit Mittelwert = 63.9 und Standardabweichung = 23.2
	SET gamediffadjustment = EXP(-0.5 * POW((losers_points_pct - 63.9) / 23.2, 2)); # Wertebereich zwischen 0 und 1
	# Linke Seite (kleier als Mittelwert) der Normalverteilung spiegeln, denn besonders hohe Ergebnisse sollen zu einem erhöhten k-factor führen
	# Skalieren auf Bereich zwischen 0.1 (für enge Ergebnisse) und 2.1 (für hohe Ergebnisse)
	IF (losers_points_pct > 63.9) THEN
		SET gamediffadjustment = 0.1 + gamediffadjustment;
	ELSE
        SET gamediffadjustment = 2.1 - gamediffadjustment;
	END IF;
	
    SET singles_doubles = GREATEST(((team1rating1 IS NOT NULL) + (team1rating2 IS NOT NULL)), ((team2rating1 IS NOT NULL) + (team2rating2 IS NOT NULL)));
    
    # take the average and not the sum as this has effect on the win expectancy
    SET team1avgrating = ((IFNULL(team1rating1,0) + IFNULL(team1rating2,0)) DIV singles_doubles) + IFNULL(homeadvantage,0);
    SET team2avgrating = ((IFNULL(team2rating1,0) + IFNULL(team2rating2,0)) DIV singles_doubles);
    SET team1winexpect = 1 / (1 + POW(10, (team2avgrating - team1avgrating) / 400));
    SET team2winexpect = 1 - team1winexpect;
                        #1 / (1 + POW(10, (team1avgrating - team2avgrating) / 400));

	IF track_evaluation > 0 THEN
		INSERT INTO `rating_evaluation` (`predicted_score`, `actual_result`)
			VALUES (team1winexpect, team1result);
    END IF;

    # GREATEST() returns NULL  when one argument is null, so NULL ratings will stay NULL
    SET team1rating1 = GREATEST(0, ROUND(team1rating1 + kfactor * gamediffadjustment * (team1result - team1winexpect)));
    SET team1rating2 = GREATEST(0, ROUND(team1rating2 + kfactor * gamediffadjustment * (team1result - team1winexpect)));
    SET team2rating1 = GREATEST(0, ROUND(team2rating1 + kfactor * gamediffadjustment * (team2result - team2winexpect)));
    SET team2rating2 = GREATEST(0, ROUND(team2rating2 + kfactor * gamediffadjustment * (team2result - team2winexpect)));
    
    END;
END IF;
END;
//



DROP PROCEDURE IF EXISTS update_ratings //
CREATE PROCEDURE update_ratings() 
    MODIFIES SQL DATA 
    COMMENT 'Akt. Rating anhand Spiele in chronologischer Reihenfolge'
BEGIN 
    DECLARE done TINYINT DEFAULT 0;

    DECLARE homeadvantage TINYINT DEFAULT 36;
	DECLARE team1_player_pre_rating, team1_partner_pre_rating, team2_player_pre_rating, team2_partner_pre_rating SMALLINT UNSIGNED;
    
	DECLARE match_id INT UNSIGNED;
    DECLARE performed_at DATE;
    DECLARE discipline VARCHAR(10);
    DECLARE team1_player_id, team1_partner_id, team2_player_id, team2_partner_id INT UNSIGNED;
    DECLARE score_diff TINYINT SIGNED;
	DECLARE losers_points_pct DECIMAL(4,1);
    DECLARE match_season VARCHAR(10);
      
    DECLARE matches_ratings_cursor CURSOR FOR 
        SELECT
			m.id,
            DATE(tm.performed_at),
            (CASE WHEN mt.gender = 3 THEN 'mixed' WHEN mt.x_on_x = 2 THEN 'doubles' ELSE 'singles' END) AS 'discipline',
            m.team1_player_id, m.team1_partner_id, 
            m.team2_player_id, m.team2_partner_id,
            CAST(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score - m.team2_original_score, 
                    m.team1_score - m.team2_score) 
                AS SIGNED) AS 'score_diff',
			ROUND(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
				IF(m.team1_original_score < m.team2_original_score, 
					m.team1_original_points / m.team2_original_points,
					m.team2_original_points / m.team1_original_points
				),
				IF(m.team1_score < m.team2_score, 
					m.team1_points / m.team2_points,
					m.team2_points / m.team1_points
				)
			) * 100, 1) AS 'losers_points_pct',
            r.season
        FROM matches m
		INNER JOIN match_types mt ON (m.match_type_id = mt.id)
        INNER JOIN teammatches tm ON (m.teammatch_id = tm.id)
        INNER JOIN teams t ON tm.team1_id = t.id
        INNER JOIN leagues l ON t.league_id = l.id
		INNER JOIN tournaments r ON l.tournament_id = r.id
        WHERE NOT m.no_fight AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
        )
        ORDER BY tm.performed_at ASC; 
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
	-- Performance optimization: Tell MySQL not to update the indexes after each insert but instead reindex the table after the entire bulk is processed
	-- This could theoretically also be achieved with transactions.
	-- Turn off autocommit mode, because it performs a log flush to disk for every insert.
	-- One could also use INSERT DELAYED on MyISAM for batch inserts but not on InnoDb 
	ALTER TABLE `ratinghistory` DISABLE KEYS;
	SET FOREIGN_KEY_CHECKS = 0;
	SET UNIQUE_CHECKS = 0;
	SET AUTOCOMMIT = 0;
	
    OPEN matches_ratings_cursor;
       
    REPEAT
        FETCH matches_ratings_cursor INTO 
			match_id, performed_at, discipline, 
            team1_player_id, team1_partner_id, 
            team2_player_id, team2_partner_id, 
            score_diff,
			losers_points_pct,
            match_season;         
            
        IF NOT done THEN
        BEGIN
            -- Ein SELECT ... INTO ohne Treffer raises NOT FOUND HANDLER
            -- So muss der aeussere Handler, der sonst greift, in dem inneren Block ueberschrieben werden, um bei einem NULL player die Schleife nicht zu beenden
            DECLARE CONTINUE HANDLER FOR NOT FOUND BEGIN END;
			
			SET @team1_player_rating = NULL;
			SET @team1_partner_rating = NULL;
			SET @team2_player_rating = NULL;
			SET @team2_partner_rating = NULL;
                    
            -- spieler mit null werden ignoriert
            -- ratings muessen @variable sein, um von der calc_ratings procedure veraendert werden zu koennen
            CASE discipline
            WHEN 'doubles' THEN
                SELECT doubles_rating INTO @team1_player_rating FROM athletes WHERE id = team1_player_id;
                SELECT doubles_rating INTO @team1_partner_rating FROM athletes WHERE id = team1_partner_id;
                SELECT doubles_rating INTO @team2_player_rating  FROM athletes WHERE id = team2_player_id;
                SELECT doubles_rating INTO @team2_partner_rating FROM athletes WHERE id = team2_partner_id;
            WHEN 'mixed' THEN
                SELECT mixed_rating INTO @team1_player_rating  FROM athletes WHERE id = team1_player_id;
                SELECT mixed_rating INTO @team1_partner_rating FROM athletes WHERE id = team1_partner_id;
                SELECT mixed_rating INTO @team2_player_rating  FROM athletes WHERE id = team2_player_id;
                SELECT mixed_rating INTO @team2_partner_rating FROM athletes WHERE id = team2_partner_id;
            ELSE
                SELECT singles_rating INTO @team1_player_rating  FROM athletes WHERE id = team1_player_id;
                SELECT singles_rating INTO @team1_partner_rating FROM athletes WHERE id = team1_partner_id;
                SELECT singles_rating INTO @team2_player_rating  FROM athletes WHERE id = team2_player_id;
                SELECT singles_rating INTO @team2_partner_rating FROM athletes WHERE id = team2_partner_id;
            END CASE;
            
	    -- Startrating von 1900 für höchste Berliner Spielklasse
	    
            IF (team1_player_id IS NOT NULL AND @team1_player_rating IS NULL) THEN -- Spieler vorhanden, aber noch kein Rating
                SELECT (-111 * (`main_level` - 5) + 1900) INTO @team1_player_rating 
                    FROM `athlete_leagues` WHERE `athlete_id` = team1_player_id AND `season` = match_season;
                IF (FOUND_ROWS() = 0) THEN -- Keine Hauptspielklasse vorhanden (Anfang der Saison), also Standard setzen
                    SET @team1_player_rating = 1600;
                END IF;
            END IF;
            IF (team1_partner_id IS NOT NULL AND @team1_partner_rating IS NULL) THEN -- Spieler vorhanden, aber noch kein Rating
                SELECT (-111 * (`main_level` - 5) + 1900) INTO @team1_partner_rating 
                    FROM `athlete_leagues` WHERE `athlete_id` = team1_partner_id AND `season` = match_season;
                IF (FOUND_ROWS() = 0) THEN -- Keine Hauptspielklasse vorhanden (Anfang der Saison), also Standard setzen
                    SET @team1_partner_rating = 1600;
                END IF;
            END IF;
            IF (team2_player_id IS NOT NULL AND @team2_player_rating IS NULL) THEN -- Spieler vorhanden, aber noch kein Rating
                SELECT (-111 * (`main_level` - 5) + 1900) INTO @team2_player_rating 
                    FROM `athlete_leagues` WHERE `athlete_id` = team2_player_id AND `season` = match_season;
                IF (FOUND_ROWS() = 0) THEN -- Keine Hauptspielklasse vorhanden (Anfang der Saison), also Standard setzen
                    SET @team2_player_rating = 1600;
                END IF;
            END IF;
            IF (team2_partner_id IS NOT NULL AND @team2_partner_rating IS NULL) THEN -- Spieler vorhanden, aber noch kein Rating
                SELECT (-111 * (`main_level` - 5) + 1900) INTO @team2_partner_rating 
                    FROM `athlete_leagues` WHERE `athlete_id` = team2_partner_id AND `season` = match_season;
                IF (FOUND_ROWS() = 0) THEN -- Keine Hauptspielklasse vorhanden (Anfang der Saison), also Standard setzen
                    SET @team2_partner_rating = 1600;
                END IF;
            END IF;
			
			SET team1_player_pre_rating 	= @team1_player_rating;
			SET team1_partner_pre_rating 	= @team1_partner_rating;
			SET team2_player_pre_rating 	= @team2_player_rating ;
			SET team2_partner_pre_rating 	= @team2_partner_rating;
            
            CALL calc_ratings(
				@team1_player_rating, @team1_partner_rating, 
				@team2_player_rating, @team2_partner_rating, 
				score_diff, losers_points_pct, homeadvantage, 
				1
			);
               
            CASE discipline
            WHEN 'doubles' THEN
                UPDATE athletes SET doubles_rating = @team1_player_rating, doubles_matches = doubles_matches + 1    WHERE id = team1_player_id;
                UPDATE athletes SET doubles_rating = @team1_partner_rating, doubles_matches = doubles_matches + 1   WHERE id = team1_partner_id;
                UPDATE athletes SET doubles_rating = @team2_player_rating, doubles_matches = doubles_matches + 1    WHERE id = team2_player_id;
                UPDATE athletes SET doubles_rating = @team2_partner_rating, doubles_matches = doubles_matches + 1   WHERE id = team2_partner_id;
            WHEN 'mixed' THEN                
                UPDATE athletes SET mixed_rating = @team1_player_rating, mixed_matches = mixed_matches + 1    WHERE id = team1_player_id;
                UPDATE athletes SET mixed_rating = @team1_partner_rating, mixed_matches = mixed_matches + 1   WHERE id = team1_partner_id;
                UPDATE athletes SET mixed_rating = @team2_player_rating, mixed_matches = mixed_matches + 1    WHERE id = team2_player_id;
                UPDATE athletes SET mixed_rating = @team2_partner_rating, mixed_matches = mixed_matches + 1   WHERE id = team2_partner_id;
            ELSE               
                UPDATE athletes SET singles_rating = @team1_player_rating, singles_matches = singles_matches + 1    WHERE id = team1_player_id;
                UPDATE athletes SET singles_rating = @team1_partner_rating, singles_matches = singles_matches + 1   WHERE id = team1_partner_id;
                UPDATE athletes SET singles_rating = @team2_player_rating, singles_matches = singles_matches + 1    WHERE id = team2_player_id;
                UPDATE athletes SET singles_rating = @team2_partner_rating, singles_matches = singles_matches + 1   WHERE id = team2_partner_id;
            END CASE;
            
            -- Verlauf des Ratings der Spieler speichern, um Trends zeigen zu können
            IF team1_player_id IS NOT NULL THEN
				INSERT INTO `ratinghistory` (`athlete_id`, `match_id`, `discipline`, `pre_rating`, `post_rating`, `created_at`)
					VALUES (team1_player_id, match_id, discipline, team1_player_pre_rating, @team1_player_rating, performed_at);
				/* Aggregated monthly version:
                SELECT `id`, `created_at` INTO @last_ratinghistory_id, @last_ratinghistory_update FROM `ratinghistory` AS rh 
                    WHERE `athlete_id` = team1_player_id AND rh.`discipline` = discipline ORDER BY `created_at` DESC LIMIT 1;
                IF (FOUND_ROWS() = 0 OR YEAR(performed_at) <> YEAR(@last_ratinghistory_update) OR MONTH(performed_at) <> MONTH(@last_ratinghistory_update)) THEN
                    -- monatliche Speicherung, sonst Werte für den Monat aktualisieren
                    -- alternativ letzter Verlaufseintrag des Spielers in dieser Disziplin ist mehr als 30 Tage her: DATEDIFF(performed_at, @last_ratinghistory_update) > 30
                    INSERT INTO `ratinghistory` (`athlete_id`, `discipline`, `created_at`, `rating`, `number_matches`)
                        VALUES (team1_player_id, discipline, performed_at, @team1_player_rating, 1);
                ELSE
                    UPDATE `ratinghistory` SET `created_at` = performed_at, `rating` = @team1_player_rating, `number_matches` = `number_matches` + 1 
                        WHERE `id` = @last_ratinghistory_id;
                END IF;
				*/
            END IF;
            IF team1_partner_id IS NOT NULL THEN
				INSERT INTO `ratinghistory` (`athlete_id`, `match_id`, `discipline`, `pre_rating`, `post_rating`, `created_at`)
					VALUES (team1_partner_id, match_id, discipline, team1_partner_pre_rating, @team1_partner_rating, performed_at);
            END IF;
            IF team2_player_id IS NOT NULL THEN
				INSERT INTO `ratinghistory` (`athlete_id`, `match_id`, `discipline`, `pre_rating`, `post_rating`, `created_at`)
                        VALUES (team2_player_id, match_id, discipline, team2_player_pre_rating, @team2_player_rating, performed_at);
            END IF;
            IF team2_partner_id IS NOT NULL THEN
				INSERT INTO `ratinghistory` (`athlete_id`, `match_id`, `discipline`, `pre_rating`, `post_rating`, `created_at`)
					VALUES (team2_partner_id, match_id, discipline, team2_partner_pre_rating, @team2_partner_rating, performed_at);
            END IF;
                        
        END;
        END IF;
    UNTIL done END REPEAT;

    CLOSE matches_ratings_cursor;
	
	COMMIT;
	SET AUTOCOMMIT = 1;
	ALTER TABLE `ratinghistory` ENABLE KEYS;
	SET FOREIGN_KEY_CHECKS = 1;
	SET UNIQUE_CHECKS = 1;
END
//


SET @team1_player_rating = 1600; 
SELECT singles_rating INTO @team1_player_rating  FROM athletes WHERE id = NULL;
SELECT @team1_player_rating;

set @t1r1 = 1500, @t1r2 = 1500, @t2r1 = 1500, @t2r2 = 1500;
call calc_ratings(@t1r1, @t1r2, @t2r1, @t2r2, 2, 45, 36, 0);
select @t1r1, @t1r2, @t2r1, @t2r2;

set @t1r1 = 1674, @t1r2 = null, @t2r1 = 2200, @t2r2 = null;
call calc_ratings(@t1r1, @t1r2, @t2r1, @t2r2, 1, 95, 0, 0);
select @t1r1, @t1r2, @t2r1, @t2r2;


UPDATE athletes SET 
    singles_rating = NULL, doubles_rating = NULL, mixed_rating = NULL,
    singles_matches = 0, doubles_matches = 0, mixed_matches = 0;
TRUNCATE TABLE ratinghistory;
TRUNCATE TABLE rating_evaluation;
CALL update_ratings();









SELECT athlete_id, created_at, discipline, rating FROM `ratinghistory` WHERE athlete_id = 808884 ORDER BY created_at, athlete_id, discipline

SELECT r.athlete_id, CONCAT_WS(' ', a.first_name, a.last_name) AS athlete_name, r.created_at, r.discipline, r.rating 
FROM `ratinghistory` r
INNER JOIN athletes a ON r.athlete_id = a.id
WHERE athlete_id IN (SELECT id FROM `athletes` WHERE club_id = 46 AND singles_matches + doubles_matches + mixed_matches > 99) 
ORDER BY created_at, athlete_id, discipline

SELECT id FROM `athletes` WHERE club_id = 46 AND singles_matches + doubles_matches + mixed_matches > 99


SELECT athlete_id, created_at, discipline, skill_mean, skill_std FROM `trueskillhistory` WHERE athlete_id = 808884 ORDER BY created_at, athlete_id, discipline

SELECT r.athlete_id, CONCAT_WS(' ', a.first_name, a.last_name) AS athlete_name, r.created_at, r.discipline, skill_mean, skill_std
FROM `trueskillhistory` r
INNER JOIN athletes a ON r.athlete_id = a.id
WHERE athlete_id IN (SELECT id FROM `athletes` WHERE club_id = 46 AND singles_matches + doubles_matches + mixed_matches > 99) 
ORDER BY created_at, athlete_id, discipline

        SELECT 
            COUNT(*) AS number_matches,
            SUM(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score > m.team2_original_score, 
                    m.team1_score > m.team2_score
                )) AS hometeam_wins,
            SUM(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score < m.team2_original_score, 
                    m.team1_score < m.team2_score
                )) AS awayteam_wins,    
            SUM(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score = m.team2_original_score, 
                    m.team1_score = m.team2_score
                )) AS draws, 
            SUM(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score + m.team2_original_score, 
                    m.team1_score + m.team2_score
                )) AS number_games,
            SUM(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score + m.team2_original_score, 
                    m.team1_score + m.team2_score
                ) = 2) AS matches_of_2_games,
            SUM(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score + m.team2_original_score, 
                    m.team1_score + m.team2_score
                ) = 3) AS matches_of_3_games,
            SUM(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score + m.team2_original_score < 2 OR m.team1_original_score + m.team2_original_score > 3, 
                    m.team1_score + m.team2_score < 2 OR m.team1_score + m.team2_score > 3
                )) AS matches_of_x_games, -- nur zum Überprüfen, dass es nur 2- und 3-Satz Spiele gibt
            AVG(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
                    m.team1_original_score + m.team2_original_score, 
                    m.team1_score + m.team2_score
                )) AS avg_games_per_match
        FROM matches m
        WHERE NOT m.no_fight AND NOT m.withdrawn_by AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
        ); 
        
        heimvorteil bei individual-spielen, wo es keine unentschieden gibt
        heimvorteil = hometeam_wins / number_matches
        heimvorteil = 27414 / 49591 [23897 / 43229] = 0.5528
        d.h. der heimspieler / die heimpaarung gewinnt in 55.28% der Fälle (nur Doppel 55.57% -> nicht abhängig von Disziplin)
        jetzt wird dieser wert gleich der ELO Gewinnerwartung gesetzt, um auszurechnen welcher Ratingunterschied zu dieser Gewinnerwartung führt
        0.5528 = 1 / (1 + POW(10, (Rb - Ra) / 400));
        (Rb - Ra) =  log10(1 / 0.5528 - 1) * 400 = -36.83 
        Damit ein Spieler in 55.28% der Fälle gewinnt, bezogen auf eine unendlich große Probe, muss er einen Vorteil von 36 ELO Punkten haben.
        
        Anteil Zwei-Satz-Spiele = matches_of_2_games / number_matches = 31003 / 43229 = 0.7172
        
        


      SELECT m.*, l.*, u.*, u2.*
        FROM matches m
        INNER JOIN teammatches tm ON (m.teammatch_id = tm.id)
        INNER JOIN teams t1 ON (tm.team1_id = t1.id)
        INNER JOIN teams t2 ON (tm.team2_id = t2.id)
        INNER JOIN leagues l ON (t1.league_id = l.id)
        LEFT JOIN lineups u ON (m.team1_player_id = u.athlete_id AND t1.id = u.team_id AND u.stage = 1)
        LEFT JOIN lineups u2 ON (m.team1_player_id = u2.athlete_id AND u2.stage = 1)
        WHERE NOT m.no_fight AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
        )
        ORDER BY m.teammatch_id, m.match_type; 
        
        
        SELECT count(*), m.*, l.*, u.*, u2.*
        FROM matches m
        INNER JOIN teammatches tm ON (m.teammatch_id = tm.id)
        INNER JOIN teams t1 ON (tm.team1_id = t1.id)
        INNER JOIN teams t2 ON (tm.team2_id = t2.id)
        INNER JOIN leagues l ON (t1.league_id = l.id)
        LEFT JOIN lineups u ON (m.team1_player_id = u.athlete_id AND t1.id = u.team_id AND u.stage = 1)
        LEFT JOIN lineups u2 ON (m.team1_player_id = u2.athlete_id AND u2.stage = 1)
        WHERE NOT m.no_fight AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
        )
        group by m.id
        having count(*) > 1;
        
        # in welchem ligalevel hat ein spezifischer sportler in einer bestimmten saison die meisten spiele gemacht
        SELECT a.id, l.season, l.level, COUNT(*) AS number_matches
        FROM athletes a
		LEFT JOIN matches m ON ( 
			m.team1_player_id = a.id OR 
			m.team1_partner_id = a.id OR 
			m.team2_player_id = a.id OR 
			m.team2_partner_id = a.id
		)
        LEFT JOIN teammatches tm ON m.teammatch_id = tm.id
        LEFT JOIN teams t ON t.id = tm.team1_id 
        INNER JOIN leagues l ON t.league_id = l.id
        WHERE l.season = '2004-05' AND a.id = 808884
        GROUP BY a.id, l.season, l.level
        ORDER BY number_matches DESC, level DESC
        LIMIT 1
