<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Lineups
 *
 * @Table(name="lineups")
 * @Entity
 */
class Lineups
{
    /**
     * @var integer $teamId
     *
     * @Column(name="team_id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $teamId;

    /**
     * @var boolean $seasonRound
     *
     * @Column(name="season_round", type="boolean", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $seasonRound;

    /**
     * @var integer $athleteId
     *
     * @Column(name="athlete_id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $athleteId;

    /**
     * @var boolean $position
     *
     * @Column(name="position", type="boolean", nullable=false)
     */
    private $position;

    /**
     * @var Athletes
     *
     * @ManyToOne(targetEntity="Athletes")
     * @JoinColumns({
     *   @JoinColumn(name="athlete_id", referencedColumnName="id")
     * })
     */
    private $athlete;

    /**
     * @var Teams
     *
     * @ManyToOne(targetEntity="Teams")
     * @JoinColumns({
     *   @JoinColumn(name="team_id", referencedColumnName="id")
     * })
     */
    private $team;


}