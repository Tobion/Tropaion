<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Users
 *
 * @Table(name="users")
 * @Entity
 */
class Users
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
     * @var string $username
     *
     * @Column(name="username", type="string", length=50, nullable=false)
     */
    private $username;

    /**
     * @var string $email
     *
     * @Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

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
     * @var Athletes
     *
     * @ManyToOne(targetEntity="Athletes")
     * @JoinColumns({
     *   @JoinColumn(name="athlete_id", referencedColumnName="id")
     * })
     */
    private $athlete;


}