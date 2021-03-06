<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tournament
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
	private $sport = '';

	/**
	 * Veranstalter
	 * Organizer that owns this tournament
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
	 */
	private $Owner;

	/**
	 * Ausrichter
	 * @var Club
	 *
	 * @ORM\ManyToOne(targetEntity="Club")
	 * @ORM\JoinColumn(name="host_id", referencedColumnName="id", nullable=true)
	 */
	private $Host;


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
	 * @return string
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
	 * @return string
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
	 * @return string
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
	 * @return string
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
	 * @return date
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
	 * @return date
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
	 * @return string
	 */
	public function getSport()
	{
		return $this->sport;
	}

	/**
	 * Set Owner
	 *
	 * @param User $owner
	 */
	public function setOwner(User $owner)
	{
		$this->Owner = $owner;
	}

	/**
	 * Get Owner
	 *
	 * @return User
	 */
	public function getOwner()
	{
		return $this->Owner;
	}

	/**
	 * Set Host
	 *
	 * @param Club $host
	 */
	public function setHost(Club $host = null)
	{
		$this->Host = $host;
	}

	/**
	 * Get Host
	 *
	 * @return Club
	 */
	public function getHost()
	{
		return $this->Host;
	}



	public function routingParams()
	{
		return array(
			'owner' => $this->Owner->getSlug(),
			'tournament' => $this->getSlug()
		);
	}

	function __toString()
	{
		return $this->getShortName();
	}

}