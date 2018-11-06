<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Yoda\UserBundle\Entity\User;


class LoadUsers implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername('yolo');
//        $user1->setPassword($this->encondePassword($user1, 'swag'));
        $user1->setPlainPassword('swag');
        $user1->setEmail('user1@deathstar.com');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('yoloadmin');
//        $user2->setPassword($this->encondePassword($user2, 'adminadmin'));
        $user2->setPlainPassword('adminadmin');
        $user2->setRoles(array('ROLE_ADMIN'));
        $user2->setEmail('user2@deathstar.com');
//        $user2->setIsActive(false);
        $manager->persist($user2);

        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getOrder()
    {
        return 10;
    }


}