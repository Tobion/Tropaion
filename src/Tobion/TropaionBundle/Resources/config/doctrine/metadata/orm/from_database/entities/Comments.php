<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Comments
 *
 * @Table(name="comments")
 * @Entity
 */
class Comments
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
     * @var integer $matchId
     *
     * @Column(name="match_id", type="integer", nullable=true)
     */
    private $matchId;

    /**
     * @var string $title
     *
     * @Column(name="title", type="string", length=100, nullable=false)
     */
    private $title;

    /**
     * @var text $message
     *
     * @Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var Users
     *
     * @ManyToOne(targetEntity="Users")
     * @JoinColumns({
     *   @JoinColumn(name="author_id", referencedColumnName="id")
     * })
     */
    private $author;

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