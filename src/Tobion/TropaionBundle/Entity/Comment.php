<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

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
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
	 */
	private $Author;

	/**
	 * @var Teammatch
	 *
	 * @ORM\ManyToOne(targetEntity="Teammatch")
	 * @ORM\JoinColumn(name="teammatch_id", referencedColumnName="id", nullable=false)
	 */
	private $Teammatch;

	/**
	 * @var Match
	 *
	 * @ORM\ManyToOne(targetEntity="Match")
	 * @ORM\JoinColumn(name="match_id", referencedColumnName="id", nullable=true)
	 */
	private $Match;



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

	/**
	 * Set Match
	 *
	 * @param Match $match
	 */
	public function setMatch(Match $match = null)
	{
		$this->Match = $match;
	}

	/**
	 * Get Match
	 *
	 * @return Match
	 */
	public function getMatch()
	{
		return $this->Match;
	}
}