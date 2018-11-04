<?php

namespace EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Yoda\EventBundle\Entity\Event;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class Controller extends BaseController
{
    /**
     * @return object|\Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext(){
        return $this->get('security.context');
    }

    public function enforceOwnerSecurity(Event $event){
        $user = $this->getUser();
        if($user != $event->getOwner()){
            throw new AccessDeniedException('You are not the owner of this event');
        }
    }
}