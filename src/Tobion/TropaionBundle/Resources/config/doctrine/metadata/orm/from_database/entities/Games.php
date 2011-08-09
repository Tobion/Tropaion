<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Games
 *
 * @Table(name="games")
 * @Entity
 */
class Games
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
     * @var boolean $gameSequence
     *
     * @Column(name="game_sequence", type="boolean", nullable=false)
     */
    private $gameSequence;

    /**
     * @var boolean $team1Score
     *
     * @Column(name="team1_score", type="boolean", nullable=false)
     */
    private $team1Score;

    /**
     * @var boolean $team2Score
     *
     * @Column(name="team2_score", type="boolean", nullable=false)
     */
    private $team2Score;

    /**
     * @var boolean $annulled
     *
     * @Column(name="annulled", type="boolean", nullable=false)
     */
    private $annulled;

    /**
     * @var Matches
     *
     * @ManyToOne(targetEntity="Matches")
     * @JoinColumns({
     *   @JoinColumn(name="match_id", referencedColumnName="id")
     * })
     */
    private $match;


}