<?php

namespace EventBundle\Controller;


use EventBundle\Services\EventReportManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * @Route("/events/report/recentlyUpdated.csv")
     */
    public function updatedCSVEventsAction(){
//        $em = $this->getDoctrine()->getManager();
//        $em = $this->container->get('doctrine.orm.entity_manager');
        $eventReportManager = $this->container->get('event_report_manager');
        $content = $eventReportManager->getRecentlyCreated();
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        return $response;
    }
}