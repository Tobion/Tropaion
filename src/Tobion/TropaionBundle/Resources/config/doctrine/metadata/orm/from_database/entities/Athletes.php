<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Athletes
 *
 * @Table(name="athletes")
 * @Entity
 */
class Athletes
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
     * @var string $lastName
     *
     * @Column(name="last_name", type="string", length=20, nullable=false)
     */
    private $lastName;

    /**
     * @var string $firstName
     *
     * @Column(name="first_name", type="string", length=20, nullable=false)
     */
    private $firstName;

    /**
     * @var string $gender
     *
     * @Column(name="gender", type="string", length=6, nullable=false)
     */
    private $gender;

    /**
     * @var date $birthday
     *
     * @Column(name="birthday", type="date", nullable=false)
     */
    private $birthday;

    /**
     * @var string $country
     *
     * @Column(name="country", type="string", length=2, nullable=false)
     */
    private $country;

    /**
     * @var boolean $isActive
     *
     * @Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var string $zip
     *
     * @Column(name="zip", type="string", length=10, nullable=false)
     */
    private $zip;

    /**
     * @var string $city
     *
     * @Column(name="city", type="string", length=70, nullable=false)
     */
    private $city;

    /**
     * @var string $street
     *
     * @Column(name="street", type="string", length=70, nullable=false)
     */
    private $street;

    /**
     * @var smallint $singlesRating
     *
     * @Column(name="singles_rating", type="smallint", nullable=true)
     */
    private $singlesRating;

    /**
     * @var smallint $doublesRating
     *
     * @Column(name="doubles_rating", type="smallint", nullable=true)
     */
    private $doublesRating;

    /**
     * @var smallint $mixedRating
     *
     * @Column(name="mixed_rating", type="smallint", nullable=true)
     */
    private $mixedRating;

    /**
     * @var smallint $singlesMatches
     *
     * @Column(name="singles_matches", type="smallint", nullable=false)
     */
    private $singlesMatches;

    /**
     * @var smallint $doublesMatches
     *
     * @Column(name="doubles_matches", type="smallint", nullable=false)
     */
    private $doublesMatches;

    /**
     * @var smallint $mixedMatches
     *
     * @Column(name="mixed_matches", type="smallint", nullable=false)
     */
    private $mixedMatches;

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
     * @var Clubs
     *
     * @ManyToOne(targetEntity="Clubs")
     * @JoinColumns({
     *   @JoinColumn(name="club_id", referencedColumnName="id")
     * })
     */
    private $club;


}