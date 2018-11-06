<?php

namespace Yoda\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text')
                ->add('email', 'email', [
                    'label' => 'Email Address',
                    'attr'  => [
                        'class' => 'html-class'
                    ]
                ])
                ->add('plainPassword', 'repeated', [
                    'type' => 'password'
                ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yoda\UserBundle\Entity\User',
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view['email']->vars['help_variable'] = 'Hint it should have an @ symbol';
    }

    public function getName()
    {
        return 'user_register';
    }
}