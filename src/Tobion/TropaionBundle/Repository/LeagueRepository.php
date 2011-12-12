<?php

namespace Tobion\TropaionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LeagueRepository
 *
 */
class LeagueRepository extends EntityRepository
{

/*

Ummeldungen zur Rueckrunde einer bestimmten Saison:	
Beide Abfragen haben das gleiche Ergebnis.

SELECT u1.* , u2.*
FROM lineups u1
INNER JOIN teams t1 ON t1.id = u1.team_id
INNER JOIN leagues l1 ON t1.league_id = l1.id
LEFT JOIN lineups u2 ON u1.athlete_id = u2.athlete_id
INNER JOIN teams t2 ON t2.id = u2.team_id
INNER JOIN leagues l2 ON t2.league_id = l2.id
WHERE u1.stage = 2
AND l1.tournament_id = 1
AND l2.tournament_id = 1
AND (
u1.team_id <> u2.team_id
OR u1.position <> u2.position
)
ORDER BY u1.team_id ASC , u1.position ASC


Folgende Version ist etwas schneller.

SELECT u.athlete_id, 
	MIN(IF(stage = 1, team_id, NULL)) AS round1_team_id,
	MAX(IF(stage = 2, team_id, NULL)) AS round2_team_id,
	MIN(IF(stage = 1, position, NULL)) AS round1_position,
	MAX(IF(stage = 2, position, NULL)) AS round2_position
FROM lineups u
INNER JOIN teams t ON t.id = u.team_id
INNER JOIN leagues l ON t.league_id = l.id
WHERE l.tournament_id = 1
GROUP BY athlete_id
HAVING MIN(team_id) <> MAX(team_id) OR MIN(position) <> MAX(position)
ORDER BY u.athlete_id ASC

*/

