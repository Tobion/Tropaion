<?php
/**
 * Tropaion - A social and semantic sport results service
 *
 * @copyright (c) Tobias Schultze <http://www.tobion.de>
 *
 * @link http://tropaion.tobion.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3
 */

namespace Tobion\TropaionBundle\Controller;

use Tobion\TropaionBundle\Entity;
use Tobion\TropaionBundle\Form\BadmintonTeammatchType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Tournament Controller
 *
 * @author Tobias Schultze <http://www.tobion.de>
 *
 * @Route("/{owner}/{tournament}")
 */
class TournamentController extends Controller
{

	/**
	 * @Route("/teammatch/create")
	 */
	public function createAction()
	{
		$teammatch = new Entity\Teammatch();
		$teammatch->setPerformedAt(new \DateTime());
		$teammatch->setDescription('Lorem ipsum dolor');

		$team1 = new Entity\Team();
		$team2 = new Entity\Team();

		$athlete1 = new Entity\Athlete();
		$athlete1->setFirstName('Tobias');
		$athlete2 = new Entity\Athlete();
		$athlete2->setFirstName('Kehrberg');

		$club1 = new Entity\Club();
		$club1->setCode('KWO');
		$club1->setName('Kabelwerk');
		$club1->setContactPerson($athlete1);
		$club2 = new Entity\Club();
		$club2->setCode('EBT');
		$club2->setName('Empor');
		$club2->setContactPerson($athlete2);

		$team1->setClub($club1);
		$team2->setClub($club2);
		$teammatch->setTeam1($team1);
		$teammatch->setTeam2($team2);

		$match = new Entity\Match();
		$teammatch->addMatches($match);



		$em = $this->getDoctrine()->getEntityManager();
		$em->persist($athlete1);
		$em->persist($athlete2);
		$em->persist($club1);
		$em->persist($club2);
		$em->persist($team1);
		$em->persist($team2);
		$em->persist($match);
		$em->persist($teammatch);
		$em->flush();

		return new Response('Created teammatch id ' . $teammatch->getId());
	}

