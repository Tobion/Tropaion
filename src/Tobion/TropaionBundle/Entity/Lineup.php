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
	 * Hinrunde = 1, Rückrunde = 2
	 * @var integer $stage
	 *
	 * @ORM\Column(type="smallint")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $stage;

	/**
	 * @var Team
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 * @ORM\ManyToOne(targetEntity="Team", inversedBy="Lineups")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
	 */
	private $Team;

	/**
	 * @var Athlete
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="athlete_id", referencedColumnName="id", nullable=false)
	 */
	private $Athlete;

	/**
	 * @var integer $position
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $position;


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
		return $this->getTeam()->isSubstitute();
	}

	function __toString()
	{
		return sprintf('%s for %s at #%s (stage %s)',
			$this->Athlete, $this->Team,
			$this->position, $this->stage
		);
	}
	
	/**
	 * Has lineup changed between stage 1 and stage 2?
	 * Handles both the lineup of a single athlete and a whole team.
	 * It ignores null values.
	 *
	 * @param array|Traversable $lineups sequence of Lineup instances
	 * @return Boolean
	 */
	public static function hasLineupChanged($lineups)
	{
		$prevTeam = null;
		$firstStageLineup = array();
		$secondStageLineup = array();
		
		foreach ($lineups as $lineup) {
			if (!$lineup) {
				continue;
			}
			if (!$prevTeam) {
				$prevTeam = $lineup->getTeam();
			} else if ($prevTeam !== $lineup->getTeam()) {
				return true;
			}
			if ($lineup->getStage() == 1) {
				$firstStageLineup[$lineup->getAthlete()->getId()] = $lineup->getPosition();
			} else {
				$secondStageLineup[$lineup->getAthlete()->getId()] = $lineup->getPosition();
			}
		}

		// wenn noch keine Aufstellung für Rückrunde vorhanden oder nur Rückrundenaufstellung vorliegend -> Aufstellung nicht geändert
		// die Überprüfung auf gleiche Anzahl an Elementen ist wichtig, da array_diff_assoc abhängig von der Reihenfolge der Parameter ist
		return count($firstStageLineup) > 0 && count($secondStageLineup) > 0 &&
			(count($firstStageLineup) !== count($secondStageLineup) || count(array_diff_assoc($firstStageLineup, $secondStageLineup)) > 0);
	}


}