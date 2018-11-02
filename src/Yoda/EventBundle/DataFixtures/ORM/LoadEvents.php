<?php

namespace EventBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Yoda\EventBundle\Entity\Event;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
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
        $manager->flush();
    }
}