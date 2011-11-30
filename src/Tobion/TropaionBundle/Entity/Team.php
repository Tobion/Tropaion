<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tobion\TropaionBundle\Util\RomanNumeral;

/**
 * Tobion\TropaionBundle\Entity\Team
 *
 * @ORM\Table(name="teams",uniqueConstraints={@ORM\UniqueConstraint(name="team_index", columns={"team_number", "league_id", "club_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Team
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
	 * @var integer $club_id
	 *
	 * @ORM\Column(type="integer")
	 */
	private $club_id;

	/**
	 * @var integer $team_number
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $team_number;

	/**
	 * @var integer $league_id
	 *
	 * @ORM\Column(type="integer")
	 */
	private $league_id;

	/**
	 * @var boolean $withdrawn
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $withdrawn;

	/**
	 * @var text $description
	 *
	 * @ORM\Column(type="text")
	 */
	private $description;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updated_at;

	/**
	 * @var Club
	 *
	 * @ORM\ManyToOne(targetEntity="Club", inversedBy="Teams")
	 * @ORM\JoinColumn(name="club_id", referencedColumnName="id")
	 */
	private $Club;

	/**
	 * @var League
	 *
	 * @ORM\ManyToOne(targetEntity="League", inversedBy="Teams")
	 * @ORM\JoinColumn(name="league_id", referencedColumnName="id")
	 */
	private $League;

	/**
	 * @var Lineups
	 *
	 * @ORM\OneToMany(targetEntity="Lineup", mappedBy="Team")
	 * @ORM\JoinColumn(name="id", referencedColumnName="team_id")
	 * @ORM\OrderBy({"position" = "ASC"})
	 */
	private $Lineups;

	public function __construct()
	{
		$this->Lineups = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function updated()
	{
		$this->updated_at = new DateTime('now');
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
	 * Set club_id
	 *
	 * @param integer $clubId
	 */
	public function setClubId($clubId)
	{
		$this->club_id = $clubId;
	}

	/**
	 * Get club_id
	 *
	 * @return integer $clubId
	 */
	public function getClubId()
	{
		return $this->club_id;
	}

	/**
	 * Set team_number
	 *
	 * @param smallint $teamNumber
	 */
	public function setTeamNumber($teamNumber)
	{
		$this->team_number = $teamNumber;
	}

	/**
	 * Get team_number
	 *
	 * @return smallint $teamNumber
	 */
	public function getTeamNumber()
	{
		return $this->team_number;
	}

	/**
	 * Set league_id
	 *
	 * @param integer $leagueId
	 */
	public function setLeagueId($leagueId)
	{
		$this->league_id = $leagueId;
	}

	/**
	 * Get league_id
	 *
	 * @return integer $leagueId
	 */
	public function getLeagueId()
	{
		return $this->league_id;
	}

	/**
	 * Set withdrawn
	 *
	 * @param boolean $withdrawn
	 */
	public function setWithdrawn($withdrawn)
	{
		$this->withdrawn = $withdrawn;
	}

	/**
	 * Get withdrawn
	 *
	 * @return boolean $withdrawn
	 */
	public function getWithdrawn()
	{
		return $this->withdrawn;
	}

	/**
	 * Set description
	 *
	 * @param text $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Get description
	 *
	 * @return text $description
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set updated_at
	 *
	 * @param datetime $updatedAt
	 */
	public function setUpdatedAt($updatedAt)
	{
		$this->updated_at = $updatedAt;
	}

	/**
	 * Get updated_at
	 *
	 * @return datetime $updatedAt
	 */
	public function getUpdatedAt()
	{
		return $this->updated_at;
	}

	/**
	 * Set Club
	 *
	 * @param Tobion\TropaionBundle\Entity\Club $club
	 */
	public function setClub(\Tobion\TropaionBundle\Entity\Club $club)
	{
		$this->Club = $club;
	}

	/**
	 * Get Club
	 *
	 * @return Tobion\TropaionBundle\Entity\Club $club
	 */
	public function getClub()
	{
		return $this->Club;
	}

	/**
	 * Set League
	 *
	 * @param Tobion\TropaionBundle\Entity\League $league
	 */
	public function setLeague(\Tobion\TropaionBundle\Entity\League $league)
	{
		$this->League = $league;
	}

	/**
	 * Get League
	 *
	 * @return Tobion\TropaionBundle\Entity\League $league
	 */
	public function getLeague()
	{
		return $this->League;
	}


	/**
	 * Add Lineups
	 *
	 * @param Tobion\TropaionBundle\Entity\Lineup $lineups
	 */
	public function addLineups(\Tobion\TropaionBundle\Entity\Lineup $lineups)
	{
		$this->Lineups[] = $lineups;
	}

	/**
	 * Get Lineups
	 *
	 * @return Doctrine\Common\Collections\Collection $lineups
	 */
	public function getLineups()
	{
		return $this->Lineups;
	}


	private function filterLineups($seasonRound = null, $athleteGender = null)
	{
		$lineups = array();
		foreach ($this->Lineups as $lineup) {
			if ((!$seasonRound || $lineup->getSeasonRound() == $seasonRound) && 
				(!$athleteGender || $lineup->getAthlete()->getGender() == $athleteGender)) {
				$lineups[] = $lineup;
			}
		}	
		return $lineups;
	}

	public function getFirstRoundMaleLineups()
	{
		return $this->filterLineups(1, 'male');
	}

	public function getFirstRoundFemaleLineups()
	{
		return $this->filterLineups(1, 'female');
	}

	public function getSecondRoundMaleLineups()
	{
		return $this->filterLineups(2, 'male');
	}

	public function getSecondRoundFemaleLineups()
	{
		return $this->filterLineups(2, 'female');
	}

	public function hasSecondRoundLineup()
	{
		return count($this->filterLineups(2, null)) > 0;
	}

	public function hasLineupChanged()
	{
		$firstRoundLineup = array();
		$secondRoundLineup = array();
		foreach ($this->Lineups as $lineup) {
			if ($lineup->getSeasonRound() == 1) {
				$firstRoundLineup[$lineup->getAthleteId()] = $lineup->getPosition();
			}
			else {
				$secondRoundLineup[$lineup->getAthleteId()] = $lineup->getPosition();
			}
		}

		// wenn noch keine Aufstellung für Rückrunde vorhanden -> Aufstellung nicht geändert
		// die Überprüfung auf gleiche Anzahl an Elementen ist wichtig, da array_diff_assoc abhängig von der Reihenfolge der Parameter ist
		return count($secondRoundLineup) > 0 && 
			(count($secondRoundLineup) != count($firstRoundLineup) || count(array_diff_assoc($firstRoundLineup, $secondRoundLineup)) > 0);

	}


	public function routingParams()
	{
		return array(
			'owner' => $this->getLeague()->getTournament()->getOwner()->getSlug(),
			'tournament' => $this->getLeague()->getTournament()->getSlug(),
			'club' => $this->getClub()->getCode(),
			'teamNumber' => $this->getTeamNumber(),
		);
	}

	public function getClubCodeWithTeamNumber($toRoman = true, $html = false)
	{
		return $this->getClub()->getCode() . 
			($html ? '&#160;' /* '&nbsp;' */ : ' ') . 
			($toRoman ? RomanNumeral::convertIntToRoman($this->getTeamNumber()) : $this->getTeamNumber());
	}

	function getTeamNumberAsRomanNumeral()
	{
		return RomanNumeral::convertIntToRoman($this->getTeamNumber());
	}

	public function isPositioned($seasonRound, $athleteId)
	{
		foreach ($this->Lineups as $lineup) {
			if ($lineup->getSeasonRound() == $seasonRound && $lineup->getAthleteId() == $athleteId) {
				return true;
			}
		}
		return false;
	}

	public function getShortName($toRoman = true, $html = false)
	{
		return $this->getClub()->getCode() . 
			($html ? '&#160;' /* '&nbsp;' */ : ' ') . 
			($toRoman ? RomanNumeral::convertIntToRoman($this->getTeamNumber()) : $this->getTeamNumber());
	}

	public function getFullName($toRoman = true, $html = false)
	{
		return $this->getClub()->getName() . 
			($html ? '&#160;' /* '&nbsp;' */ : ' ') . 
			($toRoman ? RomanNumeral::convertIntToRoman($this->getTeamNumber()) : $this->getTeamNumber());
	}


	function __toString()
	{
		// return $this->getClubCodeWithTeamNumber() . ' (' . $this->getLeague() . ')';
		return $this->getClubCodeWithTeamNumber();
	} 

}