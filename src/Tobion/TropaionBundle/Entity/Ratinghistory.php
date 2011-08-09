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
     * @var string $discipline
     *
     * @ORM\Column(type="string", length=7)
     */
    private $discipline;

    /**
     * @var integer $rating
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $rating;

    /**
     * @var integer $number_matches
     *
     * @ORM\Column(type="smallint")
     */
    private $number_matches;

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
     * Set rating
     *
     * @param smallint $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * Get rating
     *
     * @return smallint $rating
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set number_matches
     *
     * @param smallint $numberMatches
     */
    public function setNumberMatches($numberMatches)
    {
        $this->number_matches = $numberMatches;
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
     * Get number_matches
     *
     * @return smallint $numberMatches
     */
    public function getNumberMatches()
    {
        return $this->number_matches;
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

}