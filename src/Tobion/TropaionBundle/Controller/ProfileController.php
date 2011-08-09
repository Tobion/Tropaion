<?php

namespace Tobion\TropaionBundle\Controller;

use Tobion\TropaionBundle\Entity;

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
	
		$qb = $em->createQueryBuilder();
		$qb->select(array('r.discipline','r.rating','r.created_at'))
			->from('TobionTropaionBundle:Ratinghistory', 'r')
			->where('r.athlete_id = :id')
			//->andWhere('r.discipline = :discipline')
			->orderBy('r.created_at', 'ASC')
			->setParameter('id', $id)
			//->setParameter('discipline', 'doubles')
			;
		
		$ratinghistory = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);

		$groupedRatings = array();
		foreach ($ratinghistory as $rating) {
			$groupedRatings[$rating['created_at']][$rating['discipline'].'_rating'] = $rating['rating'];
		}
		
		$ratinghistory = array();
		foreach ($groupedRatings as $date => $ratings) {
			$ratinghistory[] = array_merge(
				//array('singles_rating' => false, 'doubles_rating' => false, 'mixed_rating' => false), 
				$ratings, 
				array('created_at' => $date)
			);
		}
		
		return array(
			'athlete' => $athlete,
			'ratinghistory' => $ratinghistory
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
