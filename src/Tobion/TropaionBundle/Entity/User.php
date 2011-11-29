<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $username
     *
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $username;
	
	/**
     * @var string $slug
     *
     * @ORM\Column(type="string", length=40, unique=true)
     */
    private $slug;

    /**
     * @var string $email
     *
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var integer $athlete_id
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $athlete_id;
	
    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @var Athlete
     *
     * @ORM\OneToOne(targetEntity="Athlete", inversedBy="User")
     * @ORM\JoinColumn(name="athlete_id", referencedColumnName="id", unique=true)
     */
    private $Athlete;


	public function __construct()
    {
		$this->created_at = new DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     */
    public function updated()
    {
        $this->updated_at = new DateTime('now');
    }


    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }
	
    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set athlete_id
     *
     * @param integer $athleteId
     */
    public function setAthleteId($athleteId)
    {
        $this->athlete_id = $athleteId;
    }

    /**
     * Get athlete_id
     *
     * @return integer $athleteId
     */
    public function getAthleteId()
    {
        return $this->athlete_id;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set Athlete
     *
     * @param Tobion\TropaionBundle\Entity\Athlete $athlete
     */
    public function setAthlete(\Tobion\TropaionBundle\Entity\Athlete $athlete)
    {
        $this->Athlete = $athlete;
    }

    /**
     * Get Athlete
     *
     * @return Tobion\TropaionBundle\Entity\Athlete $athlete
     */
    public function getAthlete()
    {
        return $this->Athlete;
    }

	
	public function routingParams()
    {
        return array(
			'user' => $this->getSlug()
        );
    }

	function __toString()
    {
    	return $this->getUsername();
    }

}