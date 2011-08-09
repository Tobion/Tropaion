<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Teams
 *
 * @Table(name="teams")
 * @Entity
 */
class Teams
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
     * @var boolean $teamNumber
     *
     * @Column(name="team_number", type="boolean", nullable=false)
     */
    private $teamNumber;

    /**
     * @var boolean $withdrawn
     *
     * @Column(name="withdrawn", type="boolean", nullable=false)
     */
    private $withdrawn;

    /**
     * @var text $description
     *
     * @Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var datetime $updatedAt
     *
     * @Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var Clubs
     *
     * @ManyToOne(targetEntity="Clubs")
     * @JoinColumns({
     *   @JoinColumn(name="club_id", referencedColumnName="id")
     * })
     */
    private $club;

    /**
     * @var Leagues
     *
     * @ManyToOne(targetEntity="Leagues")
     * @JoinColumns({
     *   @JoinColumn(name="league_id", referencedColumnName="id")
     * })
     */
    private $league;


}