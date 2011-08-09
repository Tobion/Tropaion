<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Teammatches
 *
 * @Table(name="teammatches")
 * @Entity
 */
class Teammatches
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var datetime $scheduledAt
     *
     * @Column(name="scheduled_at", type="datetime", nullable=false)
     */
    private $scheduledAt;

    /**
     * @var datetime $performedAt
     *
     * @Column(name="performed_at", type="datetime", nullable=false)
     */
    private $performedAt;

    /**
     * @var boolean $seasonRound
     *
     * @Column(name="season_round", type="boolean", nullable=false)
     */
    private $seasonRound;

    /**
     * @var boolean $team1Score
     *
     * @Column(name="team1_score", type="boolean", nullable=true)
     */
    private $team1Score;

    /**
     * @var boolean $team2Score
     *
     * @Column(name="team2_score", type="boolean", nullable=true)
     */
    private $team2Score;

    /**
     * @var boolean $team1Games
     *
     * @Column(name="team1_games", type="boolean", nullable=true)
     */
    private $team1Games;

    /**
     * @var boolean $team2Games
     *
     * @Column(name="team2_games", type="boolean", nullable=true)
     */
    private $team2Games;

    /**
     * @var smallint $team1Points
     *
     * @Column(name="team1_points", type="smallint", nullable=true)
     */
    private $team1Points;

    /**
     * @var smallint $team2Points
     *
     * @Column(name="team2_points", type="smallint", nullable=true)
     */
    private $team2Points;

    /**
     * @var boolean $noFight
     *
     * @Column(name="no_fight", type="boolean", nullable=false)
     */
    private $noFight;

    /**
     * @var boolean $annulled
     *
     * @Column(name="annulled", type="boolean", nullable=false)
     */
    private $annulled;

    /**
     * @var boolean $revisedScore
     *
     * @Column(name="revised_score", type="boolean", nullable=false)
     */
    private $revisedScore;

    /**
     * @var boolean $wrongdoer
     *
     * @Column(name="wrongdoer", type="boolean", nullable=false)
     */
    private $wrongdoer;

    /**
     * @var boolean $wrongdoingReason
     *
     * @Column(name="wrongdoing_reason", type="boolean", nullable=false)
     */
    private $wrongdoingReason;

    /**
     * @var boolean $incompleteLineup
     *
     * @Column(name="incomplete_lineup", type="boolean", nullable=false)
     */
    private $incompleteLineup;

    /**
     * @var datetime $submittedAt
     *
     * @Column(name="submitted_at", type="datetime", nullable=true)
     */
    private $submittedAt;

    /**
     * @var datetime $confirmedAt
     *
     * @Column(name="confirmed_at", type="datetime", nullable=true)
     */
    private $confirmedAt;

    /**
     * @var datetime $approvedAt
     *
     * @Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;

    /**
     * @var text $description
     *
     * @Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var datetime $createdAt
     *
     * @Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var Users
     *
     * @ManyToOne(targetEntity="Users")
     * @JoinColumns({
     *   @JoinColumn(name="approved_by_id", referencedColumnName="id")
     * })
     */
    private $approvedBy;

    /**
     * @var Users
     *
     * @ManyToOne(targetEntity="Users")
     * @JoinColumns({
     *   @JoinColumn(name="confirmed_by_id", referencedColumnName="id")
     * })
     */
    private $confirmedBy;

    /**
     * @var Locations
     *
     * @ManyToOne(targetEntity="Locations")
     * @JoinColumns({
     *   @JoinColumn(name="location_id", referencedColumnName="id")
     * })
     */
    private $location;

    /**
     * @var Users
     *
     * @ManyToOne(targetEntity="Users")
     * @JoinColumns({
     *   @JoinColumn(name="submitted_by_id", referencedColumnName="id")
     * })
     */
    private $submittedBy;

    /**
     * @var Teams
     *
     * @ManyToOne(targetEntity="Teams")
     * @JoinColumns({
     *   @JoinColumn(name="team1_id", referencedColumnName="id")
     * })
     */
    private $team1;

    /**
     * @var Teams
     *
     * @ManyToOne(targetEntity="Teams")
     * @JoinColumns({
     *   @JoinColumn(name="team2_id", referencedColumnName="id")
     * })
     */
    private $team2;


}