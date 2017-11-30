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
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        //$userRepository = $em->getRepository('screenAddictBundle:User');

        $user = new User();

        $regForm = $this->createFormBuilder($user)
            ->add('username',   TextType::class,array('label'=>'Pseudo'))
            ->add('password',   PasswordType::class,array('label'=>'Mot de passe'))
            ->add('name',       TextType::class,array('label'=>'Nom'))
            ->add('fname',      TextType::class,array('label'=>'Prénom'))
            ->add('mail',       EmailType::class,array('label'=>'Email'))
            ->add('bdate',      BirthdayType::class,array('label'=>'Date de naissance'))
            ->add('Valider',    SubmitType::class)
            ->getForm()
        ;

        $regForm->handleRequest($request);
        if ($regForm->isSubmitted() && $regForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('login');
        }

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('login');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('screenAddictBundle:Default:index.html.twig',
            ['regForm' => $regForm->createView(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError()]
        );
    }

    public function loginAction()
    {
        return $this->render('screenAddictBundle:Default:pageprincipale.html.twig');
    }

}
