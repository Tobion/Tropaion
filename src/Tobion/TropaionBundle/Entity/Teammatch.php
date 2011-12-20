<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Tobion\TropaionBundle\Util\SignedIntToSortableStringConverter;

/**
 * Spiele zwischen zwei Mannschaften
 * Sowohl geplante (ohne Ergebnis) als auch absolvierte,
 * die aus mehreren Individual-Matches bestehen können
 *
 * @ORM\Table(name="teammatches",indexes={@ORM\index(name="performed_at_index", columns={"performed_at"})})
 * @ORM\Entity(repositoryClass="Tobion\TropaionBundle\Repository\TeammatchRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Teammatch
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
	 * @var integer $venue_id
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $venue_id;

	/**
	 * Ursprünglich geplantes Datum des Spiels, wenn abweichend von performed_at
	 * @var datetime $scheduled_at
	 *
	 * @ORM\Column(type="datetime")
	 */
	private $scheduled_at;

	/**
	 * Verlegter Spieltermin
	 * @var datetime $moved_at
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $moved_at;

	/**
	 * Austragungsdatum des Spiels
	 * @var datetime $performed_at
	 *
	 * @ORM\Column(type="datetime")
	 */
	private $performed_at;

	/**
	 * Hinrunde = 1, Rückrunde = 2
	 * @var integer $stage
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $stage;

	/**
	 * Heimmannschaft bzw. erstes Team bei Spielen auf neutralem Grund
	 * @var integer $team1_id
	 *
	 * @ORM\Column(type="integer")
	 */
	private $team1_id;

	/**
	 * Auswärtsmannschaft bzw. zweites Team bei Spielen auf neutralem Grund
	 * @var integer $team2_id
	 *
	 * @ORM\Column(type="integer")
	 */
	private $team2_id;

	/**
	 * Resultat für Team 1 (z.B. gewonnene Spiele oder erzielte Tore)
	 * D.h. Anzahl der gewonnenen Matches von Team 1
	 * @var integer $team1_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team1_score;

	/**
	 * Resultat für Team 2 (z.B. gewonnene Spiele oder erzielte Tore)
	 * D.h. Anzahl der gewonnenen Matches von Team 2
	 * @var integer $team2_score
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team2_score;

	/**
	 * Anzahl der gewonnenen Sätze von Team 1
	 * @var integer $team1_games
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team1_games;

	/**
	 * Anzahl der gewonnenen Sätze von Team 2
	 * @var integer $team2_games
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team2_games;

	/**
	 * Gewonnene Spielpunkte von Team 1
	 * D.h. Summe aller gewonnenen Punkte von Team 1 über alle Individualspiele
	 * und Sätze des Teamspiels
	 * @var integer $team1_points
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team1_points;

	/**
	 * Gewonnene Spielpunkte von Team 2
	 * D.h. Summe aller gewonnenen Punkte von Team 2 über alle Individualspiele
	 * und Sätze des Teamspiels
	 * @var integer $team2_points
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $team2_points;

	/**
	 * Kampflos beendetes Spiel, z.B. nicht angetretene Mannschaft
	 * @var boolean $no_fight
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $no_fight = false;

	/**
	 * Unbewertetes Ergebnis bzw. gestrichene Ansetzung, z.B. durch zurückgezogene Mannschaft
	 * @var boolean $annulled
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $annulled = false;

	/**
	 * Ob Ergebnis revidiert wurde; D.h. hat matches mit revised_score = 1
	 * Dient zusätzlich als Flag für Aufstellungsfehler in den einzelnen Disziplinen,
	 * wobei nur diese umgewertet wurden. Im Gegensatz zur globalen Teammatch-Umwertung
	 * mittels revaluation_wrongdoer.
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
	 * Unvollständige Aufstellung; D.h. hat matches mit player = NULL
	 * @var boolean $incomplete_lineup
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $incomplete_lineup;

	/**
	 * Ergebnis eingegeben von welchem Nutzer
	 * @var integer $submitted_by_id
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $submitted_by_id;

	/**
	 * @var datetime $submitted_at
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $submitted_at;

	/**
	 * Ergebnis bestätigt von welchem Nutzer
	 * @var integer $confirmed_by_id
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $confirmed_by_id;

	/**
	 * @var datetime $confirmed_at
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $confirmed_at;

	/**
	 * Ergebnis genehmigt von welchem Nutzer
	 * @var integer $approved_by_id
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $approved_by_id;

	/**
	 * @var datetime $approved_at
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $approved_at;

	/**
	 * @var text $description
	 *
	 * @ORM\Column(type="text")
	 */
	private $description = '';

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $created_at;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updated_at;

	/**
	 * @var Venue
	 *
	 * @ORM\ManyToOne(targetEntity="Venue")
	 * @ORM\JoinColumn(name="venue_id", referencedColumnName="id", nullable=true, onDelete="SET NULL", onUpdate="CASCADE")
	 */
	private $Venue;

	/**
	 * @var Team
	 *
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team1_id", referencedColumnName="id", nullable=false)
	 */
	private $Team1;

	/**
	 * @var Team
	 *
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team2_id", referencedColumnName="id", nullable=false)
	 */
	private $Team2;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="submitted_by_id", referencedColumnName="id", nullable=true)
	 */
	private $Submitted_By;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="confirmed_by_id", referencedColumnName="id", nullable=true)
	 */
	private $Confirmed_By;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="approved_by_id", referencedColumnName="id", nullable=true)
	 */
	private $Approved_By;

	/**
	 * @var Match[]
	 *
	 * @ORM\OneToMany(targetEntity="Match", mappedBy="Teammatch", cascade={"persist"})
	 * @ORM\JoinColumn(name="id", referencedColumnName="teammatch_id")
	 */
	private $Matches;


	public function __construct()
	{
		$this->created_at = $this->updated_at = new \DateTime('now');
		$this->Matches = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Set venue_id
	 *
	 * @param integer $venueId
	 */
	public function setVenueId($venueId)
	{
		$this->venue_id = $venueId;
	}

	/**
	 * Get venue_id
	 *
	 * @return integer
	 */
	public function getVenueId()
	{
		return $this->venue_id;
	}

	/**
	 * Set scheduled_at
	 *
	 * @param datetime $scheduledAt
	 */
	public function setScheduledAt($scheduledAt)
	{
		$this->scheduled_at = $scheduledAt;
	}

	/**
	 * Get scheduled_at
	 *
	 * @return datetime
	 */
	public function getScheduledAt()
	{
		return $this->scheduled_at;
	}

	/**
	 * Set moved_at
	 *
	 * @param datetime $movedAt
	 */
	public function setMovedAt($movedAt)
	{
		$this->moved_at = $movedAt;
	}

	/**
	 * Get moved_at
	 *
	 * @return datetime
	 */
	public function getMovedAt()
	{
		return $this->moved_at;
	}

	/**
	 * Set performed_at
	 *
	 * @param datetime $performedAt
	 */
	public function setPerformedAt($performedAt)
	{
		$this->performed_at = $performedAt;
	}

	/**
	 * Get performed_at
	 *
	 * @return datetime
	 */
	public function getPerformedAt()
	{
		return $this->performed_at;
	}

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
	 * Set team1_id
	 *
	 * @param integer $team1Id
	 */
	public function setTeam1Id($team1Id)
	{
		$this->team1_id = $team1Id;
	}

	/**
	 * Get team1_id
	 *
	 * @return integer
	 */
	public function getTeam1Id()
	{
		return $this->team1_id;
	}

	/**
	 * Set team2_id
	 *
	 * @param integer $team2Id
	 */
	public function setTeam2Id($team2Id)
	{
		$this->team2_id = $team2Id;
	}

	/**
	 * Get team2_id
	 *
	 * @return integer
	 */
	public function getTeam2Id()
	{
		return $this->team2_id;
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
	 * Set team1_games
	 *
	 * @param smallint $team1Games
	 */
	public function setTeam1Games($team1Games)
	{
		$this->team1_games = $team1Games;
	}

	/**
	 * Get team1_games
	 *
	 * @return smallint
	 */
	public function getTeam1Games()
	{
		return $this->team1_games;
	}

	/**
	 * Set team2_games
	 *
	 * @param smallint $team2Games
	 */
	public function setTeam2Games($team2Games)
	{
		$this->team2_games = $team2Games;
	}

	/**
	 * Get team2_games
	 *
	 * @return smallint
	 */
	public function getTeam2Games()
	{
		return $this->team2_games;
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
	 * @return boolean
	 */
	public function getAnnulled()
	{
		return $this->annulled;
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
		$this->revaluation_wrongdoer = $revaluationWrongdoer;
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
	 * Set incomplete_lineup
	 *
	 * @param boolean $incompleteLineup
	 */
	public function setIncompleteLineup($incompleteLineup)
	{
		$this->incomplete_lineup = $incompleteLineup;
	}

	/**
	 * Get incomplete_lineup
	 *
	 * @return boolean
	 */
	public function getIncompleteLineup()
	{
		return $this->incomplete_lineup;
	}

	/**
	 * Set submitted_by_id
	 *
	 * @param integer $submittedById
	 */
	public function setSubmittedById($submittedById)
	{
		$this->submitted_by_id = $submittedById;
	}

	/**
	 * Get submitted_by_id
	 *
	 * @return integer
	 */
	public function getSubmittedById()
	{
		return $this->submitted_by_id;
	}

	/**
	 * Set submitted_at
	 *
	 * @param datetime $submittedAt
	 */
	public function setSubmittedAt($submittedAt)
	{
		$this->submitted_at = $submittedAt;
	}

	/**
	 * Get submitted_at
	 *
	 * @return datetime
	 */
	public function getSubmittedAt()
	{
		return $this->submitted_at;
	}

	/**
	 * Set confirmed_by_id
	 *
	 * @param integer $confirmedById
	 */
	public function setConfirmedById($confirmedById)
	{
		$this->confirmed_by_id = $confirmedById;
	}

	/**
	 * Get confirmed_by_id
	 *
	 * @return integer
	 */
	public function getConfirmedById()
	{
		return $this->confirmed_by_id;
	}

	/**
	 * Set confirmed_at
	 *
	 * @param datetime $confirmedAt
	 */
	public function setConfirmedAt($confirmedAt)
	{
		$this->confirmed_at = $confirmedAt;
	}

	/**
	 * Get confirmed_at
	 *
	 * @return datetime
	 */
	public function getConfirmedAt()
	{
		return $this->confirmed_at;
	}

	/**
	 * Set approved_by_id
	 *
	 * @param integer $approvedById
	 */
	public function setApprovedById($approvedById)
	{
		$this->approved_by_id = $approvedById;
	}

	/**
	 * Get approved_by_id
	 *
	 * @return integer
	 */
	public function getApprovedById()
	{
		return $this->approved_by_id;
	}

	/**
	 * Set approved_at
	 *
	 * @param datetime $approvedAt
	 */
	public function setApprovedAt($approvedAt)
	{
		$this->approved_at = $approvedAt;
	}

	/**
	 * Get approved_at
	 *
	 * @return datetime
	 */
	public function getApprovedAt()
	{
		return $this->approved_at;
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
	 * @return text
	 */
	public function getDescription()
	{
		return $this->description;
	}

		/**
	 * Set created_at
	 *
	 * @param datetime $createdAt
	 */
	public function setCreatedAt($createdAt)
	{
		$this->created_at = $createdAt;
	}

	/**
	 * Get created_at
	 *
	 * @return datetime
	 */
	public function getCreatedAt()
	{
		return $this->created_at;
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
	 * Set Venue
	 *
	 * @param Venue $venue
	 */
	public function setVenue(Venue $venue)
	{
		$this->Venue = $venue;
	}

	/**
	 * Get Venue
	 *
	 * @return Venue
	 */
	public function getVenue()
	{
		return $this->Venue;
	}

	/**
	 * Set Team1
	 *
	 * @param Team $team1
	 */
	public function setTeam1(Team $team1)
	{
		$this->Team1 = $team1;
	}

	/**
	 * Get Team1
	 *
	 * @return Team
	 */
	public function getTeam1()
	{
		return $this->Team1;
	}

	/**
	 * Set Team2
	 *
	 * @param Team $team2
	 */
	public function setTeam2(Team $team2)
	{
		$this->Team2 = $team2;
	}

	/**
	 * Get Team2
	 *
	 * @return Team
	 */
	public function getTeam2()
	{
		return $this->Team2;
	}

	/**
	 * Set Submitted_By
	 *
	 * @param User $submittedBy
	 */
	public function setSubmittedBy(User $submittedBy)
	{
		$this->Submitted_By = $submittedBy;
	}

	/**
	 * Get Submitted_By
	 *
	 * @return User
	 */
	public function getSubmittedBy()
	{
		return $this->Submitted_By;
	}

	/**
	 * Set Confirmed_By
	 *
	 * @param User $confirmedBy
	 */
	public function setConfirmedBy(User $confirmedBy)
	{
		$this->Confirmed_By = $confirmedBy;
	}

	/**
	 * Get Confirmed_By
	 *
	 * @return User
	 */
	public function getConfirmedBy()
	{
		return $this->Confirmed_By;
	}

	/**
	 * Set Approved_By
	 *
	 * @param User $approvedBy
	 */
	public function setApprovedBy(User $approvedBy)
	{
		$this->Approved_By = $approvedBy;
	}

	/**
	 * Get Approved_By
	 *
	 * @return User
	 */
	public function getApprovedBy()
	{
		return $this->Approved_By;
	}

	/**
	 * Add Matches
	 *
	 * @param Match $match
	 */
	public function addMatches(Match $match)
	{
		$this->Matches[] = $match;
	}

	/**
	 * Get Matches
	 *
	 * @return \Doctrine\Common\Collections\Collection[Match]
	 */
	public function getMatches()
	{
		return $this->Matches;
	}



	/**
	 * Returns safe CURIE for referencing resources in RDFa
	 * @param  string $prefix    The namespace prefix
	 * @return string CURIE
	 */
	public function getCurie($prefix = 'resource')
	{
		return "[$prefix:teammatch/{$this->getId()}]";
	}

	/**
	 * Proxymethod for getTeam1()->getLeague()
	 * Team1 and Team2 of this Teammatch should play in the same league. That means 
	 * getTeam1()->getLeague() should return the same as getTeam2()->getLeague(), but 
	 * that is not evaluated in this call for performance reasons. So it's enough to 
	 * join Team1 with its league.
	 *
	 * @return League 	The league that this teammatch is part of.
	 */
	public function getLeague()
	{
		return $this->getTeam1()->getLeague() !== null ?
			$this->getTeam1()->getLeague() : $this->getTeam2()->getLeague();
	}

	/**
	 * Set League
	 *
	 * @param League $league
	 */
	public function setLeague(League $league)
	{
		$this->getTeam1()->setLeague($league);
		$this->getTeam2()->setLeague($league);
	}

	/**
	 * Gets the slug
	 * Only unique in the right context (per league).
	 *
	 * @return string
	 */
	public function getSlug()
	{
		return 
			$this->getTeam1()->getClub()->getCode() . '-' . $this->getTeam1()->getTeamNumber() . 
			'_' .
			$this->getTeam2()->getClub()->getCode() . '-' . $this->getTeam2()->getTeamNumber() ;
	}

	/**
	 * Splits the slug into its components
	 *
	 * @param string $slug
	 * @return array|false
	 */
	public static function parseSlug($slug)
	{
		$params = array();
		// \w = [A-Za-z0-9_]
		// \d =	[0-9]

		// dadurch, dass die team_number auf Zahlen beschraenk ist (\d), kann der club_code
		// auch die Trennzeichen (- und _) enthalten
		// with named subpatterns: (?<team1_club_code>[\w-üöäÜÖÄ]+)
		preg_match('/^(.+)-(\d+)_(.+)-(\d+)$/', $slug, $params);

		if (count($params) == 0) {
			return false;
		}

		unset($params[0]);
		return array_combine(
			array('team1_club_code', 'team1_number', 'team2_club_code', 'team2_number'), 
			$params
		);
	}

	public function routingParams()
	{
		if (isset($this->homeaway) && $this->homeaway == 'away') {
			return array(
				'owner' => $this->getLeague()->getTournament()->getOwner()->getSlug(),
				'tournament' => $this->getLeague()->getTournament()->getSlug(),
				'league' => $this->getLeague()->getSlug(),
				'team1Club' => $this->getTeam2()->getClub()->getCode(),
				'team1Number' => $this->getTeam2()->getTeamNumber(),
				'team2Club' => $this->getTeam1()->getClub()->getCode(),
				'team2Number' => $this->getTeam1()->getTeamNumber()
				// 'teammatch' => $this->getSlug()
			);
		}
		else {
			return array(
				'owner' => $this->getLeague()->getTournament()->getOwner()->getSlug(),
				'tournament' => $this->getLeague()->getTournament()->getSlug(),
				'league' => $this->getLeague()->getSlug(),
				'team1Club' => $this->getTeam1()->getClub()->getCode(),
				'team1Number' => $this->getTeam1()->getTeamNumber(),
				'team2Club' => $this->getTeam2()->getClub()->getCode(),
				'team2Number' => $this->getTeam2()->getTeamNumber()
				// 'teammatch' => $this->getSlug()
			);
		}
	}


	public function hasResult()
	{
		return $this->getTeam1Score() !== null && $this->getTeam2Score() !== null;
	}

	public function hasDetailedResult()
	{
		return $this->getTeam1Games() !== null && $this->getTeam2Games() !== null;
		//return count($this->Matches) > 0;
	}

	public function isDraw()
	{
		return $this->hasResult() && $this->getTeam1Score() == $this->getTeam2Score() && $this->getTeam1Score() !== 0;
	}

	public function isBothTeamsLost()
	{
		return $this->hasResult() && $this->getTeam1Score() === 0 && $this->getTeam2Score() === 0;
	}

	/**
	 *
	 * @return Team|false|null
	 */
	public function getWinnerTeam()
	{
		if (!$this->hasResult()) return false;
		if ($this->isDraw() || $this->isBothTeamsLost()) return null;

		if ($this->getTeam1Score() > $this->getTeam2Score())
		{
			return $this->getTeam1();
		}
		if ($this->getTeam1Score() < $this->getTeam2Score())
		{
			return $this->getTeam2();
		}

		return false; // catch all
	}


	public function isWinnerTeam(Team $team)
	{
		return is_object($team) && ($team === $this->getWinnerTeam());
	} 

	public function isTeam1Winner()
	{
		return $this->getTeam1Score() > $this->getTeam2Score();
	}

	public function isTeam2Winner()
	{
		return $this->getTeam1Score() < $this->getTeam2Score();
	}




	/**
	 * Höhe des Sieges als sortierbare Zeichenkette
	 * Je höherwertig bei normalem String-Vergleich die Zeichenkette, desto eindeutiger der Sieg für das Team
	 * Betrachtet nicht nur das Spiel-, sondern auch das Satz- und Punktergebnis
	 *
	 * @return string Als String encoded Höhe des Sieges
	 */
	public function getMarginOfVictorySortableString(Team $team = null)
	{
		$chars = array(2, 3, 2, 3);

		if (!$team) {
			return SignedIntToSortableStringConverter::convertArray(
				array(
					abs($this->getTeam1Score() - $this->getTeam2Score()),
					abs($this->getTeam1Score() + $this->getTeam2Score()),
					abs($this->getTeam1Games() - $this->getTeam2Games()),
					abs($this->getTeam1Points() - $this->getTeam2Points())
				),
				$chars
			);
		}
		else if ($this->getTeam1() === $team) {
			return SignedIntToSortableStringConverter::convertArray(
				array(
					$this->getTeam1Score() - $this->getTeam2Score(),
					$this->getTeam1Score() + $this->getTeam2Score(),
					$this->getTeam1Games() - $this->getTeam2Games(),
					$this->getTeam1Points() - $this->getTeam2Points()
				),
				$chars
			);
		}
		else if ($this->getTeam2() === $team) {
			return SignedIntToSortableStringConverter::convertArray(
				array(
					$this->getTeam2Score() - $this->getTeam1Score(),
					$this->getTeam2Score() + $this->getTeam1Score(),
					$this->getTeam2Games() - $this->getTeam1Games(),
					$this->getTeam2Points() - $this->getTeam1Points()
				),
				$chars
			);
		}

		return '';

	}


	/**
	 * Ob Ergebniseingabe fällig ist
	 */
	public function isSubmissionDue()
	{
		$now = new \DateTime('now');
		return $this->getSubmittedById() === null && !$this->hasResult() && $this->getPerformedAt() < $now;
	}


	/**
	 * Ob ursprünglicher Termin ungleich aktueller Termin
	 * @return boolean
	 */
	public function hasDifferentSchedule()
	{
		return $this->getScheduledAt() != $this->getPerformedAt();
	}


	public function getTeam1ScoreVisibly()
	{
		return $this->getTeam1Score() === null ?
			($this->getSubmittedById() === null ? '‒' : '?') :
			$this->getTeam1Score();
	}

	public function getTeam2ScoreVisibly()
	{
		return $this->getTeam2Score() === null ?
			($this->getSubmittedById() === null ? '‒' : '?') :
			$this->getTeam2Score();
	}

	public function getResultVisible()
	{
		return $this->getTeam1ScoreVisibly() . ':' . $this->getTeam2ScoreVisibly(); 
	}


	/**
	 * Transforms the Teammatch so that the teams and scores are specified from the passed Clubs point of view
	 * I.e. Team1 will belong to the Club and Team2 is the opponent
	 * If both Teams belong to the Club the Teammatch will stay unmodified, thus be the home view for Team1.
	 * If neither Team1 nor Team2 belongs to the Club do nothing.
	 */
	public function transformToClubView(Club $club)
	{
		if ($this->getTeam1()->getClubId() != $club->getId() && $this->getTeam2()->getClubId() == $club->getId())
		{
			$this->homeaway = 'away';
			$this->swapTeamData();
		}
		else if ($this->getTeam1()->getClubId() == $club->getId())
		{
			$this->homeaway = 'home';
		}
	}

	public function transformToTeamView(Team $team)
	{
		if ($this->getTeam1Id() != $team->getId() && $this->getTeam2Id() == $team->getId())
		{
			$this->homeaway = 'away';
			$this->swapTeamData();
		}
		else if ($this->getTeam1Id() == $team->getId())
		{
			$this->homeaway = 'home';
		}
	}

	public function getTransformedViewHomeAway()
	{
		return isset($this->homeaway) ? $this->homeaway : null; 
	}

	public function swapTeamData()
	{
		$tmp = $this->team1_id;
		$this->team1_id = $this->team2_id;
		$this->team2_id = $tmp;

		$tmp = $this->Team1;
		$this->Team1 = $this->Team2;
		$this->Team2 = $tmp;

		$tmp = $this->team1_score;
		$this->team1_score = $this->team2_score;
		$this->team2_score = $tmp;

		$tmp = $this->team1_games;
		$this->team1_games = $this->team2_games;
		$this->team2_games = $tmp;

		$tmp = $this->team1_points;
		$this->team1_points = $this->team2_points;
		$this->team2_points = $tmp;

		if ($this->revaluation_wrongdoer == 2) {
			$this->revaluation_wrongdoer = 1;
		}
		else if ($this->revaluation_wrongdoer == 1) {
			$this->revaluation_wrongdoer = 2;
		}
	}

	public function isSameClub()
	{
		return $this->getTeam1()->getClubId() === $this->getTeam2()->getClubId();
	}

	/**
	* Team1 nicht angetreten
	*/
	public function isTeam1NoFight()
	{
		return $this->getNoFight() && ($this->isTeam2Winner() || $this->isBothTeamsLost());
	}

	/**
	* Team2 nicht angetreten
	*/
	public function isTeam2NoFight()
	{
		return $this->getNoFight() && ($this->isTeam1Winner() || $this->isBothTeamsLost());
	}


	public function isTeam1RevaluatedAgainst()
	{
		return $this->getRevaluationWrongdoer() == 1 || $this->getRevaluationWrongdoer() == 3;
	}

	public function isTeam2RevaluatedAgainst()
	{
		return $this->getRevaluationWrongdoer() == 2 || $this->getRevaluationWrongdoer() == 3;
	}
	
	public function getRevaluationAgainst()
	{
		switch ($this->getRevaluationWrongdoer()) {
			case 1:
				return 'team1';
			case 2:
				return 'team2';
			case 3:
				return 'both';
		}

		return '';
	}
	
	public function setRevaluationAgainst($value)
	{
		// TODO (switch to RevaluationWrongdoer)
	}

	/**
	 * Recalculates all statistics, like the score based on the matches and games
	 */
	public function updateStats()
	{
		$team1Matches = $team2Matches = null;
		$team1Games = $team2Games = null;
		$team1Points = $team2Points = null;

		$incompleteLineup = false;
		$revisedScore = false;

		foreach ($this->Matches as $match) {
			/** @var $match Match */
			$match->updateStats();

			$incompleteLineup = $incompleteLineup ||
				$match->getTeam1Player() === null || $match->getTeam2Player() === null;

			$revisedScore = $revisedScore || $match->getRevisedScore();
			
			$team1Matches += $match->isDraw() ? 0.5 : $match->isTeam1Winner();
			$team2Matches += $match->isDraw() ? 0.5 : $match->isTeam2Winner();

			$team1Games += $match->getTeam1Score();
			$team2Games += $match->getTeam2Score();

			$team1Points += $match->getTeam1Points();
			$team2Points += $match->getTeam2Points();
		}

		$this->setTeam1Score($team1Matches);
		$this->setTeam2Score($team2Matches);

		$this->setTeam1Games($team1Games);
		$this->setTeam2Games($team2Games);

		$this->setTeam1Points($team1Points);
		$this->setTeam2Points($team2Points);

		// TODO
//		$this->setNoFight(); // bei Nicht-Antreten einer Mannschaft
		$this->setRevisedScore($revisedScore);
		$this->setIncompleteLineup($incompleteLineup);
		
	}


	function __toString()
	{
		if ($this->hasResult() || $this->getSubmittedById() !== null) {
			return sprintf('%s – %s = %s',
				$this->getTeam1(), $this->getTeam2(), 
				$this->getResultVisible()
			);
		} else {
			return sprintf('%s – %s @ %s',
				$this->getTeam1(), $this->getTeam2(), 
				$this->getPerformedAt()->format('d.m.Y H:i')
			);
		}
	}


}