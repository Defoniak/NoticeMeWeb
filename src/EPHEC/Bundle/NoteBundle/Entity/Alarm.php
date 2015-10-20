<?php

namespace EPHEC\Bundle\NoteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alarm
 *
 * @ORM\Table(name="alarm", uniqueConstraints={@ORM\UniqueConstraint(name="id_UNIQUE", columns={"id"})})
 * @ORM\Entity
 */
class Alarm
{
    /**
     *
     * @ORM\Column(name="dateAlarm", type="datetime", nullable=false)
     */
    private $datealarm;

    /**
     *
     * @ORM\Column(name="memo", type="text", length=255, nullable=false)
     */
    private $memo;

    /**
     *
     * @ORM\Column(name="title", type="string", length=45, nullable=false)
     */
    private $title;

    /**
     *
     * @ORM\ManyToOne(targetEntity="EPHEC\Bundle\NoteBundle\Entity\Alarmgroup")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group;

    /**
     *
     * @ORM\Column(name="dateValid", type="datetime", nullable=false)
     */
    private $datevalid;

    /**
     *
     * @ORM\Column(name="latitude", type="string", length=45, nullable=false)
     */
    private $latitude;

    /**
     *
     * @ORM\Column(name="longitude", type="string", length=45, nullable=false)
     */
    private $longitude;

    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set datealarm
     *
     * @param \DateTime $datealarm
     * @return Alarm
     */
    public function setDatealarm($datealarm)
    {
        $this->datealarm = $datealarm;
    
        return $this;
    }

    /**
     * Get datealarm
     *
     * @return \DateTime 
     */
    public function getDatealarm()
    {
        return $this->datealarm;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return Alarm
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;
    
        return $this;
    }

    /**
     * Get memo
     *
     * @return string 
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Alarm
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
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
     * Set datevalid
     *
     * @param \DateTime $datevalid
     * @return Alarm
     */
    public function setDatevalid($datevalid)
    {
        $this->datevalid = $datevalid;
    
        return $this;
    }

    /**
     * Get datevalid
     *
     * @return \DateTime 
     */
    public function getDatevalid()
    {
        return $this->datevalid;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return Alarm
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
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
     * @return Alarm
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set group
     *
     * @param \EPHEC\Bundle\NoteBundle\Entity\Alarmgroup $group
     * @return Alarm
     */
    public function setGroup(\EPHEC\Bundle\NoteBundle\Entity\Alarmgroup $group)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get group
     *
     * @return \EPHEC\Bundle\NoteBundle\Entity\Alarmgroup
     */
    public function getGroup()
    {
        return $this->group;
    }
}