<?php

namespace Tobion\TropaionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tobion\TropaionBundle\Entity\MatchType
 *
 * @ORM\Table(name="match_types")
 * @ORM\Entity
 */
class MatchType
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
     * @var string $name
     *
     * @ORM\Column(type="string", length=20)
     */
    private $name;
	
	/**
     * @var smallint $x_on_x
     * 0 = Varierend (keine Beschränkung; z.B. 1 vs 2); 1 = 1 gegen 1 (Einzel); 2 = 2 gegen 2 (Doppel)
     *
     * @ORM\Column(type="smallint")
     */
    private $x_on_x;
	
	/**
     * @var smallint $gender
     * 0 = Varierend (keine Beschränkung; z.B. Herren vs Damen); 1 = alles Herren; 2 = alles Frauen; 3 = Mixed Teams (Herr + Dame vs Herr + Dame)
     *
     * @ORM\Column(type="smallint")
     */
    private $gender;
	

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
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set x_on_x
     *
     * @param smallint $xOnX
     */
    public function setXOnX($xOnX)
    {
        $this->x_on_x = $xOnX;
    }

    /**
     * Get x_on_x
     *
     * @return smallint $xOnX
     */
    public function getXOnX()
    {
        return $this->x_on_x;
    }

    /**
     * Set gender
     *
     * @param smallint $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @return smallint $gender
     */
    public function getGender()
    {
        return $this->gender;
    }

	
	function __toString()
    {
        return $this->getName();
    }

}