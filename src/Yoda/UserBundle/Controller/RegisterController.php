<?php

namespace Yoda\UserBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class RegisterController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @Template()
     */
    public function registerAction(){
        $form = $this->createFormBuilder()
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('password', 'password')
            ->getForm();
        return ['form' => $form->createView()];
    }
}