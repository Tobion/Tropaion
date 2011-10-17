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
     * @Route("/athlete/{firstName}-{lastName}_{id}.{_format}",
     *     name="profile_athlete",
     *     defaults={"_format" = "html"},
	 *     requirements={"firstName" = ".+", "lastName" = ".+", "id" = "\d+", "_format" = "html|atom"}
     * ) 
     * @Template()
     */
    public function athleteAction($firstName, $lastName, $id)
    {
		$em = $this->getDoctrine()->getEntityManager();
		$rep = $this->getDoctrine()->getRepository('TobionTropaionBundle:Ratinghistory');
	
		$athlete = $this->getDoctrine()
			->getRepository('TobionTropaionBundle:Athlete')
			->find($id);
			
		if (!$athlete) {
			throw $this->createNotFoundException('Athlete not found.');
		}
	
		/*
		$qb = $em->createQueryBuilder();
		$qb->select(array('r.discipline','r.post_rating','r.created_at'))
			->from('TobionTropaionBundle:Ratinghistory', 'r')
			->where('r.athlete_id = :id')
			//->andWhere('r.discipline = :discipline')
			->orderBy('r.created_at', 'ASC')
			->setParameter('id', $id)
			//->setParameter('discipline', 'doubles')
			;
		
		$ratinghistory = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);

		$groupedRatings = array();
		$singlesRatings = array();
		$doublesRatings = array();
		$mixedRatings = array();
		foreach ($ratinghistory as $rating) {
			$groupedRatings[$rating['created_at']][$rating['discipline'].'_rating'] = (int) $rating['post_rating'];
			
			if ($rating['discipline'] == 'singles') {
				$singlesRatings[] = array($rating['created_at'], (int) $rating['post_rating']);
			}
			
			if ($rating['discipline'] == 'doubles') {
				$doublesRatings[] = array($rating['created_at'], (int) $rating['post_rating']);
			}
			
			if ($rating['discipline'] == 'mixed') {
				$mixedRatings[] = array($rating['created_at'], (int) $rating['post_rating']);
			}
		}

		$ratinghistory = array();
		foreach ($groupedRatings as $date => $ratings) {
			$ratinghistory[] = array_merge(
				array('singles_rating' => false, 'doubles_rating' => false, 'mixed_rating' => false), 
				$ratings, 
				array('created_at' => $date)
			);
		}
		*/
		
		$query = 'SELECT UNIX_TIMESTAMP(r.created_at), r.post_rating rating
			FROM ratinghistory r
			WHERE r.athlete_id = :ATHLETE_ID AND r.discipline = :DISCIPLINE';
			// ORDER BY r.created_at ASC // not needed
	
		$conn = $this->getDoctrine()->getEntityManager()->getConnection();
		
		$stmt = $conn->prepare($query);
		
		$sqlParams = array();
		$sqlParams['ATHLETE_ID'] = $athlete->getId();
		
		$sqlParams['DISCIPLINE'] = 'singles';
		$stmt->execute($sqlParams);
		$singlesRatings = $stmt->fetchAll(PDO::FETCH_NUM);
		
		$sqlParams['DISCIPLINE'] = 'doubles';
		$stmt->execute($sqlParams);
		$doublesRatings = $stmt->fetchAll(PDO::FETCH_NUM);
		
		$sqlParams['DISCIPLINE'] = 'mixed';
		$stmt->execute($sqlParams);
		$mixedRatings = $stmt->fetchAll(PDO::FETCH_NUM);
		
		$convertRating = function(&$rating, $key) { 
			$rating = array(
				(int) $rating[0] * 1000, // number of seconds since 1970-01-01 (PHP/MySQL) to number of milliseconds (JavaScript Date.UTC)
				(int) $rating[1]
			);
		};
		array_walk($singlesRatings, $convertRating);
		array_walk($doublesRatings, $convertRating);
		array_walk($mixedRatings, $convertRating);
		
		return array(
			'athlete' => $athlete,
			// 'ratinghistory' => $ratinghistory,
			'singlesRatings' => $singlesRatings,
			'doublesRatings' => $doublesRatings,
			'mixedRatings' => $mixedRatings
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
    public function clubAction($id = null)
    {
		// Übersicht Turniere
		// Spielgemeinschaft
		// Verantwortliche
	}


	/**
     * @Route("/venues/{venue}.{_format}",
     *     name="profile_venue",
     *     defaults={"_format" = "html"},
	 *     requirements={"firstName" = ".+", "lastName" = ".+", "id" = "\d+", "_format" = "html"}
     * ) 
     * @Template()
     */
    public function venueAction($venue)
    {

	}

}
