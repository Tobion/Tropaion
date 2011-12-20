<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rating der Spieler über die Zeit
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
	 * singles, doubles, mixed
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
	 * @ORM\JoinColumn(name="athlete_id", referencedColumnName="id", nullable=false)
	 */
	private $Athlete;

	/**
	 * @var Match
	 *
	 * @ORM\ManyToOne(targetEntity="Match", inversedBy="Ratinghistory")
	 * @ORM\JoinColumn(name="match_id", referencedColumnName="id", nullable=false)
	 */
	private $Match;


	public function __construct()
	{
		$this->created_at = new \DateTime('now');
	}


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
	 * @return integer
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
	 * @return string
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
	 * @return smallint
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
	 * @return smallint
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
	 * @return date
	 */
	public function getCreatedAt()
	{
		return $this->created_at;
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

	/**
	 * Set Match
	 *
	 * @param Match $match
	 */
	public function setMatch(Match $match)
	{
		$this->Match = $match;
	}

	/**
	 * Get Match
	 *
	 * @return Match
	 */
	public function getMatch()
	{
		return $this->Match;
	}
}