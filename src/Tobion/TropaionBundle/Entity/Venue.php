<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Venue
 *
 * @ORM\Table(name="venues")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Venue
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
	 * @var string $code
	 *
	 * @ORM\Column(type="string", length=10, unique=true)
	 */
	private $code;

	/**
	 * @var string $zip
	 *
	 * @ORM\Column(type="string", length=10)
	 */
	private $zip;

	/**
	 * @var string $city
	 *
	 * @ORM\Column(type="string", length=70)
	 */
	private $city;

	/**
	 * @var string $street
	 *
	 * @ORM\Column(type="string", length=70)
	 */
	private $street;

	/**
	 * @var string $latitude
	 *
	 * @ORM\Column(type="string", length=10)
	 */
	private $latitude;

	/**
	 * @var string $longitude
	 *
	 * @ORM\Column(type="string", length=10)
	 */
	private $longitude;

	/**
	 * @var text $description
	 *
	 * @ORM\Column(type="text")
	 */
	private $description;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updated_at;


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
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	/**
	 * Get code
	 *
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
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
	 * Set latitude
	 *
	 * @param string $latitude
	 */
	public function setLatitude($latitude)
	{
		$this->latitude = $latitude;
	}

	/**
	 * Get latitude
	 *
	 * @return string
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	 * Set longitude
	 *
	 * @param string $longitude
	 */
	public function setLongitude($longitude)
	{
		$this->longitude = $longitude;
	}

	/**
	 * Get longitude
	 *
	 * @return string
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}

	/**
	 * Set description
	 *
	 * @param text $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Get description
	 *
	 * @return text
	 */
	public function getDescription()
	{
		return $this->description;
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


	public function routingParams()
	{
		return array(
			'venue' => $this->getCode()
		);
	}

	/**
	 * Returns safe CURIE for referencing resources in RDFa
	 * @param  string $prefix    The namespace prefix
	 * @return string CURIE
	 */
	public function getCurie($prefix = 'resource')
	{
		return "[$prefix:venue/{$this->getId()}]";
	}

	function __toString()
	{
		return $this->getCode();
	} 

}