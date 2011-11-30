<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Ratinghistory
 *
 * @ORM\Table(name="ratinghistory",indexes={@ORM\index(name="rating_date_index", columns={"created_at"})})
 * @ORM\Entity
 */
class Ratinghistory
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var integer $athlete_id
	 *
	 * @ORM\Column(type="integer")
	 */
	private $athlete_id;

	/**
	 * @var integer $match_id
	 *
	 * @ORM\Column(type="integer")
	 */
	private $match_id;

	/**
	 * @var string $discipline
	 *
	 * @ORM\Column(type="string", length=7)
	 */
	private $discipline;

	/**
	 * @var integer $pre_rating
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $pre_rating;

	/**
	 * @var integer $post_rating
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $post_rating;

	/**
	 * @ORM\Column(type="date")
	 */
	private $created_at;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete", inversedBy="Ratinghistory")
	 * @ORM\JoinColumn(name="athlete_id", referencedColumnName="id")
	 */
	private $Athlete;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Match", inversedBy="Ratinghistory")
	 * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
	 */
	private $Match;


	public function __construct()
	{
		$this->created_at = new DateTime('now');
	}


	/**
	 * Get id
	 *
	 * @return integer $id
	 */
	public function getId()
	{
		return $this->id;
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
	 * Set match_id
	 *
	 * @param integer $matchId
	 */
	public function setMatchId($matchId)
	{
		$this->match_id = $matchId;
	}

	/**
	 * Get match_id
	 *
	 * @return integer $matchId
	 */
	public function getMatchId()
	{
		return $this->match_id;
	}

	/**
	 * Set discipline
	 *
	 * @param string $discipline
	 */
	public function setDiscipline($discipline)
	{
		$this->discipline = $discipline;
	}

	/**
	 * Get discipline
	 *
	 * @return string $discipline
	 */
	public function getDiscipline()
	{
		return $this->discipline;
	}

	/**
	 * Set pre_rating
	 *
	 * @param smallint $pre_rating
	 */
	public function setPreRating($rating)
	{
		$this->pre_rating = $rating;
	}

	/**
	 * Get pre_rating
	 *
	 * @return smallint $pre_rating
	 */
	public function getPreRating()
	{
		return $this->pre_rating;
	}

	/**
	 * Set post_rating
	 *
	 * @param smallint $post_rating
	 */
	public function setPostRating($rating)
	{
		$this->post_rating = $rating;
	}

	/**
	 * Get post_rating
	 *
	 * @return smallint $post_rating
	 */
	public function getPostRating()
	{
		return $this->post_rating;
	}

	/**
	 * Set created_at
	 *
	 * @param date $createdAt
	 */
	public function setCreatedAt($createdAt)
	{
		$this->created_at = $createdAt;
	}

	/**
	 * Get created_at
	 *
	 * @return date $createdAt
	 */
	public function getCreatedAt()
	{
		return $this->created_at;
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

	/**
	 * Set Match
	 *
	 * @param Tobion\TropaionBundle\Entity\Match $match
	 */
	public function setMatch(\Tobion\TropaionBundle\Entity\Match $match)
	{
		$this->Match = $match;
	}

	/**
	 * Get Match
	 *
	 * @return Tobion\TropaionBundle\Entity\Match $match
	 */
	public function getMatch()
	{
		return $this->Match;
	}
}