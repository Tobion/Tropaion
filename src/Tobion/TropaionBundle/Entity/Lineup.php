<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Tobion\TropaionBundle\Entity\Athlete;
use Tobion\TropaionBundle\Entity\Team;

/**
 * Anfangsaufstellungen der Mannschaften
 *
 * @ORM\Table(name="lineups")
 * @ORM\Entity
 */
class Lineup
{
	/**
	 * @var integer $team_id
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $team_id;

	/**
	 * Hinrunde = 1, Rückrunde = 2
	 * @var integer $stage
	 *
	 * @ORM\Column(type="smallint")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $stage;

	/**
	 * @var integer $athlete_id
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $athlete_id;

	/**
	 * @var integer $position
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $position;

	/**
	 * @var Team
	 *
	 * @ORM\ManyToOne(targetEntity="Team", inversedBy="Lineups")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
	 */
	private $Team;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="athlete_id", referencedColumnName="id")
	 */
	private $Athlete;



	/**
	 * Set team_id
	 *
	 * @param integer $teamId
	 */
	public function setTeamId($teamId)
	{
		$this->team_id = $teamId;
	}

	/**
	 * Get team_id
	 *
	 * @return integer
	 */
	public function getTeamId()
	{
		return $this->team_id;
	}

	/**
	 * Set stage
	 *
	 * @param smallint $stage
	 */
	public function setStage($stage)
	{
		$this->stage = $stage;
	}

	/**
	 * Get stage
	 *
	 * @return smallint
	 */
	public function getStage()
	{
		return $this->stage;
	}

	/**
	 * Set athlete_id
	 *
	 * @param integer $athleteId
	 */
	public function setAthleteId($athleteId)
	{
		$this->athlete_id = $athleteId;
	}

	/**
	 * Get athlete_id
	 *
	 * @return integer
	 */
	public function getAthleteId()
	{
		return $this->athlete_id;
	}

	/**
	 * Set position
	 *
	 * @param smallint $position
	 */
	public function setPosition($position)
	{
		$this->position = $position;
	}

	/**
	 * Get position
	 *
	 * @return smallint
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Set Team
	 *
	 * @param Team $team
	 */
	public function setTeam(Team $team)
	{
		$this->Team = $team;
	}

	/**
	 * Get Team
	 *
	 * @return Team
	 */
	public function getTeam()
	{
		return $this->Team;
	}

	/**
	 * Set Athlete
	 *
	 * @param Athlete $athlete
	 */
	public function setAthlete(Athlete $athlete)
	{
		$this->Athlete = $athlete;
	}

	/**
	 * Get Athlete
	 *
	 * @return Athlete
	 */
	public function getAthlete()
	{
		return $this->Athlete;
	}


	public function isSubstitute()
	{
		return $this->getTeam()->getLeague()->getClassLevel() == 255;
	}
}