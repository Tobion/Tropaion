<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Matches
 *
 * @Table(name="matches")
 * @Entity
 */
class Matches
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
     * @var string $matchType
     *
     * @Column(name="match_type", type="string", length=10, nullable=false)
     */
    private $matchType;

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
     * @var smallint $team1Points
     *
     * @Column(name="team1_points", type="smallint", nullable=false)
     */
    private $team1Points;

    /**
     * @var smallint $team2Points
     *
     * @Column(name="team2_points", type="smallint", nullable=false)
     */
    private $team2Points;

    /**
     * @var boolean $noFight
     *
     * @Column(name="no_fight", type="boolean", nullable=false)
     */
    private $noFight;

    /**
     * @var boolean $withdrawnBy
     *
     * @Column(name="withdrawn_by", type="boolean", nullable=false)
     */
    private $withdrawnBy;

    /**
     * @var boolean $revisedScore
     *
     * @Column(name="revised_score", type="boolean", nullable=false)
     */
    private $revisedScore;

    /**
     * @var boolean $team1OriginalScore
     *
     * @Column(name="team1_original_score", type="boolean", nullable=true)
     */
    private $team1OriginalScore;

    /**
     * @var boolean $team2OriginalScore
     *
     * @Column(name="team2_original_score", type="boolean", nullable=true)
     */
    private $team2OriginalScore;

    /**
     * @var smallint $team1OriginalPoints
     *
     * @Column(name="team1_original_points", type="smallint", nullable=true)
     */
    private $team1OriginalPoints;

    /**
     * @var smallint $team2OriginalPoints
     *
     * @Column(name="team2_original_points", type="smallint", nullable=true)
     */
    private $team2OriginalPoints;

    /**
     * @var smallint $avgOrigSmallerDivBiggerGamescorePermil
     *
     * @Column(name="avg_orig_smaller_div_bigger_gamescore_permil", type="smallint", nullable=true)
     */
    private $avgOrigSmallerDivBiggerGamescorePermil;

    /**
     * @var smallint $stdOrigSmallerDivBiggerGamescorePermil
     *
     * @Column(name="std_orig_smaller_div_bigger_gamescore_permil", type="smallint", nullable=true)
     */
    private $stdOrigSmallerDivBiggerGamescorePermil;

    /**
     * @var datetime $updatedAt
     *
     * @Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var Athletes
     *
     * @ManyToOne(targetEntity="Athletes")
     * @JoinColumns({
     *   @JoinColumn(name="team1_partner_id", referencedColumnName="id")
     * })
     */
    private $team1Partner;

    /**
     * @var Athletes
     *
     * @ManyToOne(targetEntity="Athletes")
     * @JoinColumns({
     *   @JoinColumn(name="team1_player_id", referencedColumnName="id")
     * })
     */
    private $team1Player;

    /**
     * @var Athletes
     *
     * @ManyToOne(targetEntity="Athletes")
     * @JoinColumns({
     *   @JoinColumn(name="team2_partner_id", referencedColumnName="id")
     * })
     */
    private $team2Partner;

    /**
     * @var Athletes
     *
     * @ManyToOne(targetEntity="Athletes")
     * @JoinColumns({
     *   @JoinColumn(name="team2_player_id", referencedColumnName="id")
     * })
     */
    private $team2Player;

    /**
     * @var Teammatches
     *
     * @ManyToOne(targetEntity="Teammatches")
     * @JoinColumns({
     *   @JoinColumn(name="teammatch_id", referencedColumnName="id")
     * })
     */
    private $teammatch;


}