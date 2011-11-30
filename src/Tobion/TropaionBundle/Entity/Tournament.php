<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\Tournament
 *
 * Tournament -> Competition (Konkurrenz) or Event (Veranstaltung) -> Draw (Auslosung)
 *
 * @ORM\Table(name="tournaments",uniqueConstraints={@ORM\UniqueConstraint(name="slug_index", columns={"slug", "owner_id"})})
 * @ORM\Entity
 */
class Tournament
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
	 * @var integer $owner_id
	 * Veranstalter
	 *
	 * @ORM\Column(type="integer")
	 */
	private $owner_id; // organizer_id

	/**
	 * @var integer $host_id
	 * Ausrichter
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $host_id;

	/**
	 * @var string $short_name
	 *
	 * @ORM\Column(type="string", length=40)
	 */
	private $short_name;

	/**
	 * @var string $full_name
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $full_name;

	/**
	 * @var string $season
	 *
	 * @ORM\Column(type="string", length=10)
	 */
	private $season;

	/**
	 * @var string $slug
	 *
	 * @ORM\Column(type="string", length=40)
	 */
	private $slug;

	/**
	 * @var date $start_date
	 *
	 * @ORM\Column(type="date")
	 */
	private $start_date;

	/**
	 * @var date $end_date
	 *
	 * @ORM\Column(type="date")
	 */
	private $end_date;

	/**
	 * @var string $sport
	 *
	 * @ORM\Column(type="string", length=20)
	 */
	private $sport;

	/**
	 * @var Owner
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	private $Owner;

	/**
	 * @var Host
	 *
	 * @ORM\ManyToOne(targetEntity="Club")
	 * @ORM\JoinColumn(name="host_id", referencedColumnName="id")
	 */
	private $Host;


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
	 * Set owner_id
	 *
	 * @param integer $ownerId
	 */
	public function setOwnerId($ownerId)
	{
		$this->owner_id = $ownerId;
	}

	/**
	 * Get owner_id
	 *
	 * @return integer $ownerId
	 */
	public function getOwnerId()
	{
		return $this->owner_id;
	}

	/**
	 * Set host_id
	 *
	 * @param integer $hostId
	 */
	public function setHostId($hostId)
	{
		$this->host_id = $hostId;
	}

	/**
	 * Get host_id
	 *
	 * @return integer $hostId
	 */
	public function getHostId()
	{
		return $this->host_id;
	}

	/**
	 * Set short_name
	 *
	 * @param string $shortName
	 */
	public function setShortName($shortName)
	{
		$this->short_name = $shortName;
	}

	/**
	 * Get short_name
	 *
	 * @return string $shortName
	 */
	public function getShortName()
	{
		return $this->short_name;
	}

	/**
	 * Set full_name
	 *
	 * @param string $fullName
	 */
	public function setFullName($fullName)
	{
		$this->full_name = $fullName;
	}

	/**
	 * Get full_name
	 *
	 * @return string $fullName
	 */
	public function getFullName()
	{
		return $this->full_name;
	}

	/**
	 * Set season
	 *
	 * @param string $season
	 */
	public function setSeason($season)
	{
		$this->season = $season;
	}

	/**
	 * Get season
	 *
	 * @return string $season
	 */
	public function getSeason()
	{
		return $this->season;
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
	 * Set start_date
	 *
	 * @param date $startDate
	 */
	public function setStartDate($startDate)
	{
		$this->start_date = $startDate;
	}

	/**
	 * Get start_date
	 *
	 * @return date $startDate
	 */
	public function getStartDate()
	{
		return $this->start_date;
	}

	/**
	 * Set end_date
	 *
	 * @param date $endDate
	 */
	public function setEndDate($endDate)
	{
		$this->end_date = $endDate;
	}

	/**
	 * Get end_date
	 *
	 * @return date $endDate
	 */
	public function getEndDate()
	{
		return $this->end_date;
	}

	/**
	 * Set sport
	 *
	 * @param string $sport
	 */
	public function setSport($sport)
	{
		$this->sport = $sport;
	}

	/**
	 * Get sport
	 *
	 * @return string $sport
	 */
	public function getSport()
	{
		return $this->sport;
	}

	/**
	 * Set Owner
	 *
	 * @param Tobion\TropaionBundle\Entity\User $owner
	 */
	public function setOwner(\Tobion\TropaionBundle\Entity\User $owner)
	{
		$this->Owner = $owner;
	}

	/**
	 * Get Owner
	 *
	 * @return Tobion\TropaionBundle\Entity\User $owner
	 */
	public function getOwner()
	{
		return $this->Owner;
	}

	/**
	 * Set Host
	 *
	 * @param Tobion\TropaionBundle\Entity\Club $host
	 */
	public function setHost(\Tobion\TropaionBundle\Entity\Club $host)
	{
		$this->Host = $host;
	}

	/**
	 * Get Host
	 *
	 * @return Tobion\TropaionBundle\Entity\Club $host
	 */
	public function getHost()
	{
		return $this->Host;
	}



	public function routingParams()
	{
		return array(
			'owner' => $this->getOwner()->getSlug(),
			'tournament' => $this->getSlug()
		);
	}

	function __toString()
	{
		return $this->getShortName();
	}

}