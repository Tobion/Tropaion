<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Tobion\TropaionBundle\Util\SignedIntToSortableStringConverter;


/**
 * Spiele zwischen zwei oder vier Sportlern (Einzel und Doppel), also Individual-Spiele
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
	 * Anzahl gewonnener Sätze von Team 1
	 * NULL = kein Ergebnis
	 * @var integer $team1_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team1_score;

	/**
	 * Anzahl gewonnener Sätze von Team 2
	 * NULL = kein Ergebnis
	 * @var integer $team2_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team2_score;

	/**
	 * Anzahl erzielter Spielpunkte von Team 1 über alle Sätze
	 * @var integer $team1_points
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $team1_points = 0;

	/**
	 * Anzahl erzielter Spielpunkte von Team 2 über alle Sätze
	 * @var integer $team2_points
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $team2_points = 0;

	/**
	 * Kampflos beendetes Spiel, z.B. kein Gegner oder Aufgabe vor Spielbeginn
	 * Bezieht sich bei Umwertung auf das Orginalergebnis soweit vorhanden,
	 * da Umwertung per se kampflos
	 * @var boolean $no_fight
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $no_fight = false;

	/**
	 * Ob und wer das Spiel aufgegeben/zurückgezogen hat
	 * 0 = false, 1 = Aufgabe von Team1, 2 = Aufgabe von Team2
	 * Bezieht sich bei Umwertung auf das Orginalergebnis soweit vorhanden
	 * Spielstand zum Zeitpunkt der Aufgabe nachvollziehbar bei games.annulled = 1
	 * @var integer $given_up_by
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $given_up_by = 0;

	/**
	 * Ob Ergebnis revidiert wurde; Urspruengliches nachvollziehbar bei games.annulled = 1
	 * @var boolean $revised_score
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $revised_score = false;

	/**
	 * Ob und wer einen (Aufstellungs-)Fehler begangen hat,
	 * der zu einer Umwertung führt (Verursacher)
	 * 0 = false, 1 = Fehler verursacht von Team1, 2 = Team2, 3 = beide schuldig
	 * @var integer $revaluation_wrongdoer
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $revaluation_wrongdoer = 0;

	/**
	 * Begründung der Umwertung
	 * 0 = false, sonst Fehlernummer
	 * @var integer $revaluation_reason
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $revaluation_reason = 0;

	/**
	 * Ursprüngliches Ergebnis von Team1 (vor der Änderung oder Aufgabe; siehe games.annulled = 1)
	 * NULL = nicht vorhanden
	 * @var integer $team1_original_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team1_original_score;

	/**
	 * Ursprüngliches Ergebnis von Team2 (vor der Änderung oder Aufgabe; siehe games.annulled = 1)
	 * NULL = nicht vorhanden
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
	 * Durchschn. Quotient (in Promille) aus kleineren durch größeren Satzpunkten
	 * des urspr. (bzw. normalen wenn es nicht vorliegt) Ergebnisses.
	 * Maß für die Ausgeglichenheit des Spiels
	 * @var integer $avg_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $avg_orig_smaller_div_bigger_gamescore_permil;

	/**
	 * Standardabweichung (in Promille) der Quotienten aus kleineren durch größeren Satzpunkten
	 * des urspr. (bzw. normalen wenn es nicht vorliegt) Ergebnisses.
	 * Maß für die Ergebnisschwankung
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
	 * @ORM\JoinColumn(name="teammatch_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 */
	private $Teammatch;

	/**
	 * @var MatchType
	 *
	 * @ORM\ManyToOne(targetEntity="MatchType")
	 * @ORM\JoinColumn(name="match_type_id", referencedColumnName="id", nullable=false)
	 */
	private $MatchType;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="team1_player_id", referencedColumnName="id", nullable=true)
	 */
	private $Team1_Player;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="team1_partner_id", referencedColumnName="id", nullable=true)
	 */
	private $Team1_Partner;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="team2_player_id", referencedColumnName="id", nullable=true)
	 */
	private $Team2_Player;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="team2_partner_id", referencedColumnName="id", nullable=true)
	 */
	private $Team2_Partner;

	/**
	 * @var Game[]
	 *
	 * @ORM\OneToMany(targetEntity="Game", mappedBy="Match", cascade={"persist"}, orphanRemoval=true)
	 * @ORM\OrderBy({"game_sequence" = "ASC"})
	 */
	private $Games;
	// Remember: When using the orphanRemoval=true option Doctrine makes
	// the assumption that the entities are privately owned and will NOT be reused by other entities.
	// If you neglect this assumption your entities will get deleted by Doctrine
	// even if you assigned the orphaned entity to another one.

	/**
	 * @var Ratinghistory
	 *
	 * @ORM\OneToMany(targetEntity="Ratinghistory", mappedBy="Match")
	 */
	private $Ratinghistory;

	/**
	 * Contain a human-readable identifier for the athletes
	 * Properties used by the form and filled and transformed by the TransformAthletesListener
	 */
	public $team1_player_readable_id;
	public $team1_partner_readable_id;
	public $team2_player_readable_id;
	public $team2_partner_readable_id;

	public $team1_noplayer;
	public $team2_noplayer;
	
	public $noresult;

	public $result_incident;


	public function __construct()
	{
		$this->Games = new \Doctrine\Common\Collections\ArrayCollection();
		$this->Ratinghistory = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function updated()
	{
		$this->updated_at = new \DateTime('now');
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
	 * @return smallint
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
	 * @return smallint
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
	 * @return boolean
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
	 * @return smallint
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
	 * @return boolean
	 */
	public function getRevisedScore()
	{
		return $this->revised_score;
	}

	/**
	 * Set revaluation_wrongdoer
	 *
	 * @param smallint $revaluationWrongdoer
	 */
	public function setRevaluationWrongdoer($revaluationWrongdoer)
	{
		$this->revaluation_wrongdoer = (int) $revaluationWrongdoer;
	}

	/**
	 * Get revaluation_wrongdoer
	 *
	 * @return smallint
	 */
	public function getRevaluationWrongdoer()
	{
		return $this->revaluation_wrongdoer;
	}

	/**
	 * Set revaluation_reason
	 *
	 * @param smallint $revaluationReason
	 */
	public function setRevaluationReason($revaluationReason)
	{
		$this->revaluation_reason = $revaluationReason;
	}

	/**
	 * Get revaluation_reason
	 *
	 * @return smallint
	 */
	public function getRevaluationReason()
	{
		return $this->revaluation_reason;
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
	 * @return smallint
	 */
	public function getTeam1OriginalScore()
	{
		return $this->team1_original_score;
	}

	/**
	 * Set team2_original_score
	 *
	 * @param smallint
	 */
	public function setTeam2OriginalScore($team2OriginalScore)
	{
		$this->team2_original_score = $team2OriginalScore;
	}

	/**
	 * Get team2_original_score
	 *
	 * @return smallint
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
	 * @return smallint
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
	 * @return smallint
	 */
	public function getTeam2OriginalPoints()
	{
		return $this->team2_original_points;
	}

	/**
	 * Set avg_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @param smallint $val
	 */
	public function setAvgOrigSmallerDivBiggerGamescorePermil($val)
	{
		// type cast to integer because doctrine considers the same value
		// of type int and float to be different and thus updates the record
		// in the database
		$this->avg_orig_smaller_div_bigger_gamescore_permil = (int) $val;
	}

	/**
	 * Get avg_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @return smallint
	 */
	public function getAvgOrigSmallerDivBiggerGamescorePermil()
	{
		return $this->avg_orig_smaller_div_bigger_gamescore_permil;
	}

	/**
	 * Set std_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @param smallint $val
	 */
	public function setStdOrigSmallerDivBiggerGamescorePermil($val)
	{
		$this->std_orig_smaller_div_bigger_gamescore_permil = (int) $val;
	}

	/**
	 * Get std_orig_smaller_div_bigger_gamescore_permil
	 *
	 * @return smallint
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
	 * @return datetime
	 */
	public function getUpdatedAt()
	{
		return $this->updated_at;
	}

	/**
	 * Set Teammatch
	 *
	 * @param Teammatch $teammatch
	 */
	public function setTeammatch(Teammatch $teammatch)
	{
		$this->Teammatch = $teammatch;
	}

	/**
	 * Get Teammatch
	 *
	 * @return Teammatch
	 */
	public function getTeammatch()
	{
		return $this->Teammatch;
	}

	/**
	 * Set MatchType
	 *
	 * @param MatchType $matchType
	 */
	public function setMatchType(MatchType $matchType)
	{
		$this->MatchType = $matchType;
	}

	/**
	 * Get MatchType
	 *
	 * @return MatchType
	 */
	public function getMatchType()
	{
		return $this->MatchType;
	}

	/**
	 * Set Team1_Player
	 *
	 * @param Athlete $team1Player
	 */
	public function setTeam1Player(Athlete $team1Player = null)
	{
		$this->Team1_Player = $team1Player;
	}

	/**
	 * Get Team1_Player
	 *
	 * @return Athlete
	 */
	public function getTeam1Player()
	{
		return $this->Team1_Player;
	}

	/**
	 * Set Team1_Partner
	 *
	 * @param Athlete $team1Partner
	 */
	public function setTeam1Partner(Athlete $team1Partner = null)
	{
		$this->Team1_Partner = $team1Partner;
	}

	/**
	 * Get Team1_Partner
	 *
	 * @return Athlete
	 */
	public function getTeam1Partner()
	{
		return $this->Team1_Partner;
	}

	/**
	 * Set Team2_Player
	 *
	 * @param Athlete $team2Player
	 */
	public function setTeam2Player(Athlete $team2Player = null)
	{
		$this->Team2_Player = $team2Player;
	}

	/**
	 * Get Team2_Player
	 *
	 * @return Athlete
	 */
	public function getTeam2Player()
	{
		return $this->Team2_Player;
	}

	/**
	 * Set Team2_Partner
	 *
	 * @param Athlete $team2Partner
	 */
	public function setTeam2Partner(Athlete $team2Partner = null)
	{
		$this->Team2_Partner = $team2Partner;
	}

	/**
	 * Get Team2_Partner
	 *
	 * @return Athlete
	 */
	public function getTeam2Partner()
	{
		return $this->Team2_Partner;
	}

	/**
	 * Add Games
	 *
	 * @param Game $game
	 */
	public function addGames(Game $game)
	{
		$this->Games[] = $game;
	}

	/**
	 * Get Games
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getGames()
	{
		return $this->Games;
	}

	/**
	 * Get Ratinghistory
	 *
	 * @return Doctrine\Common\Collections\Collection
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
		// set the property on the getter because the form does not set the value on submission
		// when it did not change
		// $this->getId() !== null makes sure it is not true when the entity is new
		return $this->team1_noplayer =
			($this->Team1_Player === null && $this->Team1_Partner === null
				&& $this->getId() !== null);
	}


	/**
	* Team2 nicht angetreten
	*/
	public function isTeam2NoPlayer()
	{
		return $this->team2_noplayer =
			($this->Team2_Player === null && $this->Team2_Partner === null
				&& $this->getId() !== null);
	}

	/**
	 * 2 vs 1 ist kein Doppel
	 * @return boolean
	 */
	public function isDoubles()
	{
		return !(
			$this->Team1_Player === null || $this->Team1_Partner === null ||
			$this->Team2_Player === null || $this->Team2_Partner === null
		);
	}

	/**
	 * Ob Team1 nur aus einem Spieler besteht
	 * @return boolean
	 */
	public function isTeam1Single()
	{
		return $this->Team1_Player === null XOR $this->Team1_Partner === null;
	}

	/**
	 * Ob Team2 nur aus einem Spieler besteht
	 * @return boolean
	 */
	public function isTeam2Single()
	{
		return $this->Team2_Player === null XOR $this->Team2_Partner === null;
	}

	/**
	 * Gibt einzelnen teilgenommenen Athlete von Team1 zurück
	 * @return Athlete|null
	 */
	public function getTeam1SingleAthlete()
	{
		return $this->Team1_Player === null ? $this->Team1_Partner : $this->Team1_Player;
	}

	/**
	 * Gibt einzelnen teilgenommenen Athlete von Team2 zurück
	 * @return Athlete
	 */
	public function getTeam2SingleAthlete()
	{
		return $this->Team2_Player === null ? $this->Team2_Partner : $this->Team2_Player;
	}

	public function hasResult()
	{
		return $this->getTeam1Score() !== null && $this->getTeam2Score() !== null;
	}

	public function isNoResult()
	{
		return !$this->hasResult() && $this->getId() !== null;
	}


	public function getResultIncident()
	{
		if ($this->hasTeam1WonByDefault()) {
			return $this->result_incident = 'team1_wonbydefault';
		}
		if ($this->hasTeam1GivenUp()) {
			return $this->result_incident = 'team1_givenup';
		}
		if ($this->hasTeam2WonByDefault()) {
			return $this->result_incident = 'team2_wonbydefault';
		}
		if ($this->hasTeam2GivenUp()) {
			return $this->result_incident = 'team2_givenup';
		}
		return $this->result_incident = '';
	}

	/**
	 *
	 * @param string $resultIncident
	 */
	public function setResultIncident($resultIncident)
	{
		$this->result_incident = $resultIncident;

		$this->inferNoFight();

		switch ($resultIncident) {
			case 'team1_givenup':
				$this->setGivenUpBy(1);
				break;
			case 'team2_givenup':
				$this->setGivenUpBy(2);
				break;
			default:
				$this->setGivenUpBy(0);
		}
	}

	/**
	 * Updates no_fight flag based on some conditions that mean a match was not played:
	 * - ein Team ohne Spieler oder
	 * - kein Ergebnis vorhanden (sowohl kein gewertetes als auch
	 *   kein ursprüngliches Ergebnis = kein einziger Satz) oder
	 * - kampfloser Sieg (@see setResultIncident)
	 */
	public function inferNoFight()
	{
		$this->setNoFight(
			$this->result_incident === 'team1_wonbydefault' ||
			$this->result_incident === 'team2_wonbydefault' ||
			$this->Team1_Player === null && $this->Team1_Partner === null ||
			$this->Team2_Player === null && $this->Team2_Partner === null ||
			count($this->Games) === 0
		);
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

	public function hasOriginalResult()
	{
		return $this->getTeam1OriginalScore() !== null && $this->getTeam2OriginalScore() !== null;
	}

	public function hasOriginalFallbackEffectiveResult()
	{
		return $this->hasOriginalResult() || $this->hasResult();
	}

	public function getTeam1OriginalFallbackEffectiveScore()
	{
		return ($this->getTeam1OriginalScore() !== null && $this->getTeam2OriginalScore() !== null) ?
			$this->getTeam1OriginalScore() : 
			$this->getTeam1Score();
	}

	public function getTeam2OriginalFallbackEffectiveScore()
	{
		return ($this->getTeam1OriginalScore() !== null && $this->getTeam2OriginalScore() !== null) ?
			$this->getTeam2OriginalScore() : 
			$this->getTeam2Score();
	}

	public function getTeam1OriginalFallbackEffectivePoints()
	{
		return ($this->getTeam1OriginalPoints() !== null && $this->getTeam2OriginalPoints() !== null) ?
			$this->getTeam1OriginalPoints() : 
			$this->getTeam1Points();
	}

	public function getTeam2OriginalFallbackEffectivePoints()
	{
		return ($this->getTeam1OriginalPoints() !== null && $this->getTeam2OriginalPoints() !== null) ?
			$this->getTeam2OriginalPoints() : 
			$this->getTeam2Points();
	}

	public function isOriginalFallbackEffectiveDraw()
	{
		return $this->getTeam1OriginalFallbackEffectiveScore() == $this->getTeam2OriginalFallbackEffectiveScore() 
			&& $this->getTeam1OriginalFallbackEffectiveScore() !== null
			&& $this->getTeam1OriginalFallbackEffectiveScore() !== 0;
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
		return $this->getRevaluationWrongdoer() === 1 || $this->getRevaluationWrongdoer() === 3;
	}

	public function isTeam2RevaluatedAgainst()
	{
		return $this->getRevaluationWrongdoer() === 2 || $this->getRevaluationWrongdoer() === 3;
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
		return $this->getGivenUpBy() === 1;
	}

	public function hasTeam2GivenUp()
	{
		return $this->getGivenUpBy() === 2;
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
		else if ($this->Team1_Player === $athlete || $this->Team1_Partner === $athlete) {
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
		else if ($this->Team2_Player === $athlete || $this->Team2_Partner === $athlete) {
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
	 * Gibt die tatsächlich geltenden Sätze zurück
	 * 
	 * @return Game[]
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
	 * Gibt die ursprünglichen, annullierten Sätze zurück
	 * 
	 * @return Game[]
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
	 * Ob Team1_Player Ersatzspieler ist
	 *
	 * @return boolean
	 */
	public function isTeam1PlayerSubstitute()
	{
		return !$this->getTeammatch()->getTeam1()->isPositioned(
			$this->Team1_Player,
			$this->getTeammatch()->getStage()
		);
	}

	/**
	 * Ob Team1_Partner Ersatzspieler ist
	 *
	 * @return boolean
	 */
	public function isTeam1PartnerSubstitute()
	{
		return !$this->getTeammatch()->getTeam1()->isPositioned(
			$this->Team1_Partner,
			$this->getTeammatch()->getStage()
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
			$this->Team2_Player,
			$this->getTeammatch()->getStage()
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
			$this->Team2_Partner,
			$this->getTeammatch()->getStage()
		);
	}


	public function transformToAthleteView(Athlete $athlete, $transformTeammatch = true, $transformGames = true)
	{
		if ($transformGames) {
			foreach ($this->Games as $game) {
				if ($this->Team2_Player === $athlete || $this->Team2_Partner === $athlete)
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
		if ($this->Team1_Player === $athlete)
		{
			$this->homeaway = 'home';
			if ($transformTeammatch && !$this->getTeammatch()->getTransformedViewHomeAway()) {
				$this->getTeammatch()->transformToTeamView($this->getTeammatch()->getTeam1());
			}
		}
		else if ($this->Team1_Partner === $athlete)
		{
			$this->homeaway = 'home';
			$this->swapAthleteData(1);
			if ($transformTeammatch && !$this->getTeammatch()->getTransformedViewHomeAway()) {
				$this->getTeammatch()->transformToTeamView($this->getTeammatch()->getTeam1());
			}
		}
		else if ($this->Team2_Player === $athlete)
		{
			$this->homeaway = 'away';
			$this->swapTeamData();
			if ($transformTeammatch && !$this->getTeammatch()->getTransformedViewHomeAway()) {
				$this->getTeammatch()->transformToTeamView($this->getTeammatch()->getTeam2());
			}
		}
		else if ($this->Team2_Partner === $athlete)
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
		$tmp = $this->Team1_Player;
		$this->Team1_Player = $this->Team2_Player;
		$this->Team2_Player = $tmp;

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

		if ($this->given_up_by === 2) {
			$this->given_up_by = 1;
		}
		else if ($this->given_up_by === 1) {
			$this->given_up_by = 2;
		}

		if ($this->revaluation_wrongdoer === 2) {
			$this->revaluation_wrongdoer = 1;
		}
		else if ($this->revaluation_wrongdoer === 1) {
			$this->revaluation_wrongdoer = 2;
		}
	}


	/**
	 * Swap athletes of a particular team
	 * @param integer $team 0 = athletes of both teams; 1 = athletes of team1; 2 = athletes of team2
	 */
	private function swapAthleteData($team = 0)
	{
		if (!$team || $team === 1) {
			$tmp = $this->Team1_Player;
			$this->Team1_Player = $this->Team1_Partner;
			$this->Team1_Partner = $tmp;
		} 
		if (!$team || $team === 2) {
			$tmp = $this->Team2_Player;
			$this->Team2_Player = $this->Team2_Partner;
			$this->Team2_Partner = $tmp;
		}
	}

	/**
	 * @return Ratinghistory
	 */
	public function getAthleteRatinghistory(Athlete $athlete)
	{

		foreach ($this->Ratinghistory as $ratinghistory) {
			if ($ratinghistory->getAthlete() === $athlete)
			{
				return $ratinghistory;
			}
		}

		return null;		
	}

	/**
	 * Recalculates all statistics, like the score based on the games
	 */
	public function updateStats()
	{
		$this->inferNoFight();

		$hasOriginalGames = count($this->getAnnulledGames()) > 0;

		// nicht aufgegebene Spiele einbeziehen
		$this->setRevisedScore($hasOriginalGames && $this->getRevaluationWrongdoer());

		$gamescoreQuotients = array();

		// update effective result
		$team1Games = $team2Games = null;
		$team1Points = $team2Points = null;

		foreach ($this->getEffectiveGames() as $game) {
			$team1Games += $game->isDraw() ? 0.5 : $game->isTeam1Winner();
			$team2Games += $game->isDraw() ? 0.5 : $game->isTeam2Winner();
			$team1Points += $game->getTeam1Score();
			$team2Points += $game->getTeam2Score();

			if (!$hasOriginalGames && ($game->getTeam1Score() != 0 || $game->getTeam2Score() != 0)) {
				$gamescoreQuotients[] =
					$game->getTeam1Score() < $game->getTeam2Score() ?
						$game->getTeam1Score() / $game->getTeam2Score() :
						$game->getTeam2Score() / $game->getTeam1Score();
			}
		}

		$this->setTeam1Score($team1Games);
		$this->setTeam2Score($team2Games);

		$this->setTeam1Points($team1Points ?: 0);
		$this->setTeam2Points($team2Points ?: 0);

		// update original result
		$team1Games = $team2Games = null;
		$team1Points = $team2Points = null;

		foreach ($this->getAnnulledGames() as $game) {
			$team1Games += $game->isDraw() ? 0.5 : $game->isTeam1Winner();
			$team2Games += $game->isDraw() ? 0.5 : $game->isTeam2Winner();
			$team1Points += $game->getTeam1Score();
			$team2Points += $game->getTeam2Score();

			if ($game->getTeam1Score() != 0 || $game->getTeam2Score() != 0) {
				$gamescoreQuotients[] =
					$game->getTeam1Score() < $game->getTeam2Score() ?
						$game->getTeam1Score() / $game->getTeam2Score() :
						$game->getTeam2Score() / $game->getTeam1Score();
			}
		}

		$this->setTeam1OriginalScore($team1Games);
		$this->setTeam2OriginalScore($team2Games);

		$this->setTeam1OriginalPoints($team1Points);
		$this->setTeam2OriginalPoints($team2Points);

		// update average and standard deviation of smaller div bigger gamescore
		if ($count = count($gamescoreQuotients)) {
			$avg = array_sum($gamescoreQuotients) / $count;
			$var = 0.0;
			foreach ($gamescoreQuotients as $val)
			{
				$var += pow($val - $avg, 2);
			}
			$var /= $count;
			$std = sqrt($var);

			$this->setAvgOrigSmallerDivBiggerGamescorePermil(round($avg * 1000));
			$this->setStdOrigSmallerDivBiggerGamescorePermil(round($std * 1000));
		} else {
			$this->setAvgOrigSmallerDivBiggerGamescorePermil(null);
			$this->setStdOrigSmallerDivBiggerGamescorePermil(null);
		}

	}


	function getTeam1String()
	{
		if ($this->Team1_Player && $this->Team1_Partner) {
			return sprintf('%s / %s',
				$this->Team1_Player->getLastName(), $this->Team1_Partner->getLastName()
			);
		} else if ($this->Team1_Player) {
			return $this->Team1_Player->getFullName();
		} else if ($this->Team1_Partner) {
			return $this->Team1_Partner->getFullName();
		} else {
			return '--';
		}
	}

	function getTeam2String()
	{
		if ($this->Team2_Player && $this->Team2_Partner) {
			return sprintf('%s / %s',
				$this->Team2_Player->getLastName(), $this->Team2_Partner->getLastName()
			);
		} else if ($this->Team2_Player) {
			return $this->Team2_Player->getFullName();
		} else if ($this->Team2_Partner) {
			return $this->Team2_Partner->getFullName();
		} else {
			return '--';
		}
	}

	function __toString()
	{
		return sprintf('%s vs %s [%s @ %s]',
			$this->getTeam1String(),
			$this->getTeam2String(),
			$this->MatchType, $this->Teammatch
		);
	}

}