<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Yoda\EventBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    /**
     * @Route("/", name="event")
     * @Template()
     * Lists all event entities.
     *
     */
    public function indexAction()
    {
//        $user = $this->container->get('security.context')->getToken()->getUser();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
//        $userRepository = $em->getRepository('UserBundle:User');
//        dump($userRepository->findOneByUsernameOrEmail('user2@deathstar.com'));
//        die();
        $events = $em->getRepository('EventBundle:Event')->findAll();

        return [
            'events' => $events
        ];
    }

    /**
     * @Route("/new", name="new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        $event = new Event();
        $form = $this->createForm('Yoda\EventBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('show', array('id' => $event->getId()));
        }

        return array(
            'event' => $event,
            'form' => $form->createView(),
            );
    }

    /**
     * @Route("/{id}/show", name="show")
     * @Template()
     */
    public function showAction(Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);

        return array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            );
    }

    /**
     * @Route("/{id}/edit", name="edit")
     * @Template()
     */
    public function editAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('Yoda\EventBundle\Form\EventType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('edit', array('id' => $event->getId()));
        }

        return array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            );
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function deleteAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('event');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Event $event The event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function enforceUserSecurity($user){
        $securityContext = $this->get('security.context');
        if(!$securityContext->isGranted($user)){
            throw $this->createAccessDeniedException('Need ' . $user);
        }
    }
}
