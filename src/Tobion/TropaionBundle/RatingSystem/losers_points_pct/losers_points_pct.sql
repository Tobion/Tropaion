Für Rating-Berechnung:
Höhe des Sieges (engl. margin of victory)
Verteilung der Losers_Orig_Points_Rate/Ratio (d.h. Quote von Verlierer Punkte Summe / Gewinner Punkte Summe) interessant, um den k-factor zu beeinflussen statt nur mit Hilfe der Anzahl der Sätze (2-Satz- oder 3-Satz-Spiel)
Kontinuierliches und allgemeingültiges Maß, da das konkrete Verhältis unterschiedlich zusammengesetzt sein kann (unterschdl. Anzahl von Sätzen und gespielten Punkten).
In den vorliegenden Daten kamen 882 unterschiedliche Quotieten vor.
Um die Verteilung dieses Maßes zu analysieren, wurden die Messwerte als als Histogramm visualisiert.

SELECT match_type,
	IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
		m.team1_original_score + m.team2_original_score,
		m.team1_score + m.team2_score
	) AS games,
	avg_orig_smaller_div_bigger_gamescore_permil, std_orig_smaller_div_bigger_gamescore_permil,
	ROUND(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
		IF(m.team1_original_score < m.team2_original_score, 
			m.team1_original_points / m.team2_original_points,
			m.team2_original_points / m.team1_original_points
		),
		IF(m.team1_score < m.team2_score, 
			m.team1_points / m.team2_points,
			m.team2_points / m.team1_points
		)
	) * 100, 1) AS losers_original_points_pct
FROM matches m
WHERE NOT m.no_fight AND NOT m.withdrawn_by AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
)
; 


# Berechne Mittelwert und Standardabweichung
SELECT
	AVG(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
		IF(m.team1_original_score < m.team2_original_score, 
			m.team1_original_points / m.team2_original_points,
			m.team2_original_points / m.team1_original_points
		),
		IF(m.team1_score < m.team2_score, 
			m.team1_points / m.team2_points,
			m.team2_points / m.team1_points
		)
	)) * 100 AS losers_original_points_avg,
	STDDEV_POP(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
		IF(m.team1_original_score < m.team2_original_score, 
			m.team1_original_points / m.team2_original_points,
			m.team2_original_points / m.team1_original_points
		),
		IF(m.team1_score < m.team2_score, 
			m.team1_points / m.team2_points,
			m.team2_points / m.team1_points
		)
	)) * 100 AS losers_original_points_std,
	COUNT(DISTINCT(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
		IF(m.team1_original_score < m.team2_original_score, 
			m.team1_original_points / m.team2_original_points,
			m.team2_original_points / m.team1_original_points
		),
		IF(m.team1_score < m.team2_score, 
			m.team1_points / m.team2_points,
			m.team2_points / m.team1_points
		)
	))) AS losers_original_points_count_distinct,
	COUNT(*) AS count
FROM matches m
WHERE NOT m.no_fight AND NOT m.withdrawn_by AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
)

SELECT
	COUNT(DISTINCT(IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
		IF(m.team1_original_score < m.team2_original_score, 
			m.team1_original_points / m.team2_original_points,
			m.team2_original_points / m.team1_original_points
		),
		IF(m.team1_score < m.team2_score, 
			m.team1_points / m.team2_points,
			m.team2_points / m.team1_points
		)
	)))
FROM matches m
WHERE NOT m.no_fight AND NOT m.withdrawn_by AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
)


# Berechne inverse kumulative Verteilungsfunktion anhand der vorliegenden Daten.
# Für Zeile R, gibt es die Anzahl der Zeilen an, deren Werte größer sind als der Wert von R, geteilt durch die Anzahl aller Zeilen, die betrachtet wurden.
# http://en.wikipedia.org/wiki/Cumulative_distribution_function#Complementary_cumulative_distribution_function
SELECT COUNT(*) INTO @count_real_matchresults FROM matches m
WHERE NOT m.no_fight AND NOT m.withdrawn_by AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
);

SELECT COUNT(*) / @count_real_matchresults
FROM matches m
WHERE NOT m.no_fight AND NOT m.withdrawn_by AND (
            (m.team1_score IS NOT NULL AND m.team2_score IS NOT NULL) OR 
            (m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL)
) AND (
	IF(m.team1_original_score IS NOT NULL AND m.team2_original_score IS NOT NULL, 
		IF(m.team1_original_score < m.team2_original_score, 
			m.team1_original_points / m.team2_original_points,
			m.team2_original_points / m.team1_original_points
		),
		IF(m.team1_score < m.team2_score, 
			m.team1_points / m.team2_points,
			m.team2_points / m.team1_points
		)
	) > 0.63)
;