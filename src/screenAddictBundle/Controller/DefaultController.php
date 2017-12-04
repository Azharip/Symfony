<?php

namespace screenAddictBundle\Controller;

use screenAddictBundle\Entity\User;
use screenAddictBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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
            ->add('bdate',      BirthdayType::class,array('label'=>'Date de naissance','format' => 'ddMMMyyyy'))
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
            return $this->redirectToRoute('screen_addict_homepage');
        }

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('home');
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

		$user = $this->getUser();

		//Formulaire de recherche
		$researchData = array('recherche' => 'Recherchez un film');
		$researchForm = $this->createFormBuilder($researchData,['attr' => ['id' => 'res']])
			->setAction($this->generateUrl('rechercher'))
            ->setMethod('POST')
			->add('recherche', TextType::class, array('label' => false))
			->getForm();

		//Formulaire de messages
		$messageData = array('message' => 'Entrez votre message');
		$messageForm = $this->createFormBuilder($messageData)
			->add('message', TextType::class, array('label' => false))
			->getForm();

		$allMessages = $user->getMessages()->toArray();

		for($i = 0; $i < count($user->getFriends()); $i++)
		{
			$friend = $this->getDoctrine()->getManager()->getRepository('screenAddictBundle:User')->find($user->getFriends()[$i]->getId())->getMessages()->toArray();
			$allMessages = array_merge($allMessages,$friend);
		}

		usort($allMessages, function($a, $b) {
		  	$ad = new \DateTime($a->getDatePost()->format('Y-m-d H:i:s'));
		  	$bd = new \DateTime($b->getDatePost()->format('Y-m-d H:i:s'));

		  	if ($ad == $bd) {
		    	return 0;
		  	}

		  	return $ad < $bd ? 1 : -1;
		});


		if('POST' === $request->getMethod())
		{
			if ($request->request->has('mesform'))
			{
				$messageForm->handleRequest($request);
				if ($messageForm->isSubmitted() && $messageForm->isValid())
				{
					$data = $messageForm->getData();

					$message = new Message();

					$message->setIdAuthor($user->getId());

					//set username_author
					$message->setUsernameAuthor($user->getUsername());

					//set content
					$message->setContent($data['message']);

					$em = $this->getDoctrine()->getManager();
		            $em->persist($message);

					$message->setUser($user);
		            $em->flush();

		            return $this->redirectToRoute('home');
			    }
			}
		}

		return $this->render('screenAddictBundle:Default:pageprincipale.html.twig',
			['mesform'=>$messageForm->createView(),
			'messageList'=> $allMessages,
			'rech'=>$researchForm->createView()
			]);
    }

    public function rechercherAction(Request $request)
    {
        if($request->request->get('recherche')){
            //make something curious, get some unbelieveable data
			$recherche = $request->request->get('recherche');
            $url = "https://api.themoviedb.org/3/search/movie?api_key=5cac0300f480fa473ca2b57296132a8f&language=fr-FR&query=".$recherche."&page=1&include_adult=false";
            $json_source = file_get_contents($url);
            $arrData = ['output' => $json_source];
            return new JsonResponse($arrData);
        }
        return $this->render('screenAddictBundle:Default:pageprincipale.html.twig');
    }

}
