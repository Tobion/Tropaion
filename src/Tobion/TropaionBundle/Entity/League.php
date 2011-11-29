<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\League
 *
 * @ORM\Table(name="leagues",uniqueConstraints={@ORM\UniqueConstraint(name="league_index", columns={"class_abbr", "division","tournament_id"})})
 * @ORM\Entity(repositoryClass="Tobion\TropaionBundle\Repository\LeagueRepository")
 */
class League
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
     * @var integer $tournament_id
     *
     * @ORM\Column(type="integer")
     */
    private $tournament_id;

    /**
     * @var string $class_abbr
     *
     * @ORM\Column(type="string", length=10)
     */
    private $class_abbr;

    /**
     * @var string $class_name
     *
     * @ORM\Column(type="string", length=50)
     */
    private $class_name;
	
	/**
     * @var integer $class_level
     *
     * @ORM\Column(type="smallint")
     */
    private $class_level;

    /**
     * @var integer $division
     *
     * @ORM\Column(type="smallint")
     */
    private $division;    

    /**
     * @var integer $promoted_number
     *
     * @ORM\Column(type="smallint")
     */
    private $promoted_number;

    /**
     * @var integer $relegated_number
     *
     * @ORM\Column(type="smallint")
     */
    private $relegated_number;

    /**
     * @var Tournament
     *
     * @ORM\ManyToOne(targetEntity="Tournament")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     */
    private $Tournament;
	
    /**
     * @var Team
     *
     * @ORM\OneToMany(targetEntity="Team", mappedBy="League")
     * @ORM\JoinColumn(name="id", referencedColumnName="league_id")
     */
    private $Teams;

    public function __construct()
    {
        $this->Teams = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set tournament_id
     *
     * @param string $tournamentId
     */
    public function setTournamentId($tournamentId)
    {
        $this->tournament_id = $tournamentId;
    }

    /**
     * Get tournament_id
     *
     * @return string $tournamentId
     */
    public function getTournamentId()
    {
        return $this->tournament_id;
    }

    /**
     * Set class_abbr
     *
     * @param string $classAbbr
     */
    public function setClassAbbr($classAbbr)
    {
        $this->class_abbr = $classAbbr;
    }

    /**
     * Get class_abbr
     *
     * @return string $classAbbr
     */
    public function getClassAbbr()
    {
        return $this->class_abbr;
    }

    /**
     * Set class_name
     *
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->class_name = $className;
    }

    /**
     * Get class_name
     *
     * @return string $className
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Set class_level
     *
     * @param smallint $classLevel
     */
    public function setClassLevel($classLevel)
    {
        $this->class_level = $classLevel;
    }

    /**
     * Get class_level
     *
     * @return smallint $classLevel
     */
    public function getClassLevel()
    {
        return $this->class_level;
    }

    /**
     * Set division
     *
     * @param smallint $division
     */
    public function setDivision($division)
    {
        $this->division = $division;
    }

    /**
     * Get division
     *
     * @return smallint $division
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set promoted_number
     *
     * @param smallint $promotedNumber
     */
    public function setPromotedNumber($promotedNumber)
    {
        $this->promoted_number = $promotedNumber;
    }

    /**
     * Get promoted_number
     *
     * @return smallint $promotedNumber
     */
    public function getPromotedNumber()
    {
        return $this->promoted_number;
    }

    /**
     * Set relegated_number
     *
     * @param smallint $relegatedNumber
     */
    public function setRelegatedNumber($relegatedNumber)
    {
        $this->relegated_number = $relegatedNumber;
    }

    /**
     * Get relegated_number
     *
     * @return smallint $relegatedNumber
     */
    public function getRelegatedNumber()
    {
        return $this->relegated_number;
    }
	
    /**
     * Set Tournament
     *
     * @param Tobion\TropaionBundle\Entity\Tournament $tournament
     */
    public function setTournament(\Tobion\TropaionBundle\Entity\Tournament $tournament)
    {
        $this->Tournament = $tournament;
    }

    /**
     * Get Tournament
     *
     * @return Tobion\TropaionBundle\Entity\Tournament $tournament
     */
    public function getTournament()
    {
        return $this->Tournament;
    }

    /**
     * Add Teams
     *
     * @param Tobion\TropaionBundle\Entity\Team $teams
     */
    public function addTeams(\Tobion\TropaionBundle\Entity\Team $teams)
    {
        $this->Teams[] = $teams;
    }

    /**
     * Get Teams
     *
     * @return Doctrine\Common\Collections\Collection $teams
     */
    public function getTeams()
    {
        return $this->Teams;
    }
	
	
	
	/**
	 * Only unique in the right context (per tournament).
	 */
	public function getSlug()
    {
    	return $this->getDivision() ? 
			$this->getClassAbbr() . '-' . $this->getDivision() :
			$this->getClassAbbr();
    }
	
	public static function parseSlug($slug)
    {
		$leagueParts = array();

		if (preg_match('/^(.+)-(\d+)$/', $slug, $leagueParts)) {
			return array(
				'classAbbr' => $leagueParts[1],
				'division' => $leagueParts[2]
			);
		} else {
			return array(
				'classAbbr' => $slug,
				'division' => 0
			);
		}
    }
	
	
	/**
     * Returns safe CURIE for referencing resources in RDFa
     * @param  string $prefix    The namespace prefix
     * @return string CURIE
     */
	public function getCurie($prefix = 'resource')
    {
       	return "[$prefix:league/{$this->getId()}]";
    }
	
	public function routingParams()
    {
        return array(
			'owner' => $this->getTournament()->getOwner()->getSlug(),
        	'tournament' => $this->getTournament()->getSlug(),
			'league' => $this->getSlug()
        );
    }
	
	public function getShortName()
    {
		return $this->getClassAbbr() . ($this->getDivision() != 0 ? ' ' . $this->getDivision() : '');
    }
	
	public function getFullName()
    {
		return $this->getClassName() . ($this->getDivision() != 0 ? ' ' . $this->getDivision() : '');
    }
	
	function __toString()
    {
        return $this->getShortName();
    }
}