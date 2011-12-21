<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="games")
 * @ORM\Entity
 */
// uniqueConstraints={@ORM\UniqueConstraint(name="game_index", columns={"annulled", "game_sequence", "match_id"})}
// This unique index raises a duplicate key error because doctrine 
// updates records of a collection with cascade=persist
// before it removes records with orphanRemoval=true.
// So the entities are in valid state but doctrine propages the database changes in the wrong order.
// Anyway the index is not needed because it does not enhance query execution for our use-cases.
class Game
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
	 * @var integer $game_sequence
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $game_sequence;

	/**
	 * @var integer $team1_score
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $team1_score;

	/**
	 * @var integer $team2_score
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $team2_score;

	/**
	 * Nachträglich annulliertes Ergebnis (z.B. durch formale Fehler)
	 * oder Spielstand zum Zeitpunkt einer Aufgabe (dessen gewertetes Ergebnis anders ist)
	 * @var boolean $annulled
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $annulled = false;

	/**
	 * @var Match
	 *
	 * @ORM\ManyToOne(targetEntity="Match", inversedBy="Games")
	 * @ORM\JoinColumn(name="match_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 */
	private $Match;



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
	 * Set game_sequence
	 *
	 * @param smallint $gameSequence
	 */
	public function setGameSequence($gameSequence)
	{
		$this->game_sequence = $gameSequence;
	}

	/**
	 * Get game_sequence
	 *
	 * @return smallint
	 */
	public function getGameSequence()
	{
		return $this->game_sequence;
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
		$this->annulled = (bool) $annulled;
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


	public function hasResult()
	{
		return $this->getTeam1Score() !== null && $this->getTeam2Score() !== null;
	}

	public function isDraw()
	{
		return $this->hasResult() && $this->getTeam1Score() == $this->getTeam2Score() && $this->getTeam1Score() !== 0;
	}

	public function isBothTeamsLost()
	{
		return $this->hasResult() && $this->getTeam1Score() === 0 && $this->getTeam2Score() === 0;
	}

	public function isTeam1Winner()
	{
		return $this->hasResult() && $this->getTeam1Score() > $this->getTeam2Score();
	}

	public function isTeam2Winner()
	{
		return $this->hasResult() && $this->getTeam1Score() < $this->getTeam2Score();
	}

	/**
	 * Used to compare two game objects by game_sequence and thus getting the correct 
	 * sequence of games with usort($games, array("Game", "cmp_game_sequence"));
	 * @param $a Game
	 * @param $b Game
	 * @return array of Game objects sorted by game_sequence
	 */
	static function cmp_game_sequence(Game $a, Game $b)
	{
		if ($a->getGameSequence() == $b->getGameSequence()) {
			return 0;
		}
		return ($a->getGameSequence() > $b->getGameSequence()) ? +1 : -1;
	}

	public function swapTeamData()
	{		
		$tmp = $this->team1_score;
		$this->team1_score = $this->team2_score;
		$this->team2_score = $tmp;
	}


	function __toString()
	{
		return sprintf('Game %s%s [%s:%s] of %s',
			$this->getGameSequence() === null ? '?' : $this->getGameSequence(),
			$this->getAnnulled() ? ' (annulled)' : '',
			$this->getTeam1Score(),
			$this->getTeam2Score(),
			$this->Match
		);
	}
}