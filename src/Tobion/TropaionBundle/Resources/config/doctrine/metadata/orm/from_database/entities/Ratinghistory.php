<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Ratinghistory
 *
 * @Table(name="ratinghistory")
 * @Entity
 */
class Ratinghistory
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
     * @var string $discipline
     *
     * @Column(name="discipline", type="string", length=7, nullable=false)
     */
    private $discipline;

    /**
     * @var smallint $rating
     *
     * @Column(name="rating", type="smallint", nullable=true)
     */
    private $rating;

    /**
     * @var smallint $numberMatches
     *
     * @Column(name="number_matches", type="smallint", nullable=false)
     */
    private $numberMatches;

    /**
     * @var date $createdAt
     *
     * @Column(name="created_at", type="date", nullable=false)
     */
    private $createdAt;

    /**
     * @var Athletes
     *
     * @ManyToOne(targetEntity="Athletes")
     * @JoinColumns({
     *   @JoinColumn(name="athlete_id", referencedColumnName="id")
     * })
     */
    private $athlete;


}