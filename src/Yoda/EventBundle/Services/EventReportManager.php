<?php

namespace EventBundle\Services;

use Doctrine\ORM\EntityManager;


class EventReportManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getRecentlyCreated(){
        $eventRepo = $this->em->getRepository('EventBundle:Event');
        $updatedEvents = $eventRepo->getRecentUpdatedEvents();
        $rows = [];
        foreach ($updatedEvents as $event){
            $data = [
                $event->getId(),
                $event->getName(),
                $event->getTime()->format('Y-m-d H:i:s')
            ];
            $rows[] = implode(', ', $data);
        }

        return implode('\n', $rows);
    }
}