	/**
	 * Gibt den aktuellen Tabellenstand als Array zurück. 
	 * Berücksichtigt auch zurückgezogene Teams (withdrawn) und Teammatches, das beide Mannschaften komplett verloren haben (beide scores = 0)
	 * @TODO Die Verlustpunkte bei beiderseitiger Niederlage sind hardcoded.
	 * @param int $leagueId
	 * @param int $stage		1 = Hinrunde, 2 = Rückrunde, sonst alles
	 * @param int $homeaway		1 = Heimspiele, 2 = Auswaertsspiele, sonst alles
	 * @param int $winPoints
	 * @param int $drawPoints
	 * @return array
	 */
	public function getStandings($leagueId, $stage = 0, $homeaway = 0, $winPoints = 2, $drawPoints = 1)
	{
		$sqlParams = array();

		if ($homeaway == 1) {
			$homeawayJoin = ' LEFT JOIN teammatches tm ON (t.id = tm.team1_id) ';
		}
		else if ($homeaway == 2) {
			$homeawayJoin = ' LEFT JOIN teammatches tm ON (t.id = tm.team2_id) ';
		}
		else {
			$homeawayJoin = ' LEFT JOIN teammatches tm ON (t.id = tm.team1_id OR t.id = tm.team2_id) ';
		}

		if ($stage) {
			$stageCondition = ' AND tm.stage = :STAGE ';
			$sqlParams['STAGE'] = $stage;
		}
		else {
			$stageCondition = '';
		}

		/*
		$query = 'SELECT 
				t.id, t.club_id, c.code AS club_code, c.name AS club_name, t.team_number, t.withdrawn,
			SUM(IF((tm.annulled AND NOT t.withdrawn) OR tm.team1_score IS NULL OR tm.team2_score IS NULL, 0, tm.id IS NOT NULL)) AS number_teammatches,
			SUM(
				IF(tm.team1_id = t.id, 
					IF(tm.team1_score > tm.team2_score, 1, 0),
					IF(tm.team1_score < tm.team2_score, 1, 0)
				)
			) AS won_teammatches,
			SUM(
				IF(tm.team1_score = tm.team2_score AND tm.team1_score <> 0, 1, 0)
			) AS drawn_teammatches,
			SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, 1,
					IF(tm.team1_id = t.id, 
						IF(tm.team1_score < tm.team2_score, 1, 0),
						IF(tm.team1_score > tm.team2_score, 1, 0)
					)
				)
			) AS lost_teammatches,
			IFNULL(SUM(
				IF(tm.team1_id = t.id, tm.team1_score, tm.team2_score)
			), 0) AS won_matches,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, 8, 
					IF(tm.team1_id = t.id, tm.team2_score, tm.team1_score)
				)
			), 0) AS lost_matches,
			IFNULL(SUM(
				IF(tm.team1_id = t.id, mg.team1_won_games, mg.team2_won_games)
			), 0) AS won_games,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, 16, 
					IF(tm.team1_id = t.id, mg.team2_won_games, mg.team1_won_games)
				)
			), 0) AS lost_games,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, -16, 
					IF(tm.team1_id = t.id, mg.team1_won_games - mg.team2_won_games, mg.team2_won_games - mg.team1_won_games)
				)
			), 0) AS diff_games,
			IFNULL(SUM(
				IF(tm.team1_id = t.id, mg.team1_won_points, mg.team2_won_points)
			), 0) AS won_score,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, 336, 
					IF(tm.team1_id = t.id, mg.team2_won_points, mg.team1_won_points)
				)
			), 0) AS lost_score,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, -336, 
					IF(tm.team1_id = t.id, mg.team1_won_points - mg.team2_won_points, mg.team2_won_points - mg.team1_won_points)
				)
			), 0) AS diff_score,
			# (x * won_teammatches + y * drawn_teammatches) AS points
			SUM(
				IF(tm.team1_id = t.id, 
					CASE WHEN tm.team1_score > tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END,
					CASE WHEN tm.team1_score < tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END
				)
			) AS points,
			# ifnull due to possible division by zero
			IFNULL(ROUND(
			SUM(
				IF(tm.team1_id = t.id, 
					CASE WHEN tm.team1_score > tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END,
					CASE WHEN tm.team1_score < tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END
				)
			)
			/ 
			(GREATEST(:WIN_POINTS, :DRAW_POINTS) * 
				# see number_teammatches
				SUM(IF((tm.annulled AND NOT t.withdrawn) OR tm.team1_score IS NULL OR tm.team2_score IS NULL, 0, tm.id IS NOT NULL))
			) * 100, 
			1), 0) AS points_percent
		FROM teams t
		INNER JOIN leagues l ON t.league_id = l.id
		INNER JOIN clubs c ON t.club_id = c.id ' .
		$homeawayJoin . '
		LEFT JOIN (
			SELECT 
				m.teammatch_id,
				SUM(m.team1_score) AS team1_won_games,
				SUM(m.team2_score) AS team2_won_games,
				SUM(m.team1_points) AS team1_won_points,
				SUM(m.team2_points) AS team2_won_points
			FROM matches m 
			GROUP BY m.teammatch_id
		) mg ON tm.id = mg.teammatch_id
		WHERE 
			t.league_id = :LEAGUE_ID
			AND (NOT tm.annulled OR t.withdrawn) '
			. $roundCondition .	'
		GROUP BY t.id
		ORDER BY 
			t.withdrawn ASC, 
			points DESC,  # points_percent DESC wenn Liga-uebergreifend
			won_matches DESC, 
			diff_games DESC, 
			diff_score DESC';
		*/

		$query = 'SELECT 
				t.id, t.club_id, c.code AS club_code, c.name AS club_name, t.team_number, t.withdrawn,
			SUM(IF((tm.annulled AND NOT t.withdrawn) OR tm.team1_score IS NULL OR tm.team2_score IS NULL, 0, tm.id IS NOT NULL)) AS number_teammatches,
			SUM(
				IF(tm.team1_id = t.id, 
					IF(tm.team1_score > tm.team2_score, 1, 0),
					IF(tm.team1_score < tm.team2_score, 1, 0)
				)
			) AS won_teammatches,
			SUM(
				IF(tm.team1_score = tm.team2_score AND tm.team1_score <> 0, 1, 0)
			) AS drawn_teammatches,
			SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, 1,
					IF(tm.team1_id = t.id, 
						IF(tm.team1_score < tm.team2_score, 1, 0),
						IF(tm.team1_score > tm.team2_score, 1, 0)
					)
				)
			) AS lost_teammatches,
			IFNULL(SUM(
				IF(tm.team1_id = t.id, tm.team1_score, tm.team2_score)
			), 0) AS won_matches,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, 8, 
					IF(tm.team1_id = t.id, tm.team2_score, tm.team1_score)
				)
			), 0) AS lost_matches,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, -8, 
					IF(tm.team1_id = t.id, CAST(tm.team1_score AS SIGNED) - CAST(tm.team2_score AS SIGNED), CAST(tm.team2_score AS SIGNED) - CAST(tm.team1_score AS SIGNED))
				)
			), 0) AS diff_matches,
			IFNULL(SUM(
				IF(tm.team1_id = t.id, tm.team1_games, tm.team2_games)
			), 0) AS won_games,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, 16, 
					IF(tm.team1_id = t.id, tm.team2_games, tm.team1_games)
				)
			), 0) AS lost_games,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, -16, 
					IF(tm.team1_id = t.id, CAST(tm.team1_games AS SIGNED) - CAST(tm.team2_games AS SIGNED), CAST(tm.team2_games AS SIGNED) - CAST(tm.team1_games AS SIGNED))
				)
			), 0) AS diff_games,
			IFNULL(SUM(
				IF(tm.team1_id = t.id, tm.team1_points, tm.team2_points)
			), 0) AS won_score,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, 336, 
					IF(tm.team1_id = t.id, tm.team2_points, tm.team1_points)
				)
			), 0) AS lost_score,
			IFNULL(SUM(
				IF(tm.team1_score = 0 AND tm.team2_score = 0, -336, 
					IF(tm.team1_id = t.id, CAST(tm.team1_points AS SIGNED) - CAST(tm.team2_points AS SIGNED), CAST(tm.team2_points AS SIGNED) - CAST(tm.team1_points AS SIGNED))
				)
			), 0) AS diff_score,
			# (x * won_teammatches + y * drawn_teammatches) AS points
			SUM(
				IF(tm.team1_id = t.id, 
					CASE WHEN tm.team1_score > tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END,
					CASE WHEN tm.team1_score < tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END
				)
			) AS points,
			SUM(
				IF(tm.team1_id = t.id, 
					CASE WHEN tm.team1_score < tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END,
					CASE WHEN tm.team1_score > tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END
				)
			) AS lost_points,
			# ifnull due to possible division by zero
			IFNULL(ROUND(
			SUM(
				IF(tm.team1_id = t.id, 
					CASE WHEN tm.team1_score > tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END,
					CASE WHEN tm.team1_score < tm.team2_score THEN :WIN_POINTS WHEN tm.team1_score = tm.team2_score AND tm.team1_score <> 0 THEN :DRAW_POINTS ELSE 0 END
				)
			)
			/ 
			(GREATEST(:WIN_POINTS, :DRAW_POINTS) * 
				# see number_teammatches
				SUM(IF((tm.annulled AND NOT t.withdrawn) OR tm.team1_score IS NULL OR tm.team2_score IS NULL, 0, tm.id IS NOT NULL))
			) * 100, 
			1), 0) AS points_percent
		FROM teams t
		INNER JOIN leagues l ON t.league_id = l.id
		INNER JOIN clubs c ON t.club_id = c.id ' .
		$homeawayJoin . '
		WHERE 
			t.league_id = :LEAGUE_ID
			AND (NOT tm.annulled OR t.withdrawn) '
			. $stageCondition .	'
		GROUP BY t.id
		ORDER BY 
			t.withdrawn ASC, 
			points DESC,  # points_percent DESC wenn Liga-uebergreifend
			won_matches DESC, 
			diff_games DESC, 
			diff_score DESC';

		$conn = $this->getEntityManager()->getConnection();

		$sqlParams['LEAGUE_ID'] = $leagueId;
		$sqlParams['WIN_POINTS'] = $winPoints;
		$sqlParams['DRAW_POINTS'] = $drawPoints;

		return $conn->fetchAll($query, $sqlParams);

	}


	public function findHierarchicallyPrevious($league)
	{		
		$qb = $this->createQueryBuilder('l');
		$qb->select(array('l','r','o'))
			->innerJoin('l.Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->where($qb->expr()->neq('l.id', ':league_id'))
			->andWhere($qb->expr()->eq('l.tournament_id', ':tournament_id'))
			->andWhere('(l.class_level = :class_level AND l.division < :division) OR l.class_level < :class_level')
			->andWhere($qb->expr()->neq('l.class_level', '255'))
			->orderBy('l.class_level', 'DESC')
			->addOrderBy('l.division', 'DESC')
			->setParameter('league_id', $league->getId())
			->setParameter('tournament_id', $league->getTournamentId())
			->setParameter('class_level', $league->getClassLevel())
			->setParameter('division', $league->getDivision())
			->setMaxResults(1);

		try {
			return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			return null;
		}
	}

	public function findHierarchicallyNext($league)
	{		
		$qb = $this->createQueryBuilder('l');
		$qb->select(array('l','r','o'))
			->innerJoin('l.Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->where($qb->expr()->neq('l.id', ':league_id'))
			->andWhere($qb->expr()->eq('l.tournament_id', ':tournament_id'))
			->andWhere('(l.class_level = :class_level AND l.division > :division) OR l.class_level > :class_level')
			->andWhere($qb->expr()->neq('l.class_level', '255'))
			->orderBy('l.class_level', 'ASC')
			->addOrderBy('l.division', 'ASC')
			->setParameter('league_id', $league->getId())
			->setParameter('tournament_id', $league->getTournamentId())
			->setParameter('class_level', $league->getClassLevel())
			->setParameter('division', $league->getDivision())
			->setMaxResults(1);

		try {
			return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			return null;
		}
	}
}