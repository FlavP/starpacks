<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;



$loader =require_once __DIR__.'/app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/app/AppKernel.php';
$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$kernel->boot();

$container = $kernel->getContainer();
$container->enterScope('request');
$container->set('request', $request);

//$templating = $container->get('templating');

use Yoda\EventBundle\Entity\Event;
use Yoda\UserBundle\Entity\User;

//$event = new Event();
//$event->setName('Happy Birthday');
//$event->setLocation('Acasa');
//$event->setTime(new DateTime('tomorrow'));
//$event->setDetails('Yaaaaay');

$em = $container->get('doctrine')->getManager();

$yolo = $em->getRepository('UserBundle:User')->loadUserByUsername('yolo');

$yolo->setPlainPassword('swagger');

$em->persist($yolo);

$em->flush();
