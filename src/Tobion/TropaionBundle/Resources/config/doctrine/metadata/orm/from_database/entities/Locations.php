<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Locations
 *
 * @Table(name="locations")
 * @Entity
 */
class Locations
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
     * @var string $latitude
     *
     * @Column(name="latitude", type="string", length=10, nullable=false)
     */
    private $latitude;

    /**
     * @var string $longitude
     *
     * @Column(name="longitude", type="string", length=10, nullable=false)
     */
    private $longitude;

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


}