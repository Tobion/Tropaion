<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tobion\TropaionBundle\Util\SignedIntToSortableStringConverter;

/**
 * Tobion\TropaionBundle\Entity\Teammatch
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
     * @var datetime $scheduled_at
     *
     * @ORM\Column(type="datetime")
     */
    private $scheduled_at;
	
	/**
     * @var datetime $moved_at
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $moved_at;

    /**
     * @var datetime $performed_at
     *
     * @ORM\Column(type="datetime")
     */
    private $performed_at;

    /**
     * @var integer $season_round
     *
     * @ORM\Column(type="smallint")
     */
    private $season_round;

    /**
     * @var integer $team1_id
     *
     * @ORM\Column(type="integer")
     */
    private $team1_id;

    /**
     * @var integer $team2_id
     *
     * @ORM\Column(type="integer")
     */
    private $team2_id;

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
     * @var integer $team1_games
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $team1_games;

    /**
     * @var integer $team2_games
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $team2_games;

    /**
     * @var integer $team1_points
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $team1_points;

    /**
     * @var integer $team2_points
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $team2_points;

    /**
     * @var boolean $no_fight
     *
     * @ORM\Column(type="boolean")
     */
    private $no_fight = false;

    /**
     * @var boolean $annulled
     *
     * @ORM\Column(type="boolean")
     */
    private $annulled = false;

    /**
     * @var boolean $revised_score
     *
     * @ORM\Column(type="boolean")
     */
    private $revised_score = false;

    /**
     * @var integer $revaluation_wrongdoer
     *
     * @ORM\Column(type="smallint")
     */
    private $revaluation_wrongdoer = 0;

    /**
     * @var integer $revaluation_reason
     *
     * @ORM\Column(type="smallint")
     */
    private $revaluation_reason = 0;

    /**
     * @var boolean $incomplete_lineup
     *
     * @ORM\Column(type="boolean")
     */
    private $incomplete_lineup;

    /**
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
    private $description;
	
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
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id", onDelete="SET NULL", onUpdate="CASCADE")
     */
    private $Venue;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team1_id", referencedColumnName="id")
     */
    private $Team1;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team2_id", referencedColumnName="id")
     */
    private $Team2;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="submitted_by_id", referencedColumnName="id")
     */
    private $Submitted_By;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="confirmed_by_id", referencedColumnName="id")
     */
    private $Confirmed_By;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="approved_by_id", referencedColumnName="id")
     */
    private $Approved_By;
	
	/**
     * @var Match
     *
     * @ORM\OneToMany(targetEntity="Match", mappedBy="Teammatch")
     * @ORM\JoinColumn(name="id", referencedColumnName="teammatch_id")
     */
    private $Matches;


    public function __construct()
    {
		$this->created_at = $this->updated_at = new DateTime('now');
        $this->Matches = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return integer $venueId
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
     * @return datetime $scheduledAt
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
     * @return datetime $movedAt
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
     * @return datetime $performedAt
     */
    public function getPerformedAt()
    {
        return $this->performed_at;
    }

    /**
     * Set season_round
     *
     * @param smallint $seasonRound
     */
    public function setSeasonRound($seasonRound)
    {
        $this->season_round = $seasonRound;
    }

    /**
     * Get season_round
     *
     * @return smallint $seasonRound
     */
    public function getSeasonRound()
    {
        return $this->season_round;
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
     * @return integer $team1Id
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
     * @return integer $team2Id
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
     * @return smallint $team1Games
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
     * @return smallint $team2Games
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
     * @return smallint $revaluationWrongdoer
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
     * @return smallint $revaluationReason
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
     * @return boolean $incompleteLineup
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
     * @return integer $submittedById
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
     * @return datetime $submittedAt
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
     * @return integer $confirmedById
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
     * @return datetime $confirmedAt
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
     * @return integer $approvedById
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
     * @return datetime $approvedAt
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
     * @return text $description
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
     * @return datetime $createdAt
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
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set Venue
     *
     * @param Tobion\TropaionBundle\Entity\Venue $venue
     */
    public function setVenue(\Tobion\TropaionBundle\Entity\Venue $venue)
    {
        $this->Venue = $venue;
    }

    /**
     * Get Venue
     *
     * @return Tobion\TropaionBundle\Entity\Venue $venue
     */
    public function getVenue()
    {
        return $this->Venue;
    }

    /**
     * Set Team1
     *
     * @param Tobion\TropaionBundle\Entity\Team $team1
     */
    public function setTeam1(\Tobion\TropaionBundle\Entity\Team $team1)
    {
        $this->Team1 = $team1;
    }

    /**
     * Get Team1
     *
     * @return Tobion\TropaionBundle\Entity\Team $team1
     */
    public function getTeam1()
    {
        return $this->Team1;
    }

    /**
     * Set Team2
     *
     * @param Tobion\TropaionBundle\Entity\Team $team2
     */
    public function setTeam2(\Tobion\TropaionBundle\Entity\Team $team2)
    {
        $this->Team2 = $team2;
    }

    /**
     * Get Team2
     *
     * @return Tobion\TropaionBundle\Entity\Team $team2
     */
    public function getTeam2()
    {
        return $this->Team2;
    }

    /**
     * Set Submitted_By
     *
     * @param Tobion\TropaionBundle\Entity\User $submittedBy
     */
    public function setSubmittedBy(\Tobion\TropaionBundle\Entity\User $submittedBy)
    {
        $this->Submitted_By = $submittedBy;
    }

    /**
     * Get Submitted_By
     *
     * @return Tobion\TropaionBundle\Entity\User $submittedBy
     */
    public function getSubmittedBy()
    {
        return $this->Submitted_By;
    }

    /**
     * Set Confirmed_By
     *
     * @param Tobion\TropaionBundle\Entity\User $confirmedBy
     */
    public function setConfirmedBy(\Tobion\TropaionBundle\Entity\User $confirmedBy)
    {
        $this->Confirmed_By = $confirmedBy;
    }

    /**
     * Get Confirmed_By
     *
     * @return Tobion\TropaionBundle\Entity\User $confirmedBy
     */
    public function getConfirmedBy()
    {
        return $this->Confirmed_By;
    }

    /**
     * Set Approved_By
     *
     * @param Tobion\TropaionBundle\Entity\User $approvedBy
     */
    public function setApprovedBy(\Tobion\TropaionBundle\Entity\User $approvedBy)
    {
        $this->Approved_By = $approvedBy;
    }

    /**
     * Get Approved_By
     *
     * @return Tobion\TropaionBundle\Entity\User $approvedBy
     */
    public function getApprovedBy()
    {
        return $this->Approved_By;
    }

    /**
     * Add Matches
     *
     * @param Tobion\TropaionBundle\Entity\Match $matches
     */
    public function addMatches(\Tobion\TropaionBundle\Entity\Match $matches)
    {
        $this->Matches[] = $matches;
    }

    /**
     * Get Matches
     *
     * @return Doctrine\Common\Collections\Collection $matches
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
	 * @return Tobion\TropaionBundle\Entity\League 	The league that this teammatch is part of.
	 */
	public function getLeague()
    {
        return !is_null($this->getTeam1()->getLeague()) ? $this->getTeam1()->getLeague() : $this->getTeam2()->getLeague();
    }
	
	public function setLeague(\Tobion\TropaionBundle\Entity\League $league)
    {
        $this->getTeam1()->setLeague($league);
		$this->getTeam2()->setLeague($league);
    }
	
	/**
	* Only unique in the right context (per league).
	*/
	public function getSlug()
    {
    	return 
    		$this->getTeam1()->getClub()->getCode() . '-' . $this->getTeam1()->getTeamNumber() . 
    		'_' .
    		$this->getTeam2()->getClub()->getCode() . '-' . $this->getTeam2()->getTeamNumber() ;
    }
    
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
        return !is_null($this->getTeam1Score()) && !is_null($this->getTeam2Score());
	}
	
	public function hasDetailedResult()
	{
		return !is_null($this->getTeam1Games()) && !is_null($this->getTeam2Games());
        //return count($this->Matches) > 0;
	}
	
	public function isDraw()
	{
        return $this->hasResult() && $this->getTeam1Score() == $this->getTeam2Score() && $this->getTeam1Score() != 0;
	}
	
	public function isBothTeamsLost()
	{
        return $this->hasResult() && ($this->getTeam1Score() == 0 && $this->getTeam2Score() == 0);
	}

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
	 * Höhe des Sieges als vorzeichenbehaftete ganze Zahl (als Float weil Zahl sehr groß)
	 * Je höher die Zahl, desto eindeutiger der Sieg für das Team
	 * Betrachtet nicht nur Spiel-, sondern auch Satz- und Punktergebnis
	 *
	 * Nur Prototyp, da nicht immer korrekt (bei negativen Zwischenwerten).
	 * Außerdem Problem wegen der entstehenden großen Zahlen -> nicht beliebig erweiterbar.
	 *
	 * @return float
	 
	public function getMarginOfVictoryFloat(Team $team)
	{
		$mov = 0;
		if ($this->getTeam1() === $team) {
			$mov = $this->getTeam1Score() - $this->getTeam2Score();
			$mov = $mov * 100;
			$mov = $mov + $this->getTeam1Score() + $this->getTeam2Score();
			$mov = $mov * 100;
			if (!is_null($this->getTeam1Games()) && !is_null($this->getTeam2Games())) {
				$mov = $mov + $this->getTeam1Games() - $this->getTeam2Games();
			}
			$mov = $mov * 100;
			if (!is_null($this->getTeam1Games()) && !is_null($this->getTeam2Games())) {
				$mov = $mov + $this->getTeam1Games() + $this->getTeam2Games();
			}
			$mov = $mov * 1000;
			if (!is_null($this->getTeam1Points()) && !is_null($this->getTeam2Points())) {
				$mov = $mov + $this->getTeam1Points() - $this->getTeam2Points();
			}
			$mov = $mov * 10000;
			if (!is_null($this->getTeam1Points()) && !is_null($this->getTeam2Points())) {
				$mov = $mov + $this->getTeam1Points() + $this->getTeam2Points();
			}
		}
		else {
			$mov = $this->getTeam2Score() - $this->getTeam1Score();
			$mov = $mov * 100;
			$mov = $mov + $this->getTeam2Score() + $this->getTeam1Score();
			$mov = $mov * 100;
			if (!is_null($this->getTeam1Games()) && !is_null($this->getTeam2Games())) {
				$mov = $mov + $this->getTeam2Games() - $this->getTeam1Games();
			}
			$mov = $mov * 100;
			if (!is_null($this->getTeam1Games()) && !is_null($this->getTeam2Games())) {
				$mov = $mov + $this->getTeam2Games() + $this->getTeam1Games();
			}
			$mov = $mov * 1000;
			if (!is_null($this->getTeam1Points()) && !is_null($this->getTeam2Points())) {
				$mov = $mov + $this->getTeam2Points() - $this->getTeam1Points();
			}
			$mov = $mov * 10000;
			if (!is_null($this->getTeam1Points()) && !is_null($this->getTeam2Points())) {
				$mov = $mov + $this->getTeam2Points() + $this->getTeam1Points();
			}
		}

		return $mov;
		
	}
	*/
	
	/**
	 * Ob Ergebniseingabe fällig ist
	 */
	public function isSubmissionDue()
	{
		$now = new \DateTime('now');
		return is_null($this->getSubmittedById()) && !$this->hasResult() && $this->getPerformedAt() < $now;
	}
	
	
	/**
     * Summe aller gewonnenen Saetze von Team 1 ueber alle Individualspiele des Teamspiels
     *
     * @return integer
     */
	public function sumTeam1WonGames()
	{
		$sum = 0;
		foreach ($this->Matches as $match) {
			$sum += $match->numberTeam1WonGames();
		}
		return $sum;
	}
	
	/**
     * Summe aller gewonnenen Saetze von Team 2 ueber alle Individualspiele des Teamspiels
     *
     * @return integer
     */
	public function sumTeam2WonGames()
	{
		$sum = 0;
		foreach ($this->Matches as $match) {
			$sum += $match->numberTeam2WonGames();
		}
		return $sum;
	}
	
	/**
     * Summe aller gewonnenen Punkte von Team 1 ueber alle Individualspiele und Saetze des Teamspiels
     *
     * @return integer
     */
	public function sumTeam1Score()
	{
		$sum = 0;
		foreach ($this->Matches as $match) {
			$sum += $match->sumTeam1Score();
		}
		return $sum;
	}
	
	/**
     * Summe aller gewonnenen Punkte von Team 2 ueber alle Individualspiele und Saetze des Teamspiels
     *
     * @return integer
     */
	public function sumTeam2Score()
	{
		$sum = 0;
		foreach ($this->Matches as $match) {
			$sum += $match->sumTeam2Score();
		}
		return $sum;
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
        return is_null($this->getTeam1Score()) ? '?' : $this->getTeam1Score();
	}
    
    public function getTeam2ScoreVisibly()
	{
        return is_null($this->getTeam2Score()) ? '?' : $this->getTeam2Score();
	}
      
    public function getResultVisible()
	{
        return $this->getTeam1ScoreVisibly() . ' : ' . $this->getTeam2ScoreVisibly(); 
	}


	/**
	 * Transforms the Teammatch so that the teams and scores are specified from the passed Clubs point of view
	 * I.e. Team1 will belong to the Club and Team2 is the opponent
	 * If both Teams belong to the Club the Teammatch will stay unmodified, thus be the home view for Team1.
	 * If neither Team1 nor Team2 belongs to the Club do nothing.
	 */
	public function transformToClubView(\Tobion\TropaionBundle\Entity\Club $club)
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

	public function transformToTeamView(\Tobion\TropaionBundle\Entity\Team $team)
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
	

	function __toString()
    {
		if ($this->hasResult()) {
			return sprintf('%s – %s = %s:%s',
				$this->getTeam1(), $this->getTeam2(), 
				$this->getTeam1Score(), $this->getTeam2Score()
			);
		} elseif ($this->getSubmittedById()) {
			return sprintf('%s – %s = ‒:‒',
				$this->getTeam1(), $this->getTeam2()
			);
		} else {
			return sprintf('%s – %s @ %s',
				$this->getTeam1(), $this->getTeam2(), 
				$this->getPerformedAt()->format('d.m.Y H:i')
			);
		}
		/*
        return sprintf('%s – %s = %s',
            $this->getTeam1(), $this->getTeam2(), 
            $this->getResultVisible()
        );
		*/
    }


}