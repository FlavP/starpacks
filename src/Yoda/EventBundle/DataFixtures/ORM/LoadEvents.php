<?php

namespace EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Yoda\EventBundle\Entity\Event;

class LoadUserData implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $yoloAdmin = $manager->getRepository('UserBundle:User')
            ->loadUserByUsername('yoloadmin');
        $event1 = new Event();
        $event1->setName('O zi frumoasa cu soare');
        $event1->setLocation('Afara');
        $event1->setTime(new \DateTime('tomorrow noon'));
        $event1->setDetails('surprise surprise');
        $manager->persist($event1);

        $event2 = new Event();
        $event2->setName('More on this later');
        $event2->setLocation('pe Bucale');
        $event2->setTime(new \DateTime('tomorrow noon'));
        $event2->setDetails('yaaay');
        $manager->persist($event2);

        $event1->setOwner($yoloAdmin);
        $event2->setOwner($yoloAdmin);
        $manager->flush();
    }

     public function getOrder()
    {
        return 20;
    }
}