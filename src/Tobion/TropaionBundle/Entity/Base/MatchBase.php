<?php

namespace Tobion\TropaionBundle\Entity\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Just a test that is not used yet
 * 
 * Mapped abstract base class for matches
 * Teammatch, Match and Game could extend this to allow code reuse
 * but it's hard to find a common denominator
 * (Match does not need annulled, Game's score is nullable=false)
 * 
 * Maybe better wait for PHP 5.4 Traits
 *
 * @ORM\MappedSuperclass
 */
abstract class MatchBase
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var integer $team1_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team1_score;

	/**
	 * @var integer $team2_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team2_score;

	/**
	 * @var boolean $annulled
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $annulled = false;
	

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set team1_score
	 *
	 * @param smallint $team1Score
	 */
	public function setTeam1Score($team1Score)
	{
		$this->team1_score = $team1Score;
	}

	/**
	 * Get team1_score
	 *
	 * @return smallint
	 */
	public function getTeam1Score()
	{
		return $this->team1_score;
	}

	/**
	 * Set team2_score
	 *
	 * @param smallint $team2Score
	 */
	public function setTeam2Score($team2Score)
	{
		$this->team2_score = $team2Score;
	}

	/**
	 * Get team2_score
	 *
	 * @return smallint
	 */
	public function getTeam2Score()
	{
		return $this->team2_score;
	}
	
	/**
	 * Set annulled
	 *
	 * @param boolean $annulled
	 */
	public function setAnnulled($annulled)
	{
		$this->annulled = $annulled;
	}

	/**
	 * Get annulled
	 *
	 * @return boolean
	 */
	public function getAnnulled()
	{
		return $this->annulled;
	}


	public function hasResult()
	{
		return !is_null($this->getTeam1Score()) && !is_null($this->getTeam2Score());
	}

	public function isNoResult()
	{
		return !$this->hasResult() && !is_null($this->getId());
	}

	public function isDraw()
	{
		return $this->hasResult() && $this->getTeam1Score() == $this->getTeam2Score() && $this->getTeam1Score() != 0;
	}

	public function isBothTeamsLost()
	{
		return $this->hasResult() && ($this->getTeam1Score() == 0 && $this->getTeam2Score() == 0);
	}

	public function isTeam1Winner()
	{
		return $this->getTeam1Score() > $this->getTeam2Score();
	}

	public function isTeam2Winner()
	{
		return $this->getTeam1Score() < $this->getTeam2Score();
	}

}