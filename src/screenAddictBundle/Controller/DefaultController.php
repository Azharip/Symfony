<?php

namespace screenAddictBundle\Controller;

use screenAddictBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class DefaultController extends Controller
{

    public function indexAction()
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username',   TextType::class)
            ->add('password',   PasswordType::class)
            ->add('name',       TextType::class)
            ->add('fname',      TextType::class)
            ->add('mail',       EmailType::class)
            ->add('bdate',      BirthdayType::class)
            ->add('save',       SubmitType::class)
            ->getForm()
        ;

        return $this->render('screenAddictBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
