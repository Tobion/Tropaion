<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ligen und Staffeln
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
	 * Einteilung der Liga in eine hierarchische Pyramidenstruktur.
	 * Je größer der Wert, desto schwächer die sportliche Leistung.
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
	 * Die wieviel bestplatzierten qualifizieren sich für den Aufstieg
	 * @var integer $promoted_number
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $promoted_number = 0;

	/**
	 * Die wieviel letztplatzierten qualifizieren sich für den Abstieg
	 * @var integer $relegated_number
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $relegated_number = 0;

	/**
	 * @var Tournament
	 *
	 * @ORM\ManyToOne(targetEntity="Tournament")
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false)
	 */
	private $Tournament;

	/**
	 * @var Team[]
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
	 * @return integer
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
	 * @return string
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
	 * @return string
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
	 * @return string
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
	 * @return smallint
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
	 * @return smallint
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
	 * @return smallint
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
	 * @return smallint
	 */
	public function getRelegatedNumber()
	{
		return $this->relegated_number;
	}

	/**
	 * Set Tournament
	 *
	 * @param Tournament $tournament
	 */
	public function setTournament(Tournament $tournament)
	{
		$this->Tournament = $tournament;
	}

	/**
	 * Get Tournament
	 *
	 * @return Tournament
	 */
	public function getTournament()
	{
		return $this->Tournament;
	}

	/**
	 * Add Teams
	 *
	 * @param Team $team
	 */
	public function addTeams(Team $team)
	{
		$this->Teams[] = $team;
	}

	/**
	 * Get Teams
	 *
	 * @return Doctrine\Common\Collections\Collection
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