<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Clubs
 *
 * @Table(name="clubs")
 * @Entity
 */
class Clubs
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
     * @var string $code
     *
     * @Column(name="code", type="string", length=10, nullable=false)
     */
    private $code;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string $website
     *
     * @Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string $logo
     *
     * @Column(name="logo", type="string", length=100, nullable=true)
     */
    private $logo;

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
     *   @JoinColumn(name="club_syndicate_1", referencedColumnName="id")
     * })
     */
    private $clubSyndicate1;

    /**
     * @var Clubs
     *
     * @ManyToOne(targetEntity="Clubs")
     * @JoinColumns({
     *   @JoinColumn(name="club_syndicate_2", referencedColumnName="id")
     * })
     */
    private $clubSyndicate2;

    /**
     * @var Clubs
     *
     * @ManyToOne(targetEntity="Clubs")
     * @JoinColumns({
     *   @JoinColumn(name="club_syndicate_3", referencedColumnName="id")
     * })
     */
    private $clubSyndicate3;

    /**
     * @var Clubs
     *
     * @ManyToOne(targetEntity="Clubs")
     * @JoinColumns({
     *   @JoinColumn(name="club_syndicate_4", referencedColumnName="id")
     * })
     */
    private $clubSyndicate4;

    /**
     * @var Athletes
     *
     * @ManyToOne(targetEntity="Athletes")
     * @JoinColumns({
     *   @JoinColumn(name="contact_person_id", referencedColumnName="id")
     * })
     */
    private $contactPerson;


}