	private function getTournament()
	{
		$em = $this->getDoctrine()->getEntityManager();

		$qb = $em->createQueryBuilder();
		$qb->select(array('r','o'))
			->from('TobionTropaionBundle:Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->where($qb->expr()->eq('o.slug', '?0'))
			->andWhere($qb->expr()->eq('r.slug', '?1'))
			->setParameters(array(
				$this->getRequest()->get('owner'), 
				$this->getRequest()->get('tournament')
			));

		try {
			return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			throw $this->createNotFoundException('Tournament not found.');
		}
	}

	/**
	 * @Route(".{_format}", 
	 *     name="tournament",
	 *     defaults={"_format" = "html"},
	 *     requirements={"_format" = "html"}
	 * ) 
	 * @Template()
	 * 
	 * .html suffix for identifying the page about the tournament in RDF
	 */
	public function tournamentAction()
	{
		$tournament = $this->getTournament();

		return array(
			'tournament' => $tournament,
		);

	}

	/**
	 * @Route("/leagues", name="tournament_leagues_index") 
	 * @Template()
	 */
	public function leaguesIndexAction()
	{
		$tournament = $this->getTournament();

		$rep = $this->getDoctrine()->getRepository('TobionTropaionBundle:League');
		$leagueHierarchy = $rep->getTournamentLeaguesHierarchically($tournament);

		$nbLeagueClasses = count($leagueHierarchy);
		$nbLeagueDivisions = count($leagueHierarchy, COUNT_RECURSIVE) - $nbLeagueClasses;

		return array(
			'tournament' => $tournament,
			'leagueHierarchy' => $leagueHierarchy,
			'nbLeagueClasses' => $nbLeagueClasses,
			'nbLeagueDivisions' => $nbLeagueDivisions,
		);

	}

	/**
	 * @Route("/matches", name="tournament_matches_index") 
	 * @Template()
	 */
	public function matchesIndexAction()
	{
		$tournament = $this->getTournament();

		return array(
			'tournament' => $tournament,
		);

	}

	/**
	 * @Route("/teams", name="tournament_teams_index") 
	 * @Template()
	 */
	public function teamsIndexAction()
	{
		$tournament = $this->getTournament();

		$em = $this->getDoctrine()->getEntityManager();

		$qb = $em->createQueryBuilder();
		$qb->select(array('t','c','l'))
			->from('TobionTropaionBundle:Team', 't')
			->innerJoin('t.Club', 'c')
			->innerJoin('t.League', 'l')
			->where($qb->expr()->eq('l.Tournament', ':tournament_id'))
			->andWhere($qb->expr()->neq('l.class_level', ':class_level'))
			->orderBy('c.name', 'ASC')
			->addOrderBy('t.team_number', 'ASC')
			->setParameter('tournament_id', $tournament->getId())
			->setParameter('class_level', 255);

		$teams = $qb->getQuery()->getResult();

		$query = 'SELECT COUNT(DISTINCT(IF(l.class_level = 255, NULL, t.id))) AS nbTeams,
				COUNT(*) AS nbAthletes,
				SUM(a.gender = "male") AS nbMen, SUM(a.gender = "female") AS nbWomen,
				SUM(l.class_level = 255) AS nbSubstitutes
			FROM lineups lu
			INNER JOIN athletes a ON a.id = lu.athlete_id
			INNER JOIN teams t ON t.id = lu.team_id
			INNER JOIN clubs c ON c.id = t.club_id
			INNER JOIN leagues l ON l.id = t.league_id
			WHERE l.tournament_id = :TOURNAMENT AND lu.stage = :STAGE
			GROUP BY c.id
			ORDER BY c.name ASC';

		$conn = $em->getConnection();
		$stmt = $conn->prepare($query);

		$sqlParams = array();
		$sqlParams['STAGE'] = 1;
		$sqlParams['TOURNAMENT'] = $tournament->getId();

		$stmt->execute($sqlParams);
		$clubStats = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		$nbClubs = count($clubStats);
		$nbTeams = count($teams);
		$nbAthletes = $nbMen = $nbWomen = $nbSubstitutes = 0;

		$clubTeams = array();
		foreach ($teams as $team) {
			$c =& $clubTeams[$team->getClub()->getId()];
			$c['teams'][$team->getId()] = $team;
			if (!isset($c['club'])) {
				$c['club'] = $team->getClub();
				$c += array_shift($clubStats);
				$nbAthletes += $c['nbAthletes'];
				$nbMen += $c['nbMen'];
				$nbWomen += $c['nbWomen'];
				$nbSubstitutes += $c['nbSubstitutes'];
			}
		}

		return array(
			'tournament' => $tournament,
			'clubTeams' => $clubTeams,
			'nbClubs' => $nbClubs,
			'nbTeams' => $nbTeams,
			'nbAthletes' => $nbAthletes,
			'nbMen' => $nbMen,
			'nbWomen' => $nbWomen,
			'nbSubstitutes' => $nbSubstitutes,
		);

	}

	/**
	 * @Route("/players", name="tournament_athletes_index") 
	 * @Template()
	 */
	public function athletesIndexAction()
	{
		$tournament = $this->getTournament();

		return array(
			'tournament' => $tournament,
		);

	}

	/**
	 * @Route("/{league}.{_format}", 
	 *     name="tournament_league",
	 *     defaults={"_format" = "html"},
	 *     requirements={"_format" = "html|json"}
	 * ) 
	 * @Template()
	 */
	public function leagueAction()
	{
		$tournament = $this->getTournament();

		$em = $this->getDoctrine()->getEntityManager();
		$rep = $this->getDoctrine()->getRepository('TobionTropaionBundle:League');

		$leagueParts = \Tobion\TropaionBundle\Entity\League::parseSlug($this->getRequest()->get('league'));

		$qb = $em->createQueryBuilder();
		$qb->select(array('l'))
			->from('TobionTropaionBundle:League', 'l')
			->where($qb->expr()->eq('l.Tournament', '?0'))
			->andWhere($qb->expr()->eq('l.class_abbr', '?1'))
			->andWhere($qb->expr()->eq('l.division', '?2'))
			->setParameters(array(
				$tournament->getId(), 
				$leagueParts['classAbbr'], 
				$leagueParts['division']
			));

		try {
			$league = $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			throw $this->createNotFoundException('League not found.');
		}

		$round = intval($this->getRequest()->get('round'));
		$homeaway = intval($this->getRequest()->get('homeaway'));

		$standings = $rep->getStandings(
			$league->getId(), 
			$round,
			$homeaway
		);

		if ($this->getRequest()->get('_format') == 'json') {
			return new Response(json_encode($standings));
		}

		$qb = $em->createQueryBuilder();
		$qb->select(array('tm','t1','t2','c1','c2'))
			->from('TobionTropaionBundle:Teammatch', 'tm')
			->innerJoin('tm.Team1', 't1')
			->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
			->innerJoin('t2.Club', 'c2')
			->where($qb->expr()->eq('t1.League', ':id'))
			->setParameter('id', $league->getId());

		$teammatches = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

		$teammatchesCrossover = array();
		foreach ($teammatches as $teammatch) {
			// set league which is not hydrated but needed for teammatches methods 
			$teammatch->setLeague($league);
			$teammatchesCrossover[$teammatch->getTeam1()->getId()][$teammatch->getTeam2()->getId()] = $teammatch;
		}

		$previousLeague = $rep->findHierarchicallyPrevious($league);
		$nextLeague = $rep->findHierarchicallyNext($league);

		return array(
			'tournament' => $tournament,
			'league' => $league,
			'standings' => $standings,
			'teammatches' => $teammatches,
			'teammatchesCrossover' => $teammatchesCrossover,
			'round' => $round,
			'homeaway' => $homeaway,
			'previousLeague' => $previousLeague,
			'nextLeague' => $nextLeague
		);
	}

	/**
	 * @Route("/{league}/{team1Club}-{team1Number}_{team2Club}-{team2Number}.{_format}", 
	 *     name="tournament_teammatch",
	 *     defaults={"_format" = "html"},
	 *     requirements={"team1Club" = ".+", "team1Number" = "\d+", "team2Number" = "\d+", "team2Club" = ".+", "_format" = "html|basic|json|ics"}
	 * ) 
	 * // Route("/{owner}/{tournament}/{league}/{teammatch}", name="teammatch_show") 
	 * @Template()
	 */
	public function teammatchAction()
	{
		$tournament = $this->getTournament();

		$em = $this->getDoctrine()->getEntityManager();
		$rep = $this->getDoctrine()->getRepository('TobionTropaionBundle:Teammatch');

		/*
		$teammatch = $this->getDoctrine()
			->getRepository('TobionTropaionBundle:Teammatch')
			->find($id);


		$query = $em->createQuery(
			'SELECT tm FROM TobionTropaionBundle:Teammatch tm JOIN tm.Matches m WHERE tm.id = :id'
		)->setParameter('id', $id);
		$teammatch = $query->getArrayResult();
		*/

		/*
		$qb = $em->createQueryBuilder();
		$qb->select(array('tm','t1','t2','c1','c2','l','r','v','m','y','g','t1p1','t1p2','t2p1','t2p2'))
			->from('TobionTropaionBundle:Teammatch', 'tm')
			->innerJoin('tm.Team1', 't1')
			->leftJoin('tm.Matches', 'm')
			->where($qb->expr()->eq('tm.id', ':id'))
			->setParameter('id', $id);

		$teammatch = $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		*/

		$hydrateMode = $this->getRequest()->get('_format') == 'json' ? \Doctrine\ORM\Query::HYDRATE_ARRAY : \Doctrine\ORM\Query::HYDRATE_OBJECT;

		$leagueParts = \Tobion\TropaionBundle\Entity\League::parseSlug($this->getRequest()->get('league'));

		$teammatch = $rep->findByParamsJoinedAll(
			$this->getRequest()->get('owner'),
			$this->getRequest()->get('tournament'),
			$leagueParts['classAbbr'],
			$leagueParts['division'],
			$this->getRequest()->get('team1Club'),
			$this->getRequest()->get('team1Number'),
			$this->getRequest()->get('team2Club'),
			$this->getRequest()->get('team2Number'),
			$hydrateMode
		);

		if (!$teammatch) {
			throw $this->createNotFoundException('No teammatch found.');
		}


		if ($this->getRequest()->get('_format') == 'json') {
			// TODO exclude athlete birthday and other private data (email, password etc.) from array before encoding it

			$response = new Response(json_encode($teammatch));
			// $response->headers->set('Content-Type', 'application/json');
			return $response;
		}

		$previousTeammatch = $rep->findChronologicallyPrevious($teammatch);
		$nextTeammatch = $rep->findChronologicallyNext($teammatch);

		$returnTeammatch = $rep->findReturnTeammatch($teammatch);

		return array(
			'tournament' => $tournament,
			'teammatch' => $teammatch,
			'previousTeammatch' => $previousTeammatch,
			'nextTeammatch' => $nextTeammatch,
			'returnTeammatch' => $returnTeammatch
		);
	}


	/**
	 * @Route("/teams/{club}-{teamNumber}.{_format}",
	 *     name="tournament_team",
	 *     defaults={"_format" = "html"},
	 *     requirements={"club" = ".+", "teamNumber" = "\d+", "_format" = "html|atom"}
	 * ) 
	 * @Template()
	 */
	public function teamAction()
	{

		$tournament = $this->getTournament();

		$em = $this->getDoctrine()->getEntityManager();

		$qb = $em->createQueryBuilder();
		$qb->select(array('t','c','l','lu','a'))
			->from('TobionTropaionBundle:Team', 't')
			->innerJoin('t.Club', 'c')
			->innerJoin('t.League', 'l')
			->leftJoin('t.Lineups', 'lu')
			->leftJoin('lu.Athlete', 'a')
			->where($qb->expr()->eq('l.Tournament', '?0'))
			->andWhere($qb->expr()->eq('c.code', '?1'))
			->andWhere($qb->expr()->eq('t.team_number', '?2'))
			->setParameters(array(
				$tournament->getId(), 
				$this->getRequest()->get('club'), 
				$this->getRequest()->get('teamNumber')
			));

		try {
			$team = $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			throw $this->createNotFoundException('Team not found.');
		}

		$qb = $em->createQueryBuilder();
		$qb->select(array('tm','t1','t2','c1','c2','v'))
			->from('TobionTropaionBundle:Teammatch', 'tm')
			->innerJoin('tm.Team1', 't1')
			->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
			->innerJoin('t2.Club', 'c2')
			->innerJoin('tm.Venue', 'v')
			->where('tm.Team1 = :id OR tm.Team2 = :id')
			->orderBy('tm.performed_at', 'ASC')
			->setParameter('id', $team->getId());

		$teammatches = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

		foreach ($teammatches as $teammatch) {
			$teammatch->transformToTeamView($team);
		}

		return array(
			'tournament' => $tournament,
			'team' => $team,
			'teammatches' => $teammatches,
			'lineupChanged' => $team->hasLineupChanged(),
			'hasSecondRoundLineup' => $team->hasSecondRoundLineup()
		);

	} 


	/**
	 * @Route("/clubs/{club}",
	 *     name="tournament_club",
	 *     requirements={"club" = ".+"}
	 * ) 
	 * @Template()
	 */
	public function clubAction()
	{
		// Alle Mannschaften + Aufstellungen + Ersatzspieler

		$tournament = $this->getTournament();

		$em = $this->getDoctrine()->getEntityManager();

		$qb = $em->createQueryBuilder();
		$qb->select(array('c','t','l','lu','a'))
			->from('TobionTropaionBundle:Club', 'c')
			->innerJoin('c.Teams', 't')
			->innerJoin('t.League', 'l')
			->leftJoin('t.Lineups', 'lu')
			->leftJoin('lu.Athlete', 'a')
			->where($qb->expr()->eq('l.Tournament', '?0'))
			->andWhere($qb->expr()->eq('c.code', '?1'))
			->setParameters(array(
				$tournament->getId(), 
				$this->getRequest()->get('club')
			));

		try {
			$club = $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			throw $this->createNotFoundException('Club in tournament not found.');
		}

		$qb = $em->createQueryBuilder();
		$qb->select(array('tm','t1','t2','c1','c2','v'))
			->from('TobionTropaionBundle:Teammatch', 'tm')
			->innerJoin('tm.Team1', 't1')
			->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
			->innerJoin('t2.Club', 'c2')
			->innerJoin('t1.League', 'l')
			->innerJoin('tm.Venue', 'v')
			->where('t1.Club = :club_id OR t2.Club = :club_id')
			->andWhere($qb->expr()->eq('l.Tournament', ':tournament_id'))
			->orderBy('tm.performed_at', 'ASC')
			->setParameter('club_id', $club->getId())
			->setParameter('tournament_id', $tournament->getId());

		$teammatches = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

		foreach ($teammatches as $teammatch) {
			$teammatch->transformToClubView($club);
		}

		return array(
			'tournament' => $tournament,
			'club' => $club,
			'teammatches' => $teammatches
		);
	}



	/**
	 * @Route("/players/@{id}/{firstName}-{lastName}",
	 *     name="tournament_athlete",
	 *     requirements={"firstName" = ".+", "lastName" = ".+", "id" = "\d+"}
	 * ) 
	 * @Template()
	 */
	public function athleteAction()
	{

		$tournament = $this->getTournament();

		$em = $this->getDoctrine()->getEntityManager();

		$athlete = $em->getRepository('TobionTropaionBundle:Athlete')->find(
			$this->getRequest()->get('id')
		);

		if (!$athlete) {
			throw $this->createNotFoundException('Athlete not found.');
		}

		/* 
			Fetch the league of both teams. Otherwise strange fatal error when trying to access one teams league.
			Usually this is not necessary, for example in clubAction(). Doctrine should recognize that the same league is already loaded.
		*/
		$qb = $em->createQueryBuilder();
		$qb->select(array('m','tm','t1','t2','c1','c2','l','l2','v','y','g','t1p1','t1p2','t2p1','t2p2','rh'))
			->from('TobionTropaionBundle:Match', 'm')
			->innerJoin('m.Teammatch', 'tm')
			->innerJoin('tm.Team1', 't1')
			->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
			->innerJoin('t2.Club', 'c2')
			->innerJoin('t1.League', 'l')
			->innerJoin('t2.League', 'l2')
			->innerJoin('tm.Venue', 'v')
			->innerJoin('m.MatchType', 'y')
			->leftJoin('m.Games', 'g')
			->leftJoin('m.Team1_Player', 't1p1')
			->leftJoin('m.Team1_Partner', 't1p2')
			->leftJoin('m.Team2_Player', 't2p1')
			->leftJoin('m.Team2_Partner', 't2p2')
			->leftJoin('m.Ratinghistory', 'rh', 'WITH', $qb->expr()->eq('rh.Athlete', ':athlete_id')) // \Doctrine\ORM\Expr\Join::WITH
			->where('m.Team1_Player = :athlete_id OR m.Team1_Partner = :athlete_id OR ' .
				'm.Team2_Player = :athlete_id OR m.Team2_Partner = :athlete_id')
			->andWhere($qb->expr()->eq('l.Tournament', ':tournament_id'))
			->orderBy('tm.performed_at', 'ASC')
			->addOrderBy('y.id', 'ASC')
			// addOrderBy game_sequence ASC is added automatically
			->setParameter('athlete_id', $athlete->getId())
			->setParameter('tournament_id', $tournament->getId());


		$matches = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);

		$qb = $em->createQueryBuilder();

		$qb->select(array('lu', 't'))
			->from('TobionTropaionBundle:Lineup', 'lu')
			->innerJoin('lu.Team', 't')
			->innerJoin('t.League', 'l')
			->where($qb->expr()->eq('lu.Athlete', ':athlete_id'))
			->andWhere($qb->expr()->eq('l.Tournament', ':tournament_id'))
			->andWhere($qb->expr()->eq('lu.stage', ':stage'))
			->setParameter('athlete_id', $athlete->getId())
			->setParameter('tournament_id', $tournament->getId());

		$query = $qb->getQuery();
		$lineupFirstRound = $query->setParameter('stage', 1)->getOneOrNullResult();
		$lineupSecondRound = $query->setParameter('stage', 2)->getOneOrNullResult();

		if (!$matches && !$lineupFirstRound && !$lineupSecondRound) {
			throw $this->createNotFoundException('Athlete not participating in tournament.');
		}

		$lineupChanged = Entity\Lineup::hasLineupChanged(array($lineupFirstRound, $lineupSecondRound));

		$wins = $draws = $losses = $winsPercent = $drawsPercent = $lossesPercent = $noFights = $myGames = $opponentGames = $myPoints = $opponentPoints = 0;
		$nbPartners = $nbTeammatches = $nbMyTeams = array();

		foreach ($matches as $match) {
			$match->transformToAthleteView($athlete);

			$wins += $match->isTeam1OriginalFallbackEffectiveWinner() ? 1 : 0;
			$draws += $match->isOriginalFallbackEffectiveDraw() ? 1 : 0;
			$losses += $match->isTeam2OriginalFallbackEffectiveWinner() ? 1 : 0;
			$noFights += $match->getNoFight() ? 1 : 0;
			$myGames += $match->getTeam1OriginalFallbackEffectiveScore() ?: 0;
			$opponentGames += $match->getTeam2OriginalFallbackEffectiveScore() ?: 0;
			$myPoints += $match->getTeam1OriginalFallbackEffectivePoints() ?: 0;
			$opponentPoints += $match->getTeam2OriginalFallbackEffectivePoints() ?: 0;
			if ($match->getTeam1Partner()) {
				$nbPartners[$match->getTeam1Partner()->getId()] = true;
			}
			$nbTeammatches[$match->getTeammatch()->getId()] = true;
			$nbMyTeams[$match->getTeammatch()->getTeam1()->getId()] = true;
		}

		if (($sum = $wins + $draws + $losses) > 0) {
			$winsPercent = round($wins / $sum * 100, 1);
			$drawsPercent = round($draws / $sum * 100, 1);
			$lossesPercent = round($losses / $sum * 100, 1);
		}

		$nbPartners = count($nbPartners);
		$nbTeammatches = count($nbTeammatches);
		$nbMyTeams = count($nbMyTeams);

		return array(
			'tournament' => $tournament,
			'athlete' => $athlete,
			'matches' => $matches,
			'lineupFirstRound' => $lineupFirstRound,
			'lineupSecondRound' => $lineupSecondRound,
			'lineupChanged' => $lineupChanged,
			'wins' => $wins,
			'draws' => $draws,
			'losses' => $losses,
			'winsPercent' => $winsPercent,
			'drawsPercent' => $drawsPercent,
			'lossesPercent' => $lossesPercent,
			'noFights' => $noFights,
			'myGames' => $myGames,
			'opponentGames' => $opponentGames,
			'myPoints' => $myPoints,
			'opponentPoints' => $opponentPoints,
			'nbPartners' => $nbPartners,
			'nbTeammatches' => $nbTeammatches,
			'nbMyTeams' => $nbMyTeams
		);

	}

