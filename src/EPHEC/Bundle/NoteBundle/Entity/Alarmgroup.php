<?php

namespace EPHEC\Bundle\NoteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alarmgroup
 *
 * @ORM\Table(name="alarmGroup", uniqueConstraints={@ORM\UniqueConstraint(name="id_UNIQUE", columns={"id"})})
 * @ORM\Entity
 */
class Alarmgroup
{
    /**
     *
     * @ORM\Column(name="alarmName", type="string", length=45, nullable=false)
     */
    private $alarmname;

    /**
     *
     * @ORM\Column(name="description", type="text", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="EPHEC\Bundle\UserBundle\Entity\User", inversedBy="group")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set alarmname
     *
     * @param string $alarmname
     * @return Alarmgroup
     */
    public function setAlarmname($alarmname)
    {
        $this->alarmname = $alarmname;
    
        return $this;
    }

    /**
     * Get alarmname
     *
     * @return string 
     */
    public function getAlarmname()
    {
        return $this->alarmname;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Alarmgroup
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
     * Add user
     *
     * @param \EPHEC\Bundle\UserBundle\Entity\User $user
     * @return Alarmgroup
     */
    public function addUser(\EPHEC\Bundle\UserBundle\Entity\User $user)
    {
        $this->user[] = $user;
    
        return $this;
    }

    /**
     * Remove user
     *
     * @param \EPHEC\Bundle\UserBundle\Entity\User $user
     */
    public function removeUser(\EPHEC\Bundle\UserBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }
}
