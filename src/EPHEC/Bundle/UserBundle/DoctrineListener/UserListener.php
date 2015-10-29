<?php

namespace EPHEC\Bundle\UserBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EPHEC\Bundle\UserBundle\Entity\User;
use EPHEC\Bundle\NoteBundle\Entity\Alarmgroup;

class UserListener
{
    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        // On veut envoyer un email que pour les entités User
        if (!$entity instanceof User) {
            return;
        }
        $group = new Alarmgroup();
        $group->addUser($entity);
        $group->setAlarmname($entity->getFirstname() . " " . $entity->getLastname());
        $group->setDescription(" ");
        $entity->addAlarmGroup($group);
        $event->getObjectManager()->persist($group);
    }
}