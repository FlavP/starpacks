<?php

namespace Yoda\EventBundle\Controller;

use EventBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Yoda\EventBundle\Entity\Event;
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
//        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
//        $userRepository = $em->getRepository('UserBundle:User');
//        dump($userRepository->findOneByUsernameOrEmail('user2@deathstar.com'));
//        die();
        $events = $em->getRepository('EventBundle:Event')->getUpcominEvents();


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
            $event->setOwner($this->getUser());
            $em->persist($event);
            $em->flush();

            return $this->redirect($this->generateUrl('show', array('slug' => $event->getSlug())));
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
     * @Route("/{slug}/show", name="show")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('EventBundle:Event')->findOneBy([
            'slug' => $slug
        ]);
        if(!$event){
            throw $this->createNotFoundException("Unable to find this event");
        }
        $deleteForm = $this->createDeleteForm($event);

        return array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            );
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/{id}/edit", name="edit")
     */
    public function editAction(Request $request, $id){
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository("EventBundle:Event")
            ->find($id);
        if(!$event){
            throw $this->createNotFoundException('Unable to find Event event.');
        }
        $this->enforceOwnerSecurity($event);
        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createEditForm($event);

        return $this->render('EventBundle:Event:edit.html.twig', [
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
            ]);
    }

    /**
     * @Route("/{id}/update", name="update")
     */
    public function updateAction(Request $request, $id)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Unable to find Event event.');
        }
        $this->enforceOwnerSecurity($event);
        $deleteForm = $this->createDeleteForm($event);
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
     * @Route("/{id}/delete", name="delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('EventBundle:Event')->find($id);
        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);
        if (!$event) {
            throw $this->createNotFoundException('Unable to find Event event.');
        }
        $this->enforceOwnerSecurity($event);

        if ($form->isValid()) {
            $em->remove($event);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('event'));
    }

    /**
     * @param $id
     * @Route("/{id}/attend", name="attend")
     */
    public function attendAction($id){
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('EventBundle:Event')->find($id);
        if(!$event){
            throw $this->createNotFoundException('Unable to find Event entity.');
        }
        if(!$event->hasAttendee($this->getUser())){
            $event->getAttendees()->add($this->getUser());
        }
        $em->persist($event);
        $em->flush();
        $url = $this->generateUrl('show', [
            'slug' => $event->getSlug(),
        ]);
        return $this->redirect($url);
    }

    /**
     * @param $id
     * @Route("/{id}/unattend", name="unattend")
     */
    public function unattendAction($id){
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('EventBundle:Event')->find($id);
        if(!$event){
            throw $this->createNotFoundException('Unable to find Event entity.');
        }
        if($event->hasAttendee($this->getUser())){
            $event->getAttendees()->removeElement($this->getUser());
        }
        $em->persist($event);
        $em->flush();
        $url = $this->generateUrl('show', [
            'slug' => $event->getSlug(),
        ]);
        return $this->redirect($url);
    }

    public function enforceUserSecurity($role = 'ROLE_USER'){
        if(!$this->getSecurityContext()->isGranted($role)){
            throw $this->createAccessDeniedException('Need ' . $role);
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
            'action' => $this->generateUrl('update', array('id' => $event->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to delete a Event event by id.
     *
     * @param Event $event The event
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }
}
