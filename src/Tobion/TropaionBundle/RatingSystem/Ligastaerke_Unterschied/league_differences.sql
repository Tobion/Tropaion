/*
	Um den Spielern ein fundierteres Start-Rating zu geben wurde analysiert, wie groß der Spielstärkeunterschied Spieler unterschiedlicher Klassen ist. 
	Um diese Frage zu beantworten, wurde zunächst für jeden Spieler in jeder Saison seine jeweilige Hauptspielklasse ermittelt, in der er in dieser Zeit die meisten Spiele absolviert hat. 
	Darauf aufbauend wurde abgefragt, wie viel Prozent der Spiele, bei denen Spieler verschiedener Hauptspielklassen beteiligt sind, von den Spielern mit der besseren Hauptspielklasse gewonnen wurden.	
*/
      
CREATE TABLE athlete_leagues (
    athlete_id INT UNSIGNED NOT NULL, 
    season VARCHAR(10) NOT NULL,
    main_level TINYINT UNSIGNED NULL,
    PRIMARY KEY (athlete_id, season)
);
 

SET @season = '2002-03'; 

INSERT INTO athlete_leagues   
        SELECT a.id, @season,
            (
            SELECT l.level
    		FROM matches m 
            INNER JOIN teammatches tm ON m.teammatch_id = tm.id
            INNER JOIN teams t ON t.id = tm.team1_id 
            INNER JOIN leagues l ON t.league_id = l.id
            WHERE l.season = @season AND ( 
    			m.team1_player_id = a.id OR 
    			m.team1_partner_id = a.id OR 
    			m.team2_player_id = a.id OR 
    			m.team2_partner_id = a.id
    		)
            GROUP BY l.level
            ORDER BY COUNT(*) DESC, level DESC
            LIMIT 1
            ) AS main_league_level
        FROM athletes a;
        

DELETE FROM athlete_leagues WHERE main_level IS NULL;
OPTIMIZE TABLE athlete_leagues;


SELECT 
	ABS(CAST(
		((t1l1.main_level + IFNULL(t1l2.main_level, 0)) / (1 + (t1l2.main_level IS NOT NULL))) - 
		((t2l1.main_level + IFNULL(t2l2.main_level, 0)) / (1 + (t2l2.main_level IS NOT NULL))) 
		AS DECIMAL(4,1))) AS league_level_diff,
	IF(
		((t1l1.main_level + IFNULL(t1l2.main_level, 0)) / (1 + (t1l2.main_level IS NOT NULL))) <
		((t2l1.main_level + IFNULL(t2l2.main_level, 0)) / (1 + (t2l2.main_level IS NOT NULL))),
		CONCAT_WS('-',
			ROUND((t1l1.main_level + IFNULL(t1l2.main_level, 0)) / (1 + (t1l2.main_level IS NOT NULL)), 1),
			ROUND((t2l1.main_level + IFNULL(t2l2.main_level, 0)) / (1 + (t2l2.main_level IS NOT NULL)), 1)
		),
		CONCAT_WS('-', 
			ROUND((t2l1.main_level + IFNULL(t2l2.main_level, 0)) / (1 + (t2l2.main_level IS NOT NULL)), 1),
			ROUND((t1l1.main_level + IFNULL(t1l2.main_level, 0)) / (1 + (t1l2.main_level IS NOT NULL)), 1)
		)
		) AS league_level_confrontation,
	COUNT(*) AS number_matches,
	SUM(CASE 
		WHEN t1l1.main_level + IFNULL(t1l2.main_level, 0) < t2l1.main_level + IFNULL(t2l2.main_level, 0) THEN 
			IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
			m.team1_original_score > m.team2_original_score,
			m.team1_score > m.team2_score)
		WHEN t1l1.main_level + IFNULL(t1l2.main_level, 0) > t2l1.main_level + IFNULL(t2l2.main_level, 0) THEN 
			IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
			m.team1_original_score < m.team2_original_score,
			m.team1_score < m.team2_score)
		ELSE 0 END
		) AS superior_league_wins,
	SUM(CASE 
		WHEN t1l1.main_level + IFNULL(t1l2.main_level, 0) < t2l1.main_level + IFNULL(t2l2.main_level, 0) THEN 
			IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
			m.team1_original_score < m.team2_original_score,
			m.team1_score < m.team2_score)
		WHEN t1l1.main_level + IFNULL(t1l2.main_level, 0) > t2l1.main_level + IFNULL(t2l2.main_level, 0) THEN 
			IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
			m.team1_original_score > m.team2_original_score,
			m.team1_score > m.team2_score)
		ELSE 0 END
		) AS inferior_league_wins,
	SUM(CASE 
		WHEN t1l1.main_level + IFNULL(t1l2.main_level, 0) < t2l1.main_level + IFNULL(t2l2.main_level, 0) THEN 1
		ELSE 0 END
		) AS superior_home_matches,
	SUM(CASE 
		WHEN t1l1.main_level + IFNULL(t1l2.main_level, 0) > t2l1.main_level + IFNULL(t2l2.main_level, 0) THEN 1
		ELSE 0 END
		) AS inferior_home_matches,
	SUM(IF(t1l1.main_level + IFNULL(t1l2.main_level, 0) = t2l1.main_level + IFNULL(t2l2.main_level, 0), 1, 0)) AS same_league_matches
FROM matches m
INNER JOIN teammatches tm ON m.teammatch_id = tm.id
INNER JOIN teams t ON tm.team1_id = t.id
INNER JOIN leagues l ON t.league_id = l.id
INNER JOIN athlete_leagues t1l1 ON t1l1.athlete_id = m.team1_player_id AND t1l1.season = l.season
INNER JOIN athlete_leagues t2l1 ON t2l1.athlete_id = m.team2_player_id AND t2l1.season = l.season
LEFT JOIN athlete_leagues t1l2 ON t1l2.athlete_id = m.team1_partner_id AND t1l2.season = l.season
LEFT JOIN athlete_leagues t2l2 ON t2l2.athlete_id = m.team2_partner_id AND t2l2.season = l.season
WHERE NOT m.no_fight AND (
	(m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
	(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
)
GROUP BY league_level_confrontation -- alternatively league_level_diff
ORDER BY league_level_diff ASC;