	/**
	 * Adds all properties (e.g. matches and games) to the teammatch
	 * that are required to enter a result
	 *
	 * @param Entity\Teammatch $teammatch
	 */
	private function prepareTeammatchResultProperties(Entity\Teammatch $teammatch)
	{
		if (count($teammatch->getMatches()) == 0)
		{
			$em = $this->getDoctrine()->getEntityManager();

			$badmintonMatchTypes = array(1, 2, 3, 4, 5, 6, 7, 8);

			foreach ($badmintonMatchTypes as $matchTypeId) {
				$match = new Entity\Match();
				$match->setMatchType($em->getReference('TobionTropaionBundle:MatchType', $matchTypeId));
				$match->setTeammatch($teammatch);
				$teammatch->addMatches($match);
			}
		}

		foreach ($teammatch->getMatches() as $match) {
			for ($count = count($match->getEffectiveGames()); $count < 3; $count++)
			{
				$g = new Entity\Game();
				$g->setMatch($match);
				$match->addGames($g);
			}
			for ($count = count($match->getAnnulledGames()); $count < 3; $count++)
			{
				$g = new Entity\Game();
				$g->setAnnulled(true);
				$g->setMatch($match);
				$match->addGames($g);
			}
		}
	}

	/**
	 * @Route("/{league}/{team1Club}-{team1Number}_{team2Club}-{team2Number}/edit", 
	 *     name="tournament_teammatch_edit",
	 *     requirements={"team1Club" = ".+", "team1Number" = "\d+", "team2Number" = "\d+", "team2Club" = ".+"}
	 * )  
	 * @Template()
	 */
	public function teammatchEditAction()
	{
		$tournament = $this->getTournament();

		$leagueParts = Entity\League::parseSlug($this->getRequest()->get('league'));

		$rep = $this->getDoctrine()->getRepository('TobionTropaionBundle:Teammatch');

		$teammatch = $rep->findByParamsJoinedAll(
			$this->getRequest()->get('owner'),
			$this->getRequest()->get('tournament'),
			$leagueParts['classAbbr'],
			$leagueParts['division'],
			$this->getRequest()->get('team1Club'),
			$this->getRequest()->get('team1Number'),
			$this->getRequest()->get('team2Club'),
			$this->getRequest()->get('team2Number'),
			\Doctrine\ORM\Query::HYDRATE_OBJECT
		);

		if (!$teammatch) {
			throw $this->createNotFoundException('Teammatch not found.');
		}

		$this->prepareTeammatchResultProperties($teammatch);

		$form = $this->createForm(new BadmintonTeammatchType($this->getDoctrine()), $teammatch);
        
		$invalid = false;

		if ($this->getRequest()->getMethod() == 'POST') {
			$form->bind($this->getRequest());

			// TODO
			//$teammatch->setSubmittedBy();
			//
			// fixme
			// performed_at of teammatch always updated ?!

			// var_dump($form->getErrorsAsString());

			if ($form->isValid()) {
				// save the teammatch to the database
				$em = $this->getDoctrine()->getEntityManager();

				$em->persist($teammatch);
				$em->flush();

				return $this->redirect($this->generateUrl('tournament_teammatch', $teammatch->routingParams()));
			} else {
				$invalid = true;
				// $this->prepareTeammatchResultProperties($teammatch);
			}
		}

		/*
			Alle möglichen einsetzbaren Spieler in einer Mannschaft ermitteln
			Das sind alle gemeldeten Spieler in Mannschaften des gleichen Vereins in der entsprechenden Saison und Hin- bzw. Rückrunde
			Diese Sortieren nach ihrer Einsatzwahrscheinlichkeit, um bei der Auswahl die wahrscheinlich korrekten Spieler weit oben anzuzeigen
		*/

		$query = 'SELECT lu.athlete_id, a. first_name, a.last_name, a.gender, t.team_number, lu.position, l.class_level, l.class_abbr, l.division, IFNULL(at.number_teammatches_for_this_team, 0) AS num_team_activity, (lu.team_id = :TEAM_ID) AS is_team_starter
			FROM lineups lu
			INNER JOIN athletes a ON a.id = lu.athlete_id
			INNER JOIN teams t ON t.id = lu.team_id
			INNER JOIN leagues l ON l.id = t.league_id
			INNER JOIN tournaments r ON r.id = l.tournament_id
			LEFT JOIN (
				SELECT athlete_id, COUNT(DISTINCT(teammatch_id)) AS number_teammatches_for_this_team
				FROM (
					(
						SELECT DISTINCT m.team1_player_id AS athlete_id, m.teammatch_id
						FROM matches m
						INNER JOIN teammatches tm ON tm.id = m.teammatch_id
						WHERE tm.team1_id = :TEAM_ID AND m.team1_player_id IS NOT NULL
					) UNION DISTINCT (
						SELECT DISTINCT m.team1_partner_id AS athlete_id, m.teammatch_id
						FROM matches m
						INNER JOIN teammatches tm ON tm.id = m.teammatch_id
						WHERE tm.team1_id = :TEAM_ID AND m.team1_partner_id IS NOT NULL
					) UNION DISTINCT (
						SELECT DISTINCT m.team2_player_id AS athlete_id, m.teammatch_id
						FROM matches m
						INNER JOIN teammatches tm ON tm.id = m.teammatch_id
						WHERE tm.team2_id = :TEAM_ID AND m.team2_player_id IS NOT NULL
					) UNION DISTINCT (
						SELECT DISTINCT m.team2_partner_id AS athlete_id, m.teammatch_id
						FROM matches m
						INNER JOIN teammatches tm ON tm.id = m.teammatch_id
						WHERE tm.team2_id = :TEAM_ID AND m.team2_partner_id IS NOT NULL
					)
				) AS athlete_teammatches
				GROUP BY athlete_id
				) AS at ON lu.athlete_id = at.athlete_id
			WHERE  t.club_id = :CLUB_ID AND lu.stage = :STAGE AND r.season = :SEASON
			ORDER BY num_team_activity DESC, is_team_starter DESC, (t.team_number < :TEAM_NUMBER) ASC, t.team_number ASC, lu.position ASC';

		// Zuerst nach Anzahl der Einsätze in dieser Mannschaft sortieren, da die aktivsten Spieler am wahrscheinlichsten auch beim neu-einzutragenden Mannschaftsspiel beteiligt waren
		// Danach kommen die Stammspieler dieser Mannschaft
		// Stammspieler vorderer Mannschaften werden nach hinten sortiert, da sie eigentlich eh nicht in unteren Mannschaften aufgestellt werden dürfen
		// Dann nach aufsteigender Teamnummer sortieren, da ein Spieler der 3. Mannschaft eher in der 2. Mannschaft aushilft, als ein Spieler der 5. Mannschaft
		// Auch nach Position sotieren, um Stammspieler der selben Mannschaft entsprechend zu priotisieren, da Spieler an Pos. 1 eher die ersten Spiele machen (z.B. 1.HD, 1.HE)

		$conn = $this->getDoctrine()->getEntityManager()->getConnection();
		$stmt = $conn->prepare($query);

		$sqlParams = array();
		$sqlParams['STAGE'] = $teammatch->getStage();
		$sqlParams['SEASON'] = $tournament->getSeason();

		$sqlParams['TEAM_ID'] = $teammatch->getTeam1()->getId();
		$sqlParams['TEAM_NUMBER'] = $teammatch->getTeam1()->getTeamNumber();
		$sqlParams['CLUB_ID'] = $teammatch->getTeam1()->getClub()->getId();

		$stmt->execute($sqlParams);
		$club1_athletes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		$sqlParams['TEAM_ID'] = $teammatch->getTeam2()->getId();
		$sqlParams['TEAM_NUMBER'] = $teammatch->getTeam2()->getTeamNumber();
		$sqlParams['CLUB_ID'] = $teammatch->getTeam2()->getClub()->getId();

		$stmt->execute($sqlParams);
		$club2_athletes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		return array(
			'tournament' => $tournament,
			'teammatch' => $teammatch,
			'form' => $form->createView(),
			'invalid' => $invalid,
			'club1_athletes' => $club1_athletes,
			'club2_athletes' => $club2_athletes,
		);
	}

}
