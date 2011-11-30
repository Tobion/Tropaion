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
	 * @Route("/rankings", name="home_rankings") 
	 * @Template()
	 */
	public function rankingsAction()
	{

	}

	/**
	 * @Route("/search", name="home_search") 
	 * @Template()
	 */
	public function searchAction()
	{

	}

}
