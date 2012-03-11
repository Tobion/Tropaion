<?php

namespace Tobion\TropaionBundle\Controller;

use Tobion\TropaionBundle\Entity;

use PDO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomeController extends Controller
{

	/**
	 * @Route("/", name="home") 
	 * @Template()
	 */
	public function homeAction()
	{
		// aktuelle Turniere mit Statistiken wie Anzahl der Spieler ...
		// Top Liga (BBL): Tabelle erste 3
		// Spannendste Liga: ...
		// Männer Top 10, Frauen Top 10, Newcomer (Top der Woche)

		return array();
	}

	/**
	 * @Route("/tournaments", name="home_tournaments") 
	 * @Template()
	 */
	public function tournamentsAction()
	{

	}

	/**
	 * @Route("/match-browser", name="home_matches") 
	 * @Template()
	 */
	public function matchesAction()
	{

	}

	/**
	 * @Route("/clubs", name="home_clubs") 
	 * @Template()
	 */
	public function clubsAction()
	{

	}

	/**
	 * @Route("/venues", name="home_venues")
	 * @Template()
	 */
	public function venuesAction()
	{

	}

	/**
	 * @Route("/rankings", name="home_rankings") 
	 * @Template()
	 */
	public function rankingsAction()
	{
		return array();
	}

	/**
	 * @Route("/search", name="home_search") 
	 * @Template()
	 */
	public function searchAction()
	{

	}

	/**
	 * @Route("/help", name="home_help")
	 * @Template()
	 */
	public function helpAction()
	{

	}

	/**
	 * @Route("/contact", name="home_contact")
	 * @Template()
	 */
	public function contactAction()
	{

	}

	/**
	 * @Route("/privacy", name="home_privacy")
	 * @Template()
	 */
	public function privacyAction()
	{

	}

	/**
	 * @Route("/developer", name="home_developer")
	 * @Template()
	 */
	public function developerAction()
	{

	}

	/**
	 * @Route("/imprint", name="home_imprint")
	 * @Template()
	 */
	public function imprintAction()
	{

	}

}
