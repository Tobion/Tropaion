<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Tobion\TropaionBundle\Entity\Match;
use Tobion\TropaionBundle\Entity\Teammatch;
use Tobion\TropaionBundle\Entity\User;

/**
 * Kommentare zu Spielen
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity
 */
class Comment
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
	 * @var integer $author_id
	 *
	 * @ORM\Column(name="author_id", type="integer")
	 */
	private $author_id;

	/**
	 * @var integer $teammatch_id
	 *
	 * @ORM\Column(name="teammatch_id", type="integer")
	 */
	private $teammatch_id;

	/**
	 * @var integer $match_id
	 *
	 * @ORM\Column(name="match_id", type="integer", nullable=true)
	 */
	private $match_id;

	/**
	 * @var string $title
	 *
	 * @ORM\Column(name="title", type="string", length=100)
	 */
	private $title;

	/**
	 * @var text $message
	 *
	 * @ORM\Column(name="message", type="text")
	 */
	private $message;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="Comments")
	 * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
	 */
	private $Author;

	/**
	 * @var Teammatch
	 *
	 * @ORM\ManyToOne(targetEntity="Teammatch", inversedBy="Comments")
	 * @ORM\JoinColumn(name="teammatch_id", referencedColumnName="id")
	 */
	private $Teammatch;



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
	 * Set author_id
	 *
	 * @param integer $authorId
	 */
	public function setAuthorId($authorId)
	{
		$this->author_id = $authorId;
	}

	/**
	 * Get author_id
	 *
	 * @return integer
	 */
	public function getAuthorId()
	{
		return $this->author_id;
	}

	/**
	 * Set teammatch_id
	 *
	 * @param integer $teammatchId
	 */
	public function setTeammatchId($teammatchId)
	{
		$this->teammatch_id = $teammatchId;
	}

	/**
	 * Get teammatch_id
	 *
	 * @return integer
	 */
	public function getTeammatchId()
	{
		return $this->teammatch_id;
	}

	/**
	 * Set match_id
	 *
	 * @param integer $matchId
	 */
	public function setMatchId($matchId)
	{
		$this->match_id = $matchId;
	}

	/**
	 * Get match_id
	 *
	 * @return integer
	 */
	public function getMatchId()
	{
		return $this->match_id;
	}

	/**
	 * Set title
	 *
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set message
	 *
	 * @param text $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * Get message
	 *
	 * @return text
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Set Author
	 *
	 * @param User $author
	 */
	public function setAuthor(User $author)
	{
		$this->Author = $author;
	}

	/**
	 * Get Author
	 *
	 * @return User
	 */
	public function getAuthor()
	{
		return $this->Author;
	}

	/**
	 * Set Teammatch
	 *
	 * @param Teammatch $teammatch
	 */
	public function setTeammatch(Teammatch $teammatch)
	{
		$this->Teammatch = $teammatch;
	}

	/**
	 * Get Teammatch
	 *
	 * @return Teammatch
	 */
	public function getTeammatch()
	{
		return $this->Teammatch;
	}
}