<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Athlete
 *
 * @ORM\Table(name="athletes",indexes={@ORM\index(name="is_active_index", columns={"is_active"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Athlete
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
	 * @var string $last_name
	 *
	 * @ORM\Column(type="string", length=20)
	 */
	private $last_name;

	/**
	 * @var string $first_name
	 *
	 * @ORM\Column(type="string", length=20)
	 */
	private $first_name;

	/**
	 * @var string $gender
	 *
	 * @ORM\Column(type="string", length=6)
	 */
	private $gender = 'male';

	/**
	 * @var date $birthday
	 *
	 * @ORM\Column(type="date")
	 */
	private $birthday;

	/**
	 * @var string $country
	 *
	 * @ORM\Column(type="string", length=2)
	 */
	private $country = '';

	/**
	 * @var boolean $is_active
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $is_active = true;

	/**
	 * @var string $zip
	 *
	 * @ORM\Column(type="string", length=10)
	 */
	private $zip = '';

	/**
	 * @var string $city
	 *
	 * @ORM\Column(type="string", length=70)
	 */
	private $city = '';

	/**
	 * @var string $street
	 *
	 * @ORM\Column(type="string", length=70)
	 */
	private $street = '';

	/**
	 * @var integer $singles_rating
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $singles_rating;

	/**
	 * @var integer $doubles_rating
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $doubles_rating;

	/**
	 * @var integer $mixed_rating
	 *
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $mixed_rating;

	/**
	 * @var integer $singles_matches
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $singles_matches = 0;

	/**
	 * @var integer $doubles_matches
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $doubles_matches = 0;

	/**
	 * @var integer $mixed_matches
	 *
	 * @ORM\Column(type="smallint")
	 */
	private $mixed_matches = 0;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $created_at;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updated_at;

	/**
	 * @var Club
	 *
	 * @ORM\ManyToOne(targetEntity="Club")
	 * @ORM\JoinColumn(name="club_id", referencedColumnName="id", nullable=false)
	 */
	private $Club;

	// OneToOne(targetEntity="User", mappedBy="Athlete")
	// private $User;
	// Do not specify inverse side of a one-to-one relation because
	// http://www.doctrine-project.org/docs/orm/2.1/en/reference/faq.html#why-is-an-extra-sql-query-executed-every-time-i-fetch-an-entity-with-a-one-to-one-relation


	public function __construct()
	{
		$this->created_at = $this->updated_at = new \DateTime('now');
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function updated()
	{
		$this->updated_at = new \DateTime('now');
	}


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set last_name
	 *
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		$this->last_name = $lastName;
	}

	/**
	 * Get last_name
	 *
	 * @return string
	 */
	public function getLastName()
	{
		return $this->last_name;
	}

	/**
	 * Set first_name
	 *
	 * @param string $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->first_name = $firstName;
	}

	/**
	 * Get first_name
	 *
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->first_name;
	}

	/**
	 * Set gender
	 *
	 * @param string $gender
	 */
	public function setGender($gender)
	{
		$this->gender = $gender;
	}

	/**
	 * Get gender
	 *
	 * @return string
	 */
	public function getGender()
	{
		return $this->gender;
	}

	/**
	 * Set birthday
	 *
	 * @param date $birthday
	 */
	public function setBirthday($birthday)
	{
		$this->birthday = $birthday;
	}

	/**
	 * Get birthday
	 *
	 * @return date
	 */
	public function getBirthday()
	{
		return $this->birthday;
	}

	/**
	 * Set country
	 *
	 * @param string $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}

	/**
	 * Get country
	 *
	 * @return string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * Set is_active
	 *
	 * @param boolean $isActive
	 */
	public function setIsActive($isActive)
	{
		$this->is_active = $isActive;
	}

	/**
	 * Get is_active
	 *
	 * @return boolean
	 */
	public function getIsActive()
	{
		return $this->is_active;
	}

	/**
	 * Set zip
	 *
	 * @param string $zip
	 */
	public function setZip($zip)
	{
		$this->zip = $zip;
	}

	/**
	 * Get zip
	 *
	 * @return string
	 */
	public function getZip()
	{
		return $this->zip;
	}

	/**
	 * Set city
	 *
	 * @param string $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}

	/**
	 * Get city
	 *
	 * @return string
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * Set street
	 *
	 * @param string $street
	 */
	public function setStreet($street)
	{
		$this->street = $street;
	}

	/**
	 * Get street
	 *
	 * @return string
	 */
	public function getStreet()
	{
		return $this->street;
	}

	/**
	 * Set singles_rating
	 *
	 * @param smallint $singlesRating
	 */
	public function setSinglesRating($singlesRating)
	{
		$this->singles_rating = $singlesRating;
	}

	/**
	 * Get singles_rating
	 *
	 * @return smallint
	 */
	public function getSinglesRating()
	{
		return $this->singles_rating;
	}

	/**
	 * Set doubles_rating
	 *
	 * @param smallint $doublesRating
	 */
	public function setDoublesRating($doublesRating)
	{
		$this->doubles_rating = $doublesRating;
	}

	/**
	 * Get doubles_rating
	 *
	 * @return smallint
	 */
	public function getDoublesRating()
	{
		return $this->doubles_rating;
	}

	/**
	 * Set mixed_rating
	 *
	 * @param smallint $mixedRating
	 */
	public function setMixedRating($mixedRating)
	{
		$this->mixed_rating = $mixedRating;
	}

	/**
	 * Get mixed_rating
	 *
	 * @return smallint
	 */
	public function getMixedRating()
	{
		return $this->mixed_rating;
	}

	/**
	 * Set singles_matches
	 *
	 * @param smallint $singlesMatches
	 */
	public function setSinglesMatches($singlesMatches)
	{
		$this->singles_matches = $singlesMatches;
	}

	/**
	 * Get singles_matches
	 *
	 * @return smallint
	 */
	public function getSinglesMatches()
	{
		return $this->singles_matches;
	}

	/**
	 * Set doubles_matches
	 *
	 * @param smallint $doublesMatches
	 */
	public function setDoublesMatches($doublesMatches)
	{
		$this->doubles_matches = $doublesMatches;
	}

	/**
	 * Get doubles_matches
	 *
	 * @return smallint
	 */
	public function getDoublesMatches()
	{
		return $this->doubles_matches;
	}

	/**
	 * Set mixed_matches
	 *
	 * @param smallint $mixedMatches
	 */
	public function setMixedMatches($mixedMatches)
	{
		$this->mixed_matches = $mixedMatches;
	}

	/**
	 * Get mixed_matches
	 *
	 * @return smallint
	 */
	public function getMixedMatches()
	{
		return $this->mixed_matches;
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
	 * @return datetime
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
	 * @return datetime
	 */
	public function getUpdatedAt()
	{
		return $this->updated_at;
	}

	/**
	 * Set Club
	 *
	 * @param Club $club
	 */
	public function setClub(Club $club)
	{
		$this->Club = $club;
	}

	/**
	 * Get Club
	 *
	 * @return Club
	 */
	public function getClub()
	{
		return $this->Club;
	}


	public function routingParams(Tournament $tournament = null)
	{
		$params = array(
			'firstName' => $this->getFirstName(),
			'lastName' => $this->getLastName(),
			'id' => $this->getId()
		);

		if ($tournament) {
			return array_merge($params, $tournament->routingParams());
		} else {
			return $params;
		}

	}

	function getFullName()
	{
		return sprintf('%s %s', 
			$this->getFirstName(), $this->getLastName()
		);
	}

	function getReadableId()
	{
		return sprintf('%s [%s]', 
			$this->getFullName(),
			$this->getId()
		);
	}

	function __toString()
	{
		return $this->getFullName();
	}

}