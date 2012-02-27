<?php

namespace Tobion\TropaionBundle\Controller;

use Tobion\TropaionBundle\Entity;

use PDO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProfileController extends Controller
{

	/**
	 * @Route("/@{id}/{firstName}-{lastName}.{_format}",
	 *     name="profile_athlete",
	 *     defaults={"_format" = "html"},
	 *     requirements={"firstName" = ".+", "lastName" = ".+", "id" = "\d+", "_format" = "html|atom"}
	 * ) 
	 * @Template()
	 */
	public function athleteAction($firstName, $lastName, $id)
	{
		$em = $this->getDoctrine()->getEntityManager();

		$qb = $em->createQueryBuilder();
		$qb->select(array('a', 'c'))
			->from('TobionTropaionBundle:Athlete', 'a')
			->leftJoin('a.Club', 'c')
			->where('a.id = :id')
			->setParameter('id', $id);

		try {
			$athlete = $qb->getQuery()->getSingleResult();
		} catch (\Doctrine\Orm\NoResultException $e) {
			throw $this->createNotFoundException('Athlete not found.');
		}


		if ($athlete->getFirstName() != $firstName || $athlete->getLastName() != $lastName) {
			return $this->redirect($this->generateUrl('profile_athlete', $athlete->routingParams()), 301);
		}

		$user = $em->getRepository('TobionTropaionBundle:User')->findOneBy(
			array('Athlete' => $athlete->getId())
		);


		$qb = $em->createQueryBuilder();
		$qb->select(array('lu', 't', 'l', 'r'))
			->from('TobionTropaionBundle:Lineup', 'lu')
			->innerJoin('lu.Team', 't')
			->innerJoin('t.League', 'l')
			->innerJoin('l.Tournament', 'r')
			->where($qb->expr()->eq('lu.Athlete', ':athlete_id'))
			->addOrderBy('r.end_date', 'DESC')
			->addOrderBy('r.id', 'ASC')
			->addOrderBy('lu.stage', 'ASC')
			->setParameter('athlete_id', $athlete->getId());

		$lineups = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

		$tournamentLineups = array();

		foreach ($lineups as $lineup) {
			$tl =& $tournamentLineups[$lineup->getTeam()->getLeague()->getTournament()->getId()];
			$tl['tournament'] = $lineup->getTeam()->getLeague()->getTournament();
			$tl['lineups'][$lineup->getStage()] = $lineup;
			$tl['lineupChanged'] = Entity\Lineup::lineupChanged($tl['lineups']);
		}


		/*
		 *	Version with partial selection when using array tree hydration
			$qb->select('partial h.{id, discipline, post_rating}, 
				partial m.{id}, 
				g, 
				partial tm.{id, performed_at, team1_score, team2_score},
				partial c1.{id, code},
				partial c2.{id, code},
				partial t1.{id, team_number},
				partial t2.{id, team_number},
				partial l.{id, class_abbr, division},
				partial r.{id, short_name, slug},
				partial o.{id, slug},
				partial t1p1.{id, first_name, last_name},
				partial t1p2.{id, first_name, last_name},
				partial t2p1.{id, first_name, last_name},
				partial t2p2.{id, first_name, last_name}')
		*/

		$qb = $em->createQueryBuilder();
		$qb->select(array(
				'h.discipline', 'h.post_rating',
				'tm.performed_at', 'tm.team1_score tm_team1_score', 'tm.team2_score tm_team2_score',
				'm.id match_id',
				'c1.code team1_club', 't1.team_number team1_number',
				'c2.code team2_club', 't2.team_number team2_number',
				'l.class_abbr', 'l.division',
				'r.short_name', 'r.slug tournament', 'o.slug owner',
				'g.annulled g_annulled', 'g.team1_score g_team1_score', 'g.team2_score g_team2_score',
				't1p1.first_name t1p1_first_name', 't1p1.last_name t1p1_last_name',
				't1p2.first_name t1p2_first_name', 't1p2.last_name t1p2_last_name',
				't2p1.first_name t2p1_first_name', 't2p1.last_name t2p1_last_name',
				't2p2.first_name t2p2_first_name', 't2p2.last_name t2p2_last_name'
			))
			->from('TobionTropaionBundle:Ratinghistory', 'h')
			->innerJoin('h.Match', 'm')
			->innerJoin('m.Teammatch', 'tm')
			->innerJoin('tm.Team1', 't1')
			->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
			->innerJoin('t2.Club', 'c2')
			->innerJoin('t1.League', 'l')
			->innerJoin('l.Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->leftJoin('m.Games', 'g')
			->leftJoin('m.Team1_Player', 't1p1')
			->leftJoin('m.Team1_Partner', 't1p2')
			->leftJoin('m.Team2_Player', 't2p1')
			->leftJoin('m.Team2_Partner', 't2p2')
			->where('h.Athlete = :id')
			->orderBy('tm.performed_at', 'ASC')
			->addOrderBy('m.id', 'ASC') // needed for building the ratings array
			->addOrderBy('g.game_sequence', 'ASC')
			->setParameter('id', $id);

		$ratinghistory = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);

		$match = array();

		// build all match result texts using games
		foreach ($ratinghistory as $rating) {
			if (!isset($match[$rating['match_id']])) {
				$match[$rating['match_id']]['annulled'] = '';
				$match[$rating['match_id']]['effective'] = '';
			}

			if ($rating['g_annulled']) {
				$match[$rating['match_id']]['annulled'] .= $rating['g_team1_score'] . ':' . $rating['g_team2_score'] . ' ';
			} else {
				$match[$rating['match_id']]['effective'] .= $rating['g_team1_score'] . ':' . $rating['g_team2_score'] . ' ';		
			}
		}

		$router = $this->get('router');
		$lastMatchId = false;

		$singlesRatings = array();
		$doublesRatings = array();
		$mixedRatings = array();

		$singlesRatingMin = null;
		$singlesRatingMinAt = null;

		$ratingStats = array_fill_keys(
			array('singles', 'doubles', 'mixed'), 
			array('minRating' => null, 'minAt' => null, 'maxRating' => null, 'maxAt' => null)
		);

		foreach ($ratinghistory as $rating) {
			if ($lastMatchId === $rating['match_id']) {
				continue;
			}

			$lastMatchId = $rating['match_id'];

			$match[$rating['match_id']]['effective'] = trim($match[$rating['match_id']]['effective']);
			$match[$rating['match_id']]['annulled'] = trim($match[$rating['match_id']]['annulled']);

			$rating['post_rating'] = (int) $rating['post_rating'];

			$r = array(
				// number of seconds since 1970-01-01 (PHP/MySQL) to number of milliseconds (JavaScript Date.UTC)
				'x' => strtotime($rating['performed_at']) * 1000, 
				'y' => $rating['post_rating'],
				'tournament' => $rating['short_name'],
				'league' => $rating['division'] ? 
					$rating['class_abbr'] . ' ' . $rating['division'] :
					$rating['class_abbr'],
				'teammatch' => $rating['team1_club'] . ' ' . $rating['team1_number'] .
					' – ' . $rating['team2_club'] . ' ' . $rating['team2_number'] .
					' = ' . $rating['tm_team1_score'] . ':' . $rating['tm_team2_score'],
				'match' => $rating['t1p1_last_name'] . 
					($rating['t1p1_last_name'] && $rating['t1p2_last_name'] ? '/' : '') . 
					$rating['t1p2_last_name'] .
					' – ' . 
					$rating['t2p1_last_name'] . 
					($rating['t2p1_last_name'] && $rating['t2p2_last_name'] ? '/' : '') . 
					$rating['t2p2_last_name'] .
					' = ' . 
					($match[$rating['match_id']]['annulled'] ?: $match[$rating['match_id']]['effective']),
				'url' => $router->generate('tournament_teammatch', array(
					'owner' => $rating['owner'],
					'tournament' => $rating['tournament'],
					'league' => $rating['division'] ? 
						$rating['class_abbr'] . '-' . $rating['division'] :
						$rating['class_abbr'],
					'team1Club' => $rating['team1_club'],
					'team1Number' => $rating['team1_number'],
					'team2Club' => $rating['team2_club'],
					'team2Number' => $rating['team2_number']
				)) . '#match-' . $rating['match_id']
			);

			switch ($rating['discipline']) {
				case 'singles':
					$singlesRatings[] = $r;
					break;
				case 'doubles':
					$doublesRatings[] = $r;
					break;
				case 'mixed':
					$mixedRatings[] = $r;
					break;
			}

			$disciplineStats =& $ratingStats[$rating['discipline']];

			if ($disciplineStats['minRating'] === null || 
				$disciplineStats['minRating'] > $rating['post_rating']) 
			{
				$disciplineStats['minRating'] = $rating['post_rating'];
				$disciplineStats['minAt'] = new \DateTime($rating['performed_at']);
			}

			if ($disciplineStats['maxRating'] === null || 
				$disciplineStats['maxRating'] < $rating['post_rating']) 
			{
				$disciplineStats['maxRating'] = $rating['post_rating'];
				$disciplineStats['maxAt'] = new \DateTime($rating['performed_at']);
			}

		}

		return array(
			'athlete' => $athlete,
			'user' => $user,
			'tournamentLineups' => $tournamentLineups,
			'singlesRatings' => $singlesRatings,
			'doublesRatings' => $doublesRatings,
			'mixedRatings' => $mixedRatings,
			'ratingStats' => $ratingStats
		);
	}


	/**
	 * @Route("/clubs/{club}.{_format}",
	 *     name="profile_club",
	 *     defaults={"_format" = "html"},
	 *     requirements={"club" = ".+", "_format" = "html|atom"}
	 * ) 
	 * @Template()
	 */
	public function clubAction($club)
	{

	}


	/**
	 * @Route("/venues/{venue}", name="profile_venue") 
	 * @Template()
	 */
	public function venueAction($venue)
	{

	}


	/**
	 * @Route("/{user}",name="profile_user") 
	 * @Template()
	 */
	public function userAction($user)
	{

	}

}
