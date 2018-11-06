<?php

namespace Yoda\EventBundle\Controller;

use EventBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name, $count)
    {
//        $em = $this->container->get('doctrine')->getManager();
//        $this->container->getParameter('our_assets_version');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EventBundle:Event');
        $event = $repo->findOneBy([
            'name' => 'Happy Birthday'
        ]);
        return $this->render(
            'EventBundle:Default:index.html.twig',
            ['name' => $name, 'count' =>$count, 'event' => $event]
        );
    }
}
