<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Tobion\TropaionBundle\Entity\Athlete;
use Tobion\TropaionBundle\Entity\Team;
use Tobion\TropaionBundle\Entity\Tournament;

/**
 * Sportvereine und Spielgemeinschaften zusammengesetzt aus mehreren Vereinen
 *
 * @ORM\Table(name="clubs")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Club
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string $code
	 *
	 * @ORM\Column(name="code", type="string", length=10, unique=true)
	 */
	private $code;

	/**
	 * @var string $name
	 *
	 * @ORM\Column(name="name", type="string", length=100)
	 */
	private $name;

	/**
	 * @var integer $contact_person_id
	 *
	 * @ORM\Column(name="contact_person_id", type="integer", nullable=true)
	 */
	private $contact_person_id;

	/**
	 * @var integer $club_syndicate_1
	 *
	 * @ORM\Column(name="club_syndicate_1", type="integer", nullable=true)
	 */
	private $club_syndicate_1;

	/**
	 * @var integer $club_syndicate_2
	 *
	 * @ORM\Column(name="club_syndicate_2", type="integer", nullable=true)
	 */
	private $club_syndicate_2;

	/**
	 * @var integer $club_syndicate_3
	 *
	 * @ORM\Column(name="club_syndicate_3", type="integer", nullable=true)
	 */
	private $club_syndicate_3;

	/**
	 * @var integer $club_syndicate_4
	 *
	 * @ORM\Column(name="club_syndicate_4", type="integer", nullable=true)
	 */
	private $club_syndicate_4;

	/**
	 * @var string $website
	 *
	 * @ORM\Column(name="website", type="string", length=255)
	 */
	private $website;

	/**
	 * @var string $logo
	 *
	 * @ORM\Column(name="logo", type="string", length=100)
	 */
	private $logo;

	/**
	 * @var text $description
	 *
	 * @ORM\Column(name="description", type="text")
	 */
	private $description;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updated_at;

	/**
	 * @var Athlete
	 *
	 * @ORM\ManyToOne(targetEntity="Athlete")
	 * @ORM\JoinColumn(name="contact_person_id", referencedColumnName="id")
	 */
	private $Contact_Person;

	/**
	 * @var Club
	 *
	 * @ORM\ManyToOne(targetEntity="Club")
	 * @ORM\JoinColumn(name="club_syndicate_1", referencedColumnName="id")
	 */
	private $Syndicate1;

	/**
	 * @var Club
	 *
	 * @ORM\ManyToOne(targetEntity="Club")
	 * @ORM\JoinColumn(name="club_syndicate_2", referencedColumnName="id")
	 */
	private $Syndicate2;

	/**
	 * @var Club
	 *
	 * @ORM\ManyToOne(targetEntity="Club")
	 * @ORM\JoinColumn(name="club_syndicate_3", referencedColumnName="id")
	 */
	private $Syndicate3;

	/**
	 * @var Club
	 *
	 * @ORM\ManyToOne(targetEntity="Club")
	 * @ORM\JoinColumn(name="club_syndicate_4", referencedColumnName="id")
	 */
	private $Syndicate4;

	/**
	 * @var Team
	 *
	 * @ORM\OneToMany(targetEntity="Team", mappedBy="Club")
	 * @ORM\JoinColumn(name="id", referencedColumnName="club_id")
	 */
	private $Teams;

	public function __construct()
	{
		$this->Teams = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Set name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set contact_person_id
	 *
	 * @param integer $contactPersonId
	 */
	public function setContactPersonId($contactPersonId)
	{
		$this->contact_person_id = $contactPersonId;
	}

	/**
	 * Get contact_person_id
	 *
	 * @return integer
	 */
	public function getContactPersonId()
	{
		return $this->contact_person_id;
	}

	/**
	 * Set club_syndicate_1
	 *
	 * @param integer $clubSyndicate1
	 */
	public function setClubSyndicate1($clubSyndicate1)
	{
		$this->club_syndicate_1 = $clubSyndicate1;
	}

	/**
	 * Get club_syndicate_1
	 *
	 * @return integer
	 */
	public function getClubSyndicate1()
	{
		return $this->club_syndicate_1;
	}

	/**
	 * Set club_syndicate_2
	 *
	 * @param integer $clubSyndicate2
	 */
	public function setClubSyndicate2($clubSyndicate2)
	{
		$this->club_syndicate_2 = $clubSyndicate2;
	}

	/**
	 * Get club_syndicate_2
	 *
	 * @return integer
	 */
	public function getClubSyndicate2()
	{
		return $this->club_syndicate_2;
	}

	/**
	 * Set club_syndicate_3
	 *
	 * @param integer $clubSyndicate3
	 */
	public function setClubSyndicate3($clubSyndicate3)
	{
		$this->club_syndicate_3 = $clubSyndicate3;
	}

	/**
	 * Get club_syndicate_3
	 *
	 * @return integer
	 */
	public function getClubSyndicate3()
	{
		return $this->club_syndicate_3;
	}

	/**
	 * Set club_syndicate_4
	 *
	 * @param integer $clubSyndicate4
	 */
	public function setClubSyndicate4($clubSyndicate4)
	{
		$this->club_syndicate_4 = $clubSyndicate4;
	}

	/**
	 * Get club_syndicate_4
	 *
	 * @return integer
	 */
	public function getClubSyndicate4()
	{
		return $this->club_syndicate_4;
	}

	/**
	 * Set website
	 *
	 * @param string $website
	 */
	public function setWebsite($website)
	{
		$this->website = $website;
	}

	/**
	 * Get website
	 *
	 * @return string
	 */
	public function getWebsite()
	{
		return $this->website;
	}

	/**
	 * Set logo
	 *
	 * @param string $logo
	 */
	public function setLogo($logo)
	{
		$this->logo = $logo;
	}

	/**
	 * Get logo
	 *
	 * @return string
	 */
	public function getLogo()
	{
		return $this->logo;
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

	/**
	 * Set Contact_Person
	 *
	 * @param Athlete $contactPerson
	 */
	public function setContactPerson(Athlete $contactPerson)
	{
		$this->Contact_Person = $contactPerson;
	}

	/**
	 * Get Contact_Person
	 *
	 * @return Athlete
	 */
	public function getContactPerson()
	{
		return $this->Contact_Person;
	}

	/**
	 * Set Syndicate1
	 *
	 * @param Club $syndicate1
	 */
	public function setSyndicate1(Club $syndicate1)
	{
		$this->Syndicate1 = $syndicate1;
	}

	/**
	 * Get Syndicate1
	 *
	 * @return Club
	 */
	public function getSyndicate1()
	{
		return $this->Syndicate1;
	}

	/**
	 * Set Syndicate2
	 *
	 * @param Club $syndicate2
	 */
	public function setSyndicate2(Club $syndicate2)
	{
		$this->Syndicate2 = $syndicate2;
	}

	/**
	 * Get Syndicate2
	 *
	 * @return Club
	 */
	public function getSyndicate2()
	{
		return $this->Syndicate2;
	}

	/**
	 * Set Syndicate3
	 *
	 * @param Club $syndicate3
	 */
	public function setSyndicate3(Club $syndicate3)
	{
		$this->Syndicate3 = $syndicate3;
	}

	/**
	 * Get Syndicate3
	 *
	 * @return Club
	 */
	public function getSyndicate3()
	{
		return $this->Syndicate3;
	}

	/**
	 * Set Syndicate4
	 *
	 * @param Club $syndicate4
	 */
	public function setSyndicate4(Club $syndicate4)
	{
		$this->Syndicate4 = $syndicate4;
	}

	/**
	 * Get Syndicate4
	 *
	 * @return Club
	 */
	public function getSyndicate4()
	{
		return $this->Syndicate4;
	}

	/**
	 * Add Teams
	 *
	 * @param Team $team
	 */
	public function addTeams(Team $team)
	{
		$this->Teams[] = $team;
	}

	/**
	 * Get Teams
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getTeams()
	{
		return $this->Teams;
	}



	public function routingParams(Tournament $tournament = null)
	{
		$params = array(
			'club' => $this->getCode()
		);

		if ($tournament) {
			return array_merge($params, $tournament->routingParams());
		} else {
			return $params;
		}
	}

	function __toString()
	{
		return $this->getName();
	}

}