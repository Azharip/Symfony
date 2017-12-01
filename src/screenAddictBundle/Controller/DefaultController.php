<?php

namespace screenAddictBundle\Controller;

use screenAddictBundle\Entity\User;
use screenAddictBundle\Entity\Message;
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
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            $user->setSalt('');
            $user->setRoles(array('ROLE_USER'));
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

    public function loginAction(Request $request)
    {
		$defaultData = array('message' => 'Type your message here');
		$form = $this->createFormBuilder($defaultData)
			->add('message', TextType::class)
            ->add('Valider',    SubmitType::class)
			->getForm();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
        	$data = $form->getData();

			//set id_author
			$message = new Message();
			$user = $this->getUser();
			$message->setIdAuthor($user->getId());

			//set content
			$message->setContent($data['message']);

			$em = $this->getDoctrine()->getManager();
            $em->persist($message);

			$message->setUser($user);
            $em->flush();

            return $this->redirectToRoute('login');
	    }

        return $this->render('screenAddictBundle:Default:pageprincipale.html.twig',['mesform'=>$form->createView()]);
    }

}
