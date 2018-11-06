<?php

namespace EventBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;


class EventReportManager
{
    private $em;
    private $router;

    public function __construct(EntityManager $em, Router $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function getRecentlyCreated(){
        $eventRepo = $this->em->getRepository('EventBundle:Event');
        $updatedEvents = $eventRepo->getRecentUpdatedEvents();
        $rows = [];
        foreach ($updatedEvents as $event){
            $data = [
                $event->getId(),
                $event->getName(),
                $event->getTime()->format('Y-m-d H:i:s'),
                $this->router->generate(
                'show', [
                'slug' => $event->getSlug(),
                ],
                true)
            ];
            $rows[] = implode(', ', $data);
        }

        return implode('\n', $rows);
    }
}