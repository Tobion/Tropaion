<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Game
 *
 * @ORM\Table(name="games",uniqueConstraints={@ORM\UniqueConstraint(name="game_index", columns={"annulled", "game_sequence", "match_id"})})
 * @ORM\Entity
 */
class Game
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
	 * @var integer $match_id
	 *
	 * @ORM\Column(name="match_id", type="integer")
	 */
	private $match_id;

	/**
	 * @var integer $game_sequence
	 *
	 * @ORM\Column(name="game_sequence", type="smallint")
	 */
	private $game_sequence;

	/**
	 * @var integer $team1_score
	 *
	 * @ORM\Column(name="team1_score", type="smallint")
	 */
	private $team1_score;

	/**
	 * @var integer $team2_score
	 *
	 * @ORM\Column(name="team2_score", type="smallint")
	 */
	private $team2_score;

	/**
	 * @var boolean $annulled
	 *
	 * @ORM\Column(name="annulled", type="boolean")
	 */
	private $annulled;

	/**
	 * @var Match
	 *
	 * @ORM\ManyToOne(targetEntity="Match", inversedBy="Games")
	 * @ORM\JoinColumn(name="match_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $Match;



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
	 * @return smallint $gameSequence
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
	 * @return smallint $team1Score
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
	 * @return smallint $team2Score
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
	 * @return boolean $annulled
	 */
	public function getAnnulled()
	{
		return $this->annulled;
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
		return sprintf('Game %s of %s [%s : %s]', 
			$this->getGameSequence(), $this->Match,
			$this->getTeam1Score(),
			$this->getTeam2Score()
		);
	}
}