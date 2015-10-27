<?php

namespace EPHEC\Bundle\UserBundle\Entity;
use EPHEC\Bundle\NoteBundle\Entity\Alarmgroup;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 *
 */
class User extends baseUser
{
    /**
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @ORM\Column(name="lastName", type="string", length=45, nullable=false)
     */
    private $lastname;

    /**
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @ORM\Column(name="firstName", type="string", length=45, nullable=false)
     */
    private $firstname;

    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToMany(targetEntity="EPHEC\Bundle\NoteBundle\Entity\Alarmgroup", mappedBy="user", cascade={"persist", "refresh"}, fetch="EAGER")
     */
    private $group;


    public function __construct(){
        parent::__construct();
        $this->group = new ArrayCollection();
    }

    /*public function prePersist(LifecycleEventArgs $event)
    {
        dump($event);
        $group = new Alarmgroup();
        $group->addUser($this);
        $group->setAlarmname($this->getFirstname().$this->getLastname());
        $group->setDescription(" ");
        $event->getObjectManager()->persist($group);
        $this->addAlarmGroup($group);
        dump($group);
    }*/

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Get group
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroup()
    {
        return $this->group;
    }

    public function addAlarmGroup(Alarmgroup $group)
    {
        $this->group[] = $group;

        return $this;
    }

    public function removeAlarmGroup(AlarmGroup $group)
    {
        $this->applications->removeElement($group);
    }
}
