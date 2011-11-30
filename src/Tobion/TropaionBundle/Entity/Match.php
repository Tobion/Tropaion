<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tobion\TropaionBundle\Util\SignedIntToSortableStringConverter;

/**
 * Tobion\TropaionBundle\Entity\Match
 *
 * @ORM\Table(name="matches")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Match
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
	 * @var integer $teammatch_id
	 *
	 * @ORM\Column(type="integer")
	 */
	private $teammatch_id;

	/**
	 * @var string $match_type_id
	 *
	 * @ORM\Column(type="integer")
	 */
	private $match_type_id;

	/**
	 * @var integer $team1_player_id
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $team1_player_id;

	/**
	 * @var integer $team1_partner_id
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $team1_partner_id;

	/**
	 * @var integer $team2_player_id
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $team2_player_id;

	/**
	 * @var integer $team2_partner_id
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $team2_partner_id;

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
	 * @var integer $team1_points
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $team1_points = 0;

	/**
	 * @var integer $team2_points
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $team2_points = 0;

	/**
	 * @var boolean $no_fight
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $no_fight = false;

	/**
	 * @var integer $given_up_by
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $given_up_by = 0;

	/**
	 * @var boolean $revised_score
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $revised_score = false;

	/**
	 * @var integer $team1_original_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team1_original_score;

	/**
	 * @var integer $team2_original_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team2_original_score;

	/**
	 * @var integer $team1_original_points
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team1_original_points;

	/**
	 * @var integer $team2_original_points
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team2_original_points;

	/**
	 * @var integer $avg_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $avg_orig_smaller_div_bigger_gamescore_permil;

	/**
	 * @var integer $std_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $std_orig_smaller_div_bigger_gamescore_permil;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updated_at;

	/**
	 * @var Teammatch
	 *
	 * @ORM\ManyToOne(targetEntity="Teammatch", inversedBy="Matches")
	 * @ORM\JoinColumn(name="teammatch_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $Teammatch;

	/**
	 * @var MatchType
	 *
	 * @ORM\ManyToOne(targetEntity="MatchType")
	 * @ORM\JoinColumn(name="match_type_id", referencedColumnName="id")
	 */
	private $MatchType;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="team1_player_id", referencedColumnName="id")
	 */
	private $Team1_Player;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="team1_partner_id", referencedColumnName="id")
	 */
	private $Team1_Partner;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="team2_player_id", referencedColumnName="id")
	 */
	private $Team2_Player;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="team2_partner_id", referencedColumnName="id")
	 */
	private $Team2_Partner;

	/**
	 * @var Game
	 *
	 * @ORM\OneToMany(targetEntity="Game", mappedBy="Match")
	 * @ORM\JoinColumn(name="id", referencedColumnName="match_id")
	 * @ORM\OrderBy({"game_sequence" = "ASC"})
	 */
	private $Games;

	/**
	 * @var Ratinghistory
	 *
	 * @ORM\OneToMany(targetEntity="Ratinghistory", mappedBy="Match")
	 * @ORM\JoinColumn(name="id", referencedColumnName="match_id")
	 */
	private $Ratinghistory;

	/**
	 * Contain a human-readable identifier for the athletes
	 * Properties used by the form and filled and transformed by the MatchAthletesToIdTransformer
	 */
	public $team1_player_readable_id;
	public $team1_partner_readable_id;
	public $team2_player_readable_id;
	public $team2_partner_readable_id;

	public $team1_noplayer;
	public $team2_noplayer;

	public $result_incident;
	public $revaluation_against;


	public function __construct()
	{
		$this->Games = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Set teammatch_id
	 *
	 * @param integer $teammatchId
	 */
	public function setTeammatchId($teammatchId)
	{
		$this->teammatch_id = $teammatchId;
	}

	/**
	 * Get teammatch_id
	 *
	 * @return integer $teammatchId
	 */
	public function getTeammatchId()
	{
		return $this->teammatch_id;
	}

	/**
	 * Set match_type_id
	 *
	 * @param string $matchTypeId
	 */
	public function setMatchTypeId($matchTypeId)
	{
		$this->match_type_id = $matchTypeId;
	}

	/**
	 * Get match_type_id
	 *
	 * @return string $matchTypeId
	 */
	public function getMatchTypeId()
	{
		return $this->match_type_id;
	}

	/**
	 * Set team1_player_id
	 *
	 * @param integer $team1PlayerId
	 */
	public function setTeam1PlayerId($team1PlayerId)
	{
		$this->team1_player_id = $team1PlayerId;
	}

	/**
	 * Get team1_player_id
	 *
	 * @return integer $team1PlayerId
	 */
	public function getTeam1PlayerId()
	{
		return $this->team1_player_id;
	}

	/**
	 * Set team1_partner_id
	 *
	 * @param integer $team1PartnerId
	 */
	public function setTeam1PartnerId($team1PartnerId)
	{
		$this->team1_partner_id = $team1PartnerId;
	}

	/**
	 * Get team1_partner_id
	 *
	 * @return integer $team1PartnerId
	 */
	public function getTeam1PartnerId()
	{
		return $this->team1_partner_id;
	}

	/**
	 * Set team2_player_id
	 *
	 * @param integer $team2PlayerId
	 */
	public function setTeam2PlayerId($team2PlayerId)
	{
		$this->team2_player_id = $team2PlayerId;
	}

	/**
	 * Get team2_player_id
	 *
	 * @return integer $team2PlayerId
	 */
	public function getTeam2PlayerId()
	{
		return $this->team2_player_id;
	}

	/**
	 * Set team2_partner_id
	 *
	 * @param integer $team2PartnerId
	 */
	public function setTeam2PartnerId($team2PartnerId)
	{
		$this->team2_partner_id = $team2PartnerId;
	}

	/**
	 * Get team2_partner_id
	 *
	 * @return integer $team2PartnerId
	 */
	public function getTeam2PartnerId()
	{
		return $this->team2_partner_id;
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
	 * Set team1_points
	 *
	 * @param smallint $team1Points
	 */
	public function setTeam1Points($team1Points)
	{
		$this->team1_points = $team1Points;
	}

	/**
	 * Get team1_points
	 *
	 * @return smallint $team1Points
	 */
	public function getTeam1Points()
	{
		return $this->team1_points;
	}

	/**
	 * Set team2_points
	 *
	 * @param smallint $team2Points
	 */
	public function setTeam2Points($team2Points)
	{
		$this->team2_points = $team2Points;
	}

	/**
	 * Get team2_points
	 *
	 * @return smallint $team2Points
	 */
	public function getTeam2Points()
	{
		return $this->team2_points;
	}

	/**
	 * Set no_fight
	 *
	 * @param boolean $noFight
	 */
	public function setNoFight($noFight)
	{
		$this->no_fight = $noFight;
	}

	/**
	 * Get no_fight
	 *
	 * @return boolean $noFight
	 */
	public function getNoFight()
	{
		return $this->no_fight;
	}

	/**
	 * Set given_up_by
	 *
	 * @param smallint $givenUpBy
	 */
	public function setGivenUpBy($givenUpBy)
	{
		$this->given_up_by = $givenUpBy;
	}

	/**
	 * Get given_up_by
	 *
	 * @return smallint $givenUpBy
	 */
	public function getGivenUpBy()
	{
		return $this->given_up_by;
	}

	/**
	 * Set revised_score
	 *
	 * @param boolean $revisedScore
	 */
	public function setRevisedScore($revisedScore)
	{
		$this->revised_score = $revisedScore;
	}

	/**
	 * Get revised_score
	 *
	 * @return boolean $revisedScore
	 */
	public function getRevisedScore()
	{
		return $this->revised_score;
	}

	/**
	 * Set team1_original_score
	 *
	 * @param smallint $team1OriginalScore
	 */
	public function setTeam1OriginalScore($team1OriginalScore)
	{
		$this->team1_original_score = $team1OriginalScore;
	}

	/**
	 * Get team1_original_score
	 *
	 * @return smallint $team1OriginalScore
	 */
	public function getTeam1OriginalScore()
	{
		return $this->team1_original_score;
	}

	/**
	 * Set team2_original_score
	 *
	 * @param smallint $team2OriginalScore
	 */
	public function setTeam2OriginalScore($team2OriginalScore)
	{
		$this->team2_original_score = $team2OriginalScore;
	}

	/**
	 * Get team2_original_score
	 *
	 * @return smallint $team2OriginalScore
	 */
	public function getTeam2OriginalScore()
	{
		return $this->team2_original_score;
	}

	/**
	 * Set team1_original_points
	 *
	 * @param smallint $team1OriginalPoints
	 */
	public function setTeam1OriginalPoints($team1OriginalPoints)
	{
		$this->team1_original_points = $team1OriginalPoints;
	}

	/**
	 * Get team1_original_points
	 *
	 * @return smallint $team1OriginalPoints
	 */
	public function getTeam1OriginalPoints()
	{
		return $this->team1_original_points;
	}

	/**
	 * Set team2_original_points
	 *
	 * @param smallint $team2OriginalPoints
	 */
	public function setTeam2OriginalPoints($team2OriginalPoints)
	{
		$this->team2_original_points = $team2OriginalPoints;
	}

	/**
	 * Get team2_original_points
	 *
	 * @return smallint $team2OriginalPoints
	 */
	public function getTeam2OriginalPoints()
	{
		return $this->team2_original_points;
	}

	/**
	 * Set avg_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @param smallint $avgOrigSmallerDivBiggerGamescorePermil
	 */
	public function setAvgOrigSmallerDivBiggerGamescorePermil($avgOrigSmallerDivBiggerGamescorePermil)
	{
		$this->avg_orig_smaller_div_bigger_gamescore_permil = $avgOrigSmallerDivBiggerGamescorePermil;
	}

	/**
	 * Get avg_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @return smallint $avgOrigSmallerDivBiggerGamescorePermil
	 */
	public function getAvgOrigSmallerDivBiggerGamescorePermil()
	{
		return $this->avg_orig_smaller_div_bigger_gamescore_permil;
	}

	/**
	 * Set std_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @param smallint $stdOrigSmallerDivBiggerGamescorePermil
	 */
	public function setStdOrigSmallerDivBiggerGamescorePermil($stdOrigSmallerDivBiggerGamescorePermil)
	{
		$this->std_orig_smaller_div_bigger_gamescore_permil = $stdOrigSmallerDivBiggerGamescorePermil;
	}

	/**
	 * Get std_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @return smallint $stdOrigSmallerDivBiggerGamescorePermil
	 */
	public function getStdOrigSmallerDivBiggerGamescorePermil()
	{
		return $this->std_orig_smaller_div_bigger_gamescore_permil;
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
	 * Set Teammatch
	 *
	 * @param Tobion\TropaionBundle\Entity\Teammatch $teammatch
	 */
	public function setTeammatch(\Tobion\TropaionBundle\Entity\Teammatch $teammatch)
	{
		$this->Teammatch = $teammatch;
	}

	/**
	 * Get Teammatch
	 *
	 * @return Tobion\TropaionBundle\Entity\Teammatch $teammatch
	 */
	public function getTeammatch()
	{
		return $this->Teammatch;
	}

	/**
	 * Set MatchType
	 *
	 * @param Tobion\TropaionBundle\Entity\MatchType $matchType
	 */
	public function setMatchType(\Tobion\TropaionBundle\Entity\MatchType $matchType)
	{
		$this->MatchType = $matchType;
	}

	/**
	 * Get MatchType
	 *
	 * @return Tobion\TropaionBundle\Entity\MatchType $matchType
	 */
	public function getMatchType()
	{
		return $this->MatchType;
	}

	/**
	 * Set Team1_Player
	 *
	 * @param Tobion\TropaionBundle\Entity\Athlete $team1Player
	 */
	public function setTeam1Player(\Tobion\TropaionBundle\Entity\Athlete $team1Player = null)
	{
		$this->Team1_Player = $team1Player;
	}

	/**
	 * Get Team1_Player
	 *
	 * @return Tobion\TropaionBundle\Entity\Athlete $team1Player
	 */
	public function getTeam1Player()
	{
		return $this->Team1_Player;
	}

	/**
	 * Set Team1_Partner
	 *
	 * @param Tobion\TropaionBundle\Entity\Athlete $team1Partner
	 */
	public function setTeam1Partner(\Tobion\TropaionBundle\Entity\Athlete $team1Partner = null)
	{
		$this->Team1_Partner = $team1Partner;
	}

	/**
	 * Get Team1_Partner
	 *
	 * @return Tobion\TropaionBundle\Entity\Athlete $team1Partner
	 */
	public function getTeam1Partner()
	{
		return $this->Team1_Partner;
	}

	/**
	 * Set Team2_Player
	 *
	 * @param Tobion\TropaionBundle\Entity\Athlete $team2Player
	 */
	public function setTeam2Player(\Tobion\TropaionBundle\Entity\Athlete $team2Player = null)
	{
		$this->Team2_Player = $team2Player;
	}

	/**
	 * Get Team2_Player
	 *
	 * @return Tobion\TropaionBundle\Entity\Athlete $team2Player
	 */
	public function getTeam2Player()
	{
		return $this->Team2_Player;
	}

	/**
	 * Set Team2_Partner
	 *
	 * @param Tobion\TropaionBundle\Entity\Athlete $team2Partner
	 */
	public function setTeam2Partner(\Tobion\TropaionBundle\Entity\Athlete $team2Partner = null)
	{
		$this->Team2_Partner = $team2Partner;
	}

	/**
	 * Get Team2_Partner
	 *
	 * @return Tobion\TropaionBundle\Entity\Athlete $team2Partner
	 */
	public function getTeam2Partner()
	{
		return $this->Team2_Partner;
	}

	/**
	 * Add Games
	 *
	 * @param Tobion\TropaionBundle\Entity\Game $games
	 */
	public function addGames(\Tobion\TropaionBundle\Entity\Game $games)
	{
		$this->Games[] = $games;
	}

	/**
	 * Get Games
	 *
	 * @return Doctrine\Common\Collections\Collection $games
	 */
	public function getGames()
	{
		return $this->Games;
	}

	/**
	 * Get Ratinghistory
	 *
	 * @return Doctrine\Common\Collections\Collection $ratinghistory
	 */
	public function getRatinghistory()
	{
		return $this->Ratinghistory;
	}


	/**
	* Team1 nicht angetreten
	*/
	public function isTeam1NoPlayer()
	{
		return is_null($this->getTeam1PlayerId()) && is_null($this->getTeam1PartnerId()) 
				&& !is_null($this->getId());
	}


	/**
	* Team2 nicht angetreten
	*/
	public function isTeam2NoPlayer()
	{
		return is_null($this->getTeam2PlayerId()) && is_null($this->getTeam2PartnerId()) 
				&& !is_null($this->getId());
	}

	/**
	 * 2 vs 1 ist kein Doppel
	 * @return boolean
	 */
	public function isDoubles()
	{
		return !(
			is_null($this->getTeam1PlayerId()) || is_null($this->getTeam1PartnerId()) || 
			is_null($this->getTeam2PlayerId()) || is_null($this->getTeam2PartnerId())
		);
	}

	/**
	 * Ob Team1 nur aus einem Spieler besteht
	 * @return boolean
	 */
	public function isTeam1Single()
	{
		return (is_null($this->getTeam1PlayerId()) XOR is_null($this->getTeam1PartnerId()));
	}

	/**
	 * Ob Team2 nur aus einem Spieler besteht
	 * @return boolean
	 */
	public function isTeam2Single()
	{
		return (is_null($this->getTeam2PlayerId()) XOR is_null($this->getTeam2PartnerId()));
	}

	/**
	 * Gibt einzelnen teilgenommenen Athlete von Team1 zurück
	 * @return Athlete
	 */
	public function getTeam1SingleAthlete()
	{
		return is_null($this->getTeam1PlayerId()) ? $this->Team1_Partner : $this->Team1_Player;
	}

	/**
	 * Gibt einzelnen teilgenommenen Athlete von Team2 zurück
	 * @return Athlete
	 */
	public function getTeam2SingleAthlete()
	{
		return is_null($this->getTeam2PlayerId()) ? $this->Team2_Partner : $this->Team2_Player;
	}

	public function hasResult()
	{
		return !is_null($this->getTeam1Score()) && !is_null($this->getTeam2Score());
	}

	public function isNoResult()
	{
		return !$this->hasResult() && !is_null($this->getTeammatch()->getSubmittedBy());
	}


	public function getResultIncident()
	{
		if ($this->hasTeam1WonByDefault()) {
			return 'team1_wonbydefault';
		}
		if ($this->hasTeam1GivenUp()) {
			return 'team1_givenup';
		}
		if ($this->hasTeam2WonByDefault()) {
			return 'team2_wonbydefault';
		}
		if ($this->hasTeam2GivenUp()) {
			return 'team2_givenup';
		}
		return '';
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
		// return ($this->numberTeam1WonGames() > $this->numberTeam2WonGames());
	}

	public function isTeam2Winner()
	{
		return $this->getTeam1Score() < $this->getTeam2Score();
		// return ($this->numberTeam1WonGames() < $this->numberTeam2WonGames());
	}

	public function hasOriginalResult()
	{
		return !is_null($this->getTeam1OriginalScore()) && !is_null($this->getTeam2OriginalScore());
	}

	public function hasOriginalFallbackEffectiveResult()
	{
		return $this->hasOriginalResult() || $this->hasResult();
	}

	public function getTeam1OriginalFallbackEffectiveScore()
	{
		return (!is_null($this->getTeam1OriginalScore()) && !is_null($this->getTeam2OriginalScore())) ? 
			$this->getTeam1OriginalScore() : 
			$this->getTeam1Score();
	}

	public function getTeam2OriginalFallbackEffectiveScore()
	{
		return (!is_null($this->getTeam1OriginalScore()) && !is_null($this->getTeam2OriginalScore())) ? 
			$this->getTeam2OriginalScore() : 
			$this->getTeam2Score();
	}

	public function getTeam1OriginalFallbackEffectivePoints()
	{
		return (!is_null($this->getTeam1OriginalPoints()) && !is_null($this->getTeam2OriginalPoints())) ? 
			$this->getTeam1OriginalPoints() : 
			$this->getTeam1Points();
	}

	public function getTeam2OriginalFallbackEffectivePoints()
	{
		return (!is_null($this->getTeam1OriginalPoints()) && !is_null($this->getTeam2OriginalPoints())) ? 
			$this->getTeam2OriginalPoints() : 
			$this->getTeam2Points();
	}

	public function isOriginalFallbackEffectiveDraw()
	{
		return $this->getTeam1OriginalFallbackEffectiveScore() == $this->getTeam2OriginalFallbackEffectiveScore() 
			&& !is_null($this->getTeam1OriginalFallbackEffectiveScore()) 
			&& $this->getTeam1OriginalFallbackEffectiveScore() != 0;
	}

	public function isTeam1OriginalFallbackEffectiveWinner()
	{
		return ($this->getTeam1OriginalFallbackEffectiveScore() > $this->getTeam2OriginalFallbackEffectiveScore());
	}

	public function isTeam2OriginalFallbackEffectiveWinner()
	{
		return ($this->getTeam1OriginalFallbackEffectiveScore() < $this->getTeam2OriginalFallbackEffectiveScore());
	}

	public function isTeam1RevaluatedAgainst()
	{
		return $this->getRevisedScore() && ($this->isTeam2Winner() || $this->isBothTeamsLost());
	}

	public function isTeam2RevaluatedAgainst()
	{
		return $this->getRevisedScore() && ($this->isTeam1Winner() || $this->isBothTeamsLost());
	}

	public function getRevaluationAgainst()
	{
		if ($this->getRevisedScore() && $this->isTeam2Winner()) {
			return 'team1';
		}
		if ($this->getRevisedScore() && $this->isTeam1Winner()) {
			return 'team2';
		}
		if ($this->getRevisedScore() && $this->isBothTeamsLost()) {
			return 'both';
		}

		return '';
	}

	/**
	 * Ob Team1 kampflos gewonnen hat
	 * @return boolean
	 */
	public function hasTeam1WonByDefault()
	{
		return $this->getNoFight()&& $this->isTeam1OriginalFallbackEffectiveWinner();
	}

	/**
	 * Ob Team2 kampflos gewonnen hat
	 * @return boolean
	 */
	public function hasTeam2WonByDefault()
	{
		return $this->getNoFight()&& $this->isTeam2OriginalFallbackEffectiveWinner();
	}

	public function hasTeam1GivenUp()
	{
		return $this->getGivenUpBy() == 1;
	}

	public function hasTeam2GivenUp()
	{
		return $this->getGivenUpBy() == 2;
	}


	/**
	 * Höhe des Sieges als sortierbare Zeichenkette
	 * Je höherwertig bei normalem String-Vergleich die Zeichenkette, desto eindeutiger der Sieg für den Spieler
	 * Betrachtet nicht nur das Spiel-, sondern auch das Punktergebnis
	 *
	 * @return string Als String encoded Höhe des Sieges
	 */
	public function getMarginOfVictorySortableString(Athlete $athlete = null)
	{
		if (!$athlete) {
			return SignedIntToSortableStringConverter::convertArray(
				array(
					abs($this->getTeam1OriginalFallbackEffectiveScore() - $this->getTeam2OriginalFallbackEffectiveScore()),
					abs($this->getTeam1OriginalFallbackEffectiveScore() + $this->getTeam2OriginalFallbackEffectiveScore()),
					1000 - $this->getAvgOrigSmallerDivBiggerGamescorePermil(),
					abs($this->getTeam1OriginalFallbackEffectivePoints() + $this->getTeam2OriginalFallbackEffectivePoints())
					// 1000 - $this->getStdOrigSmallerDivBiggerGamescorePermil()
				),
				array(2, 3, 4, 3)
			);
		}
		/*
			getAvgOrigSmallerDivBiggerGamescorePermil() not usefull when analyzing from a players perspective
			Example: 21:0 , 19:21 = 0:21 , 21:19

			Points difference is not universal across sports or varying game length:
			$this->getTeam1OriginalFallbackEffectivePoints() - $this->getTeam2OriginalFallbackEffectivePoints(),
			- ($this->getTeam1OriginalFallbackEffectivePoints() + $this->getTeam2OriginalFallbackEffectivePoints())
			Also 21 : 16 , 21 : 15 should be a higher win than 21 : 11 , 26 : 24 but it is not
		*/
		else if ($this->getTeam1Player() === $athlete || $this->getTeam1Partner() === $athlete) {
			if ($this->getTeam1OriginalFallbackEffectivePoints() || $this->getTeam2OriginalFallbackEffectivePoints()) {
				$pointsQuotient = $this->getTeam1OriginalFallbackEffectivePoints() < $this->getTeam2OriginalFallbackEffectivePoints() ?
					$this->getTeam1OriginalFallbackEffectivePoints() / $this->getTeam2OriginalFallbackEffectivePoints() :
					$this->getTeam2OriginalFallbackEffectivePoints() / $this->getTeam1OriginalFallbackEffectivePoints();

				$pointsQuotient = round((1 - $pointsQuotient) * ($this->getTeam1OriginalFallbackEffectivePoints() >= $this->getTeam2OriginalFallbackEffectivePoints() ? 1 : -1) * 9999);

			} else {
				$pointsQuotient = 0;
			}

			return SignedIntToSortableStringConverter::convertArray(
				array(
					$this->getTeam1OriginalFallbackEffectiveScore() - $this->getTeam2OriginalFallbackEffectiveScore(),
					$this->getTeam1OriginalFallbackEffectiveScore() + $this->getTeam2OriginalFallbackEffectiveScore(),
					$pointsQuotient,
					$this->getTeam1OriginalFallbackEffectivePoints() + $this->getTeam2OriginalFallbackEffectivePoints()
				),
				array(2, 3, 4, 3)
			);
		}
		else if ($this->getTeam2Player() === $athlete || $this->getTeam2Partner() === $athlete) {
			/*
			Another try:
			$pointsQuotient = ($this->getTeam2OriginalFallbackEffectivePoints() ?: 1) / ($this->getTeam1OriginalFallbackEffectivePoints() ?: 1);
			$pointsQuotient = ($pointsQuotient < 1) ? (1 - $pointsQuotient) * -10000 : (1 - 1 / $pointsQuotient) * 10000;
			$pointsQuotient = round($pointsQuotient);
			*/
			if ($this->getTeam1OriginalFallbackEffectivePoints() || $this->getTeam2OriginalFallbackEffectivePoints()) {
				$pointsQuotient = $this->getTeam1OriginalFallbackEffectivePoints() < $this->getTeam2OriginalFallbackEffectivePoints() ?
					$this->getTeam1OriginalFallbackEffectivePoints() / $this->getTeam2OriginalFallbackEffectivePoints() :
					$this->getTeam2OriginalFallbackEffectivePoints() / $this->getTeam1OriginalFallbackEffectivePoints();

				$pointsQuotient = round((1 - $pointsQuotient) * ($this->getTeam2OriginalFallbackEffectivePoints() >= $this->getTeam1OriginalFallbackEffectivePoints() ? 1 : -1) * 9999);
			} else {
				$pointsQuotient = 0;
			}

			return SignedIntToSortableStringConverter::convertArray(
				array(
					$this->getTeam2OriginalFallbackEffectiveScore() - $this->getTeam1OriginalFallbackEffectiveScore(),
					$this->getTeam2OriginalFallbackEffectiveScore() + $this->getTeam1OriginalFallbackEffectiveScore(),
					$pointsQuotient,
					$this->getTeam2OriginalFallbackEffectivePoints() + $this->getTeam1OriginalFallbackEffectivePoints()
				),
				array(2, 3, 4, 3)
			);
		}

		return '';

	}


	/**
	 * Gibt die tatsaechlich geltenden Saetze zurueck
	 * 
	 * @return array
	 */
	public function getEffectiveGames()
	{
		$games = array();
		foreach ($this->Games as $game) {
			if (!$game->getAnnulled()) {
				$games[] = $game;
			}
		}
		return $games;
	}

	/**
	 * Gibt die urspruenglichen, annullierten Saetze zurueck
	 * 
	 * @return array
	 */
	public function getAnnulledGames()
	{
		$games = array();
		foreach ($this->Games as $game) {
			if ($game->getAnnulled()) {
				$games[] = $game;
			}
		}
		return $games;
	}

	/**
	 * Gibt die Anzahl annullierter Saetze zurück
	 * 
	 * @return integer
	 */
	public function countAnnulledGames()
	{
		return count($this->getAnnulledGames());
	}

	/**
	 * Anzahl gewonnener Saetze von Team 1
	 *
	 * @return integer
	 */
	public function numberTeam1WonGames()
	{
		$sum = 0;
		foreach ($this->getEffectiveGames() as $game) {
			if ($game->getTeam1Score() > $game->getTeam2Score()) {
				$sum++;
			}
		}
		return $sum;
	}

	/**
	 * Anzahl gewonnener Saetze von Team 2
	 *
	 * @return integer
	 */
	public function numberTeam2WonGames()
	{
		$sum = 0;
		foreach ($this->getEffectiveGames() as $game) {
			if ($game->getTeam1Score() < $game->getTeam2Score()) {
				$sum++;
			}
		}
		return $sum;
	}

	/**
	 * Summe der gewonnenen Punkte von Team 1 ueber alle Saetze
	 *
	 * @return integer
	 */
	public function sumTeam1Score()
	{
		$sum = 0;
		foreach ($this->getEffectiveGames() as $game) {
			$sum += $game->getTeam1Score();
		}
		return $sum;
	}

	/**
	 * Summe der gewonnenen Punkte von Team 2 ueber alle Saetze
	 *
	 * @return integer
	 */
	public function sumTeam2Score()
	{
		$sum = 0;
		foreach ($this->getEffectiveGames() as $game) {
			$sum += $game->getTeam2Score();
		}
		return $sum;
	}


	/**
	 * Ob Team1_Player Ersatzspieler ist
	 *
	 * @return boolean
	 */
	public function isTeam1PlayerSubstitute()
	{
		return !$this->getTeammatch()->getTeam1()->isPositioned(
			$this->getTeammatch()->getSeasonRound(), 
			$this->getTeam1PlayerId()
		);
		/*
		return !(Doctrine::getTable('Lineup')->isPositioned(
			$this->Teammatch->Team1->getId(), 
			$this->Teammatch->getSeasonRound(), 
			$this->getTeam1PlayerId()
		));
		*/
	}

	/**
	 * Ob Team1_Partner Ersatzspieler ist
	 *
	 * @return boolean
	 */
	public function isTeam1PartnerSubstitute()
	{
		return !$this->getTeammatch()->getTeam1()->isPositioned(
			$this->getTeammatch()->getSeasonRound(), 
			$this->getTeam1PartnerId()
		);
	}


	/**
	 * Ob Team2_Player Ersatzspieler ist
	 *
	 * @return boolean
	 */
	public function isTeam2PlayerSubstitute()
	{
		return !$this->getTeammatch()->getTeam2()->isPositioned(
			$this->getTeammatch()->getSeasonRound(), 
			$this->getTeam2PlayerId()
		);	
	}

	/**
	 * Ob Team2_Partner Ersatzspieler ist
	 *
	 * @return boolean
	 */
	public function isTeam2PartnerSubstitute()
	{
		return !$this->getTeammatch()->getTeam2()->isPositioned( 
			$this->getTeammatch()->getSeasonRound(), 
			$this->getTeam2PartnerId()
		);
	}


	public function transformToAthleteView(\Tobion\TropaionBundle\Entity\Athlete $athlete, $transformTeammatch = true, $transformGames = true)
	{
		/* Observed strange behavior of Doctrine:
			$this->getTeam1PlayerId() returns string
			$athlete->getId() returns integer
			So cannot use === comparison
		*/

		if ($transformGames) {
			foreach ($this->getGames() as $game) {
				if ($this->getTeam2PlayerId() == $athlete->getId() || $this->getTeam2PartnerId() == $athlete->getId())
				{
					$game->swapTeamData();
				}
			}
		}

		/*
			Check if teammatch has not already been transformed with !$this->getTeammatch()->getTransformedViewHomeAway()
			to make sure it does not get transformed multiple times back and forth 
			because the same Teammatch can be referenced in memory by several Matches
		*/
		if ($this->getTeam1PlayerId() == $athlete->getId())
		{
			$this->homeaway = 'home';
			if ($transformTeammatch && !$this->getTeammatch()->getTransformedViewHomeAway()) {
				$this->getTeammatch()->transformToTeamView($this->getTeammatch()->getTeam1());
			}
		}
		else if ($this->getTeam1PartnerId() == $athlete->getId())
		{
			$this->homeaway = 'home';
			$this->swapAthleteData(1);
			if ($transformTeammatch && !$this->getTeammatch()->getTransformedViewHomeAway()) {
				$this->getTeammatch()->transformToTeamView($this->getTeammatch()->getTeam1());
			}
		}
		else if ($this->getTeam2PlayerId() == $athlete->getId())
		{
			$this->homeaway = 'away';
			$this->swapTeamData();
			if ($transformTeammatch && !$this->getTeammatch()->getTransformedViewHomeAway()) {
				$this->getTeammatch()->transformToTeamView($this->getTeammatch()->getTeam2());
			}
		}
		else if ($this->getTeam2PartnerId() == $athlete->getId())
		{
			$this->homeaway = 'away';
			$this->swapAthleteData(2);
			$this->swapTeamData();
			if ($transformTeammatch && !$this->getTeammatch()->getTransformedViewHomeAway()) {
				$this->getTeammatch()->transformToTeamView($this->getTeammatch()->getTeam2());
			}
		}


	}

	public function getTransformedViewHomeAway()
	{
		return isset($this->homeaway) ? $this->homeaway : null; 
	}

	private function swapTeamData()
	{
		$tmp = $this->team1_player_id;
		$this->team1_player_id = $this->team2_player_id;
		$this->team2_player_id = $tmp;

		$tmp = $this->Team1_Player;
		$this->Team1_Player = $this->Team2_Player;
		$this->Team2_Player = $tmp;

		$tmp = $this->team1_partner_id;
		$this->team1_partner_id = $this->team2_partner_id;
		$this->team2_partner_id = $tmp;

		$tmp = $this->Team1_Partner;
		$this->Team1_Partner = $this->Team2_Partner;
		$this->Team2_Partner = $tmp;

		$tmp = $this->team1_score;
		$this->team1_score = $this->team2_score;
		$this->team2_score = $tmp;

		$tmp = $this->team1_points;
		$this->team1_points = $this->team2_points;
		$this->team2_points = $tmp;

		$tmp = $this->team1_original_score;
		$this->team1_original_score = $this->team2_original_score;
		$this->team2_original_score = $tmp;

		$tmp = $this->team1_original_points;
		$this->team1_original_points = $this->team2_original_points;
		$this->team2_original_points = $tmp;

		if ($this->given_up_by == 2) {
			$this->given_up_by = 1;
		}
		else if ($this->given_up_by == 1) {
			$this->given_up_by = 2;
		}
	}


	/**
	 * Swap athletes of a particular team
	 * @param integer $team 0 = athletes of both teams; 1 = athletes of team1; 2 = athletes of team2
	 */
	private function swapAthleteData($team = 0)
	{
		if (!$team || $team === 1) {
			$tmp = $this->team1_player_id;
			$this->team1_player_id = $this->team1_partner_id;
			$this->team1_partner_id = $tmp;

			$tmp = $this->Team1_Player;
			$this->Team1_Player = $this->Team1_Partner;
			$this->Team1_Partner = $tmp;
		} 
		if (!$team || $team === 2) {
			$tmp = $this->team2_player_id;
			$this->team2_player_id = $this->team2_partner_id;
			$this->team2_partner_id = $tmp;

			$tmp = $this->Team2_Player;
			$this->Team2_Player = $this->Team2_Partner;
			$this->Team2_Partner = $tmp;
		}
	}

	/**
	 * @return Ratinghistory
	 */
	public function getAthleteRatinghistory(\Tobion\TropaionBundle\Entity\Athlete $athlete)
	{

		foreach ($this->getRatinghistory() as $ratinghistory) {
			if ($ratinghistory->getAthleteId() == $athlete->getId())
			{
				return $ratinghistory;
			}
		}

		return null;		
	}


	function __toString()
	{
		if ($this->isDoubles()) {
			return sprintf('%s / %s vs %s / %s [%s @ %s]', 
				$this->Team1_Player->getLastName(), $this->Team1_Partner->getLastName(),
				$this->Team2_Player->getLastName(), $this->Team2_Partner->getLastName(),
				$this->MatchType, $this->Teammatch 
			);
		}
		else {
			return sprintf('%s vs %s [%s @ %s]', 
				$this->Team1_Player,
				$this->Team2_Player,
				$this->MatchType, $this->Teammatch 
			);
		}
	}


	public function checkAthletesSpecified(\Symfony\Component\Validator\ExecutionContext $context)
	{
		$basePath = $context->getPropertyPath();

		// when using TransformationFailedException
		// basePath is children[matches].data[0]
		// instead of the real children[matches][0].data
		//var_dump($basePath);

		//echo 'VALIDATOR:';
		//var_dump($this->team1_player_readable_id);
		//var_dump($this->team1_noplayer);
		//var_dump($this->getGames()->get(0)->getTeam1Score());

		if (!$this->team1_noplayer) {
			if ($this->team1_player_readable_id === null || $this->team1_player_readable_id === '') {
				$context->setPropertyPath($basePath . '.team1_player_readable_id');
				$context->addViolation('Not blank', array(), null);
			}
			if ($this->getMatchType()->getXOnX() != 1 && 
				($this->team1_partner_readable_id === null || $this->team1_partner_readable_id === '')) {
				$context->setPropertyPath($basePath . '.team1_partner_readable_id');
				$context->addViolation('Not blank', array(), null);
			}
		} else {
			if (!($this->team1_player_readable_id === null || $this->team1_player_readable_id === '')) {
				$context->setPropertyPath($basePath . '.team1_player_readable_id');
				$context->addViolation('Should be blank', array(), null);
			}
			if (!($this->team1_partner_readable_id === null || $this->team1_partner_readable_id === '')) {
				$context->setPropertyPath($basePath . '.team1_partner_readable_id');
				$context->addViolation('Should be blank', array(), null);
			}
		}

		if (!$this->team2_noplayer) {
			if ($this->team2_player_readable_id === null || $this->team2_player_readable_id === '') {
				$context->setPropertyPath($basePath . '.team2_player_readable_id');
				$context->addViolation('Not blank', array(), null);
			}
			if ($this->getMatchType()->getXOnX() != 1 && 
				($this->team2_partner_readable_id === null || $this->team2_partner_readable_id === '')) {
				$context->setPropertyPath($basePath . '.team2_partner_readable_id');
				$context->addViolation('Not blank', array(), null);
			}
		} else {
			if (!($this->team2_player_readable_id === null || $this->team2_player_readable_id === '')) {
				$context->setPropertyPath($basePath . '.team2_player_readable_id');
				$context->addViolation('Should be blank', array(), null);
			}
			if (!($this->team2_partner_readable_id === null || $this->team2_partner_readable_id === '')) {
				$context->setPropertyPath($basePath . '.team2_partner_readable_id');
				$context->addViolation('Should be blank', array(), null);
			}
		}
	}

}