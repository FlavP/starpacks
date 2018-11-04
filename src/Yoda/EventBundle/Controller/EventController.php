<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Yoda\EventBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yoda\EventBundle\Form\EventType;

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
     * @Route("/new", name="create")
     *
     */
    public function createAction(Request $request)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $event = new Event();
        $form = $this->createCreateForm($event);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirect($this->generateUrl('event', array('id' => $event->getId())));
        }

        return $this->render('EventBundle:Event:new.html.twig', array(
            'event' => $event,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/new", name="new")
     * @Template()
     */
    public function newAction()
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $event = new Event();
        $form   = $this->createCreateForm($event);

        return $this->render('EventBundle:Event:new.html.twig', array(
            'event' => $event,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/show", name="show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('EventBundle:Event')->find($id);
        if(!$event){
            throw $this->createNotFoundException("Unable to find this event");
        }
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            );
    }

    /**
     * @Route("/{id}/edit", name="edit")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Unable to find Event event.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($event);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('edit', array('id' => $id)));
        }

        return $this->render('EventBundle:Event:edit.html.twig', array(
            'event'      => $event,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Event event.
     *
     * @param Event $event The event
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Event $event)
    {
        $form = $this->createForm(new EventType(), $event, array(
            'action' => $this->generateUrl('edit', array('id' => $event->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $event = $em->getRepository('EventBundle:Event')->find($id);

            if (!$event) {
                throw $this->createNotFoundException('Unable to find Event event.');
            }

            $em->remove($event);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('event'));
    }

    /**
     * Creates a form to delete a Event event by id.
     *
     * @param mixed $id The event id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }


    public function enforceUserSecurity($user){
        $securityContext = $this->get('security.context');
        if(!$securityContext->isGranted($user)){
            throw $this->createAccessDeniedException('Need ' . $user);
        }
    }

    private function enforceOwnerSecurity(Event $event){
        $user = $this->getUser();
        if($user != $event->getOwner()){
            throw new AccessDeniedException('You are not the owner of this event');
        }
    }

    /**
     * Creates a form to create a Event event.
     *
     * @param Event $event The event
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Event $event)
    {
        $form = $this->createForm(new EventType(), $event, array(
            'action' => $this->generateUrl('create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
}
