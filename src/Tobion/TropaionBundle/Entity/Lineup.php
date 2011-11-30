<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Lineup
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
	 * @var integer $season_round
	 *
	 * @ORM\Column(type="smallint")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $season_round;

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
	 * @return integer $teamId
	 */
	public function getTeamId()
	{
		return $this->team_id;
	}

	/**
	 * Set season_round
	 *
	 * @param smallint $seasonRound
	 */
	public function setSeasonRound($seasonRound)
	{
		$this->season_round = $seasonRound;
	}

	/**
	 * Get season_round
	 *
	 * @return smallint $seasonRound
	 */
	public function getSeasonRound()
	{
		return $this->season_round;
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
	 * @return integer $athleteId
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
	 * @return smallint $position
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Set Team
	 *
	 * @param Tobion\TropaionBundle\Entity\Team $team
	 */
	public function setTeam(\Tobion\TropaionBundle\Entity\Team $team)
	{
		$this->Team = $team;
	}

	/**
	 * Get Team
	 *
	 * @return Tobion\TropaionBundle\Entity\Team $team
	 */
	public function getTeam()
	{
		return $this->Team;
	}

	/**
	 * Set Athlete
	 *
	 * @param Tobion\TropaionBundle\Entity\Athlete $athlete
	 */
	public function setAthlete(\Tobion\TropaionBundle\Entity\Athlete $athlete)
	{
		$this->Athlete = $athlete;
	}

	/**
	 * Get Athlete
	 *
	 * @return Tobion\TropaionBundle\Entity\Athlete $athlete
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