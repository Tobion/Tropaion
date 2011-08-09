<?php

namespace Tobion\TropaionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TeammatchRepository
 *
 */
class TeammatchRepository extends EntityRepository
{

	public function findByIdJoinedAll($id)
    {		
		$qb = $this->createQueryBuilder('tm');
		$qb->select(array('tm','t1','t2','c1','c2','l','r','o','v','m','y','g','t1p1','t1p2','t2p1','t2p2'))
			// ->from('TobionTropaionBundle:Teammatch', 'tm')
			->innerJoin('tm.Team1', 't1')
    		->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
	    	->innerJoin('t2.Club', 'c2')
	    	->innerJoin('t1.League', 'l')
			->innerJoin('l.Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->innerJoin('tm.Venue', 'v')
	    	->leftJoin('tm.Matches', 'm')
			->leftJoin('m.MatchType', 'y')
	    	->leftJoin('m.Games', 'g')
	    	->leftJoin('m.Team1_Player', 't1p1')
	    	->leftJoin('m.Team1_Partner', 't1p2')
	    	->leftJoin('m.Team2_Player', 't2p1')
	    	->leftJoin('m.Team2_Partner', 't2p2')
			->where($qb->expr()->eq('tm.id', ':id'))
			->setParameter('id', $id);
		
		try {
			return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			return null;
		}
    }
	
	public function findByParamsJoinedAll($ownerSlug, $tournamentSlug, $classAbbr, $division, 
		$team1ClubCode, $team1Number, $team2ClubCode, $team2Number, $hydrateMode = \Doctrine\ORM\Query::HYDRATE_OBJECT)
    {		
		// Do not join lineups here because it makes the query over 10 times slower.
		// Presumably because the query already has many columns and with lineups 
		// the number of rows gets multiplied resulting in a big result set and thus doubling memory usage.
		// Instead let doctrine lazy load it when needed.
		$qb = $this->createQueryBuilder('tm');
		$qb->select(array('tm','t1','t2','c1','c2','l','r','o','v','m','y','g','t1p1','t1p2','t2p1','t2p2'))
			->innerJoin('tm.Team1', 't1')
    		->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
	    	->innerJoin('t2.Club', 'c2')
	    	->innerJoin('t1.League', 'l')
			->innerJoin('l.Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->innerJoin('tm.Venue', 'v')
	    	->leftJoin('tm.Matches', 'm')
			->leftJoin('m.MatchType', 'y')
	    	->leftJoin('m.Games', 'g')
	    	->leftJoin('m.Team1_Player', 't1p1')
	    	->leftJoin('m.Team1_Partner', 't1p2')
	    	->leftJoin('m.Team2_Player', 't2p1')
	    	->leftJoin('m.Team2_Partner', 't2p2')
			->where($qb->expr()->eq('o.slug', '?0'))
			->andWhere($qb->expr()->eq('r.slug', '?1'))
			->andWhere($qb->expr()->eq('l.class_abbr', '?2'))
			->andWhere($qb->expr()->eq('l.division', '?3'))
			->andWhere($qb->expr()->eq('c1.code', '?4'))
			->andWhere($qb->expr()->eq('t1.team_number', '?5'))
			->andWhere($qb->expr()->eq('c2.code', '?6'))
			->andWhere($qb->expr()->eq('t2.team_number', '?7'))
			->orderBy('y.id', 'ASC')
			// orderBy game_sequence ASC is added automatically
			->setParameters(array($ownerSlug, $tournamentSlug, $classAbbr, $division, $team1ClubCode, $team1Number, $team2ClubCode, $team2Number));
		
		try {
			return $qb->getQuery()->getSingleResult($hydrateMode);
		} catch (\Doctrine\Orm\NoResultException $e) {
			return null;
		}
    }
	
	public function findChronologicallyPrevious($teammatch)
    {		
		$qb = $this->createQueryBuilder('tm');
		$qb->select(array('tm','t1','t2','c1','c2','l','r','o'))
			->innerJoin('tm.Team1', 't1')
    		->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
	    	->innerJoin('t2.Club', 'c2')
			->innerJoin('t1.League', 'l')
			->innerJoin('l.Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->where($qb->expr()->eq('t1.league_id', ':league_id'))
			->andWhere('tm.performed_at <= :performed_at')
			// das ist wichtig, um sowohl sich selber als auch circulären Referenzen (a has next b und b has next a) auszuschließen
			->andWhere('NOT (tm.performed_at = :performed_at AND tm.id >= :id)')
			->orderBy('tm.performed_at', 'DESC')
			->addOrderBy('tm.id', 'DESC')
			->setParameter('league_id', $teammatch->getLeague()->getId())
			->setParameter('performed_at', $teammatch->getPerformedAt())
			->setParameter('id', $teammatch->getId())
			->setMaxResults(1);
		
		try {
			return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			return null;
		}
    }

	public function findChronologicallyNext($teammatch)
    {		
		$qb = $this->createQueryBuilder('tm');
		$qb->select(array('tm','t1','t2','c1','c2','l','r','o'))
			->innerJoin('tm.Team1', 't1')
    		->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
	    	->innerJoin('t2.Club', 'c2')
			->innerJoin('t1.League', 'l')
			->innerJoin('l.Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->where($qb->expr()->eq('t1.league_id', ':league_id'))
			->andWhere('tm.performed_at >= :performed_at')
			// das ist wichtig, um sowohl sich selber als auch circulären Referenzen (a has next b und b has next a) auszuschließen
			->andWhere('NOT (tm.performed_at = :performed_at AND tm.id <= :id)')
			->orderBy('tm.performed_at', 'ASC')
			->addOrderBy('tm.id', 'ASC')
			->setParameter('league_id', $teammatch->getLeague()->getId())
			->setParameter('performed_at', $teammatch->getPerformedAt())
			->setParameter('id', $teammatch->getId())
			->setMaxResults(1);
		
		try {
			return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			return null;
		}
    }
	
	public function findReturnTeammatch($teammatch)
    {		
		$qb = $this->createQueryBuilder('tm');
		$qb->select(array('tm','t1','t2','c1','c2','l','r','o'))
			->innerJoin('tm.Team1', 't1')
    		->innerJoin('tm.Team2', 't2')
			->innerJoin('t1.Club', 'c1')
	    	->innerJoin('t2.Club', 'c2')
			->innerJoin('t1.League', 'l')
			->innerJoin('l.Tournament', 'r')
			->innerJoin('r.Owner', 'o')
			->where($qb->expr()->eq('tm.team1_id', ':team1_id'))
			->andWhere($qb->expr()->eq('tm.team2_id', ':team2_id'))
			->setParameter('team1_id', $teammatch->getTeam2Id())
			->setParameter('team2_id', $teammatch->getTeam1Id())
			->setMaxResults(1);
		
		try {
			return $qb->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
		} catch (\Doctrine\Orm\NoResultException $e) {
			return null;
		}
    }
}