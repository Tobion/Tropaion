<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Leagues
 *
 * @Table(name="leagues")
 * @Entity
 */
class Leagues
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
     * @var string $season
     *
     * @Column(name="season", type="string", length=10, nullable=false)
     */
    private $season;

    /**
     * @var string $leagueAbbr
     *
     * @Column(name="league_abbr", type="string", length=10, nullable=false)
     */
    private $leagueAbbr;

    /**
     * @var string $leagueName
     *
     * @Column(name="league_name", type="string", length=50, nullable=false)
     */
    private $leagueName;

    /**
     * @var boolean $division
     *
     * @Column(name="division", type="boolean", nullable=false)
     */
    private $division;

    /**
     * @var boolean $level
     *
     * @Column(name="level", type="boolean", nullable=false)
     */
    private $level;

    /**
     * @var boolean $promotedNumber
     *
     * @Column(name="promoted_number", type="boolean", nullable=false)
     */
    private $promotedNumber;

    /**
     * @var boolean $relegatedNumber
     *
     * @Column(name="relegated_number", type="boolean", nullable=false)
     */
    private $relegatedNumber;


}