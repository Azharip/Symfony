<?php

namespace screenAddictBundle\Controller;

use screenAddictBundle\Entity\User;
use screenAddictBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $user = new User();

        $regForm = $this->createFormBuilder($user)
            ->add('username',   TextType::class,array('label'=>'Pseudo'))
            ->add('password',   PasswordType::class,array('label'=>'Mot de passe'))
            ->add('name',       TextType::class,array('label'=>'Nom'))
            ->add('fname',      TextType::class,array('label'=>'Prénom'))
            ->add('mail',       EmailType::class,array('label'=>'Email'))
            ->add('bdate',      BirthdayType::class,array('widget' => 'single_text'))
            ->getForm()
        ;

        $regForm->handleRequest($request);
        if ($regForm->isSubmitted() && $regForm->isValid())
        {
            $passwordEncoder = $this->get('security.password_encoder');
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setSalt($this->generateRandomString());
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
		$researchData = array();
		$researchForm = $this->createFormBuilder($researchData,['attr' => ['id' => 'res']])
			->setAction($this->generateUrl('rechercher'))
            ->setMethod('POST')
			->add('recherche', TextType::class, array('label' => false,'attr' => ['autocomplete' => 'off']))
			->getForm();

		//Formulaire de messages
		$messageData = array();
		$messageForm = $this->createFormBuilder($messageData,['attr' => ['id' => 'mes']])
			->add('message', TextareaType::class, array('label' => false,'attr' => ['autocomplete' => 'off']))
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


		if($request->isMethod('POST'))
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
			$recherche = str_replace(" ","%20",$recherche);
            $url = "https://api.themoviedb.org/3/search/movie?api_key=5cac0300f480fa473ca2b57296132a8f&language=fr-FR&query=".$recherche."&page=1&include_adult=false";
            $json_source = file_get_contents($url);
            $arrData = ['output' => $json_source];
            return new JsonResponse($arrData);
        }
        return $this->render('screenAddictBundle:Default:pageprincipale.html.twig');
    }

    public function rechercherAmiAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('screenAddictBundle:User');
        if($request->request->get('recherche')){
            //make something curious, get some unbelieveable data
			$recherche = $request->request->get('recherche');
            $user = $repository->findOneByUsername($recherche);
			$encoders = array(new XmlEncoder(), new JsonEncoder());
			$normalizer = new ObjectNormalizer();
			$normalizer->setCircularReferenceLimit(2);
			$normalizer->setCircularReferenceHandler(function ($object) {
			    return $object->getId();
			});
			$normalizers = array($normalizer);
			$serializer = new Serializer($normalizers, $encoders);

			$jsonContent = $serializer->serialize($user, 'json');
            return new JsonResponse(['output' => $jsonContent]);
        }
		return $this->render('screenAddictBundle:Default:pageprincipale.html.twig');
    }

	public function filmAction($id_film)
	{
		$url = "https://api.themoviedb.org/3/movie/".$id_film."?api_key=5cac0300f480fa473ca2b57296132a8f&language=fr-FR";
		$film_details = file_get_contents($url);

		$url = "https://api.themoviedb.org/3/movie/".$id_film."/credits?api_key=5cac0300f480fa473ca2b57296132a8f";
		$film_crew = file_get_contents($url);

		return $this->render('screenAddictBundle:Default:film.html.twig',
		['id_film'=>$id_film,
		'film_details'=>$film_details,
		'film_crew'=>$film_crew
		]);
	}

	public function ajoutFilmAction($id_film)
	{
		$user = $this->getUser();

		$user_movies = $user->getMovies();
		$isin = false;
		for($i = 0; $i < count($user_movies); $i++)
			if($user_movies[$i] == $id_film)
				$isin = true;

		if(!$isin)
		{
            $em = $this->getDoctrine()->getManager();
			if(!is_array($user_movies))
				$user_movies = $user_movies->toArray();
			$movies = array_merge($user_movies,array($id_film));
			$user->setMovies($movies);
			$em->flush();
		}

		$url = "https://api.themoviedb.org/3/movie/".$id_film."?api_key=5cac0300f480fa473ca2b57296132a8f&language=fr-FR";
		$film_details = file_get_contents($url);

		$url = "https://api.themoviedb.org/3/movie/".$id_film."/credits?api_key=5cac0300f480fa473ca2b57296132a8f";
		$film_crew = file_get_contents($url);

		return $this->render('screenAddictBundle:Default:film.html.twig',
		['id_film'=>$id_film,
		'film_details'=>$film_details,
		'film_crew'=>$film_crew
		]);
	}

    public function amiAction($id_ami)
	{
        $repository = $this->getDoctrine()->getRepository('screenAddictBundle:User');

        //make something curious, get some unbelieveable data
		$user = $this->getUser();

		if($id_ami != $user->getId() && $id_ami != -1)
        	$user = $repository->find($id_ami);

		if($id_ami == -1)
			$id_ami = $user->getId();

		$messages = $user->getMessages()->toArray();

		usort($messages, function($a, $b) {
		  	$ad = new \DateTime($a->getDatePost()->format('Y-m-d H:i:s'));
		  	$bd = new \DateTime($b->getDatePost()->format('Y-m-d H:i:s'));

		  	if ($ad == $bd) {
		    	return 0;
		  	}

		  	return $ad < $bd ? 1 : -1;
		});

        return $this->render('screenAddictBundle:Default:user.html.twig',
		['id_ami'=>$id_ami,
		'id_user'=>$this->getUser()->getId(),
        'username'=>$user->getUsername(),
        'fname'=>$user->getFname(),
        'name'=>$user->getName(),
        'bdate'=>$user->getBdate(),
		'messages'=>$messages
		]);
	}

    public function ajoutAmiAction($id_ami)
	{
		$user = $this->getUser();

		$user_friends = $user->getFriends();
		$isin = false;
		for($i = 0; $i < count($user_friends); $i++)
			if($user_friends[$i]->getId() == $id_ami)
				$isin = true;

        $em = $this->getDoctrine()->getManager();
        if(!is_array($user_friends))
            $user_friends = $user_friends->toArray();
        $me = $em->getRepository('screenAddictBundle:User')->find($id_ami);

        $me_friends = $me->getFriends();
        if(!is_array($me_friends))
            $me_friends = $me_friends->toArray();


		if(!$isin)
		{
            $friends = array_merge($user_friends,array($me));
            $user->setFriends($friends);

            $me_friends = array_merge($me_friends,array($user));
            $me->setFriends($me_friends);

            $em->flush();
		}

        $messages = $me->getMessages()->toArray();

		usort($messages, function($a, $b) {
		  	$ad = new \DateTime($a->getDatePost()->format('Y-m-d H:i:s'));
		  	$bd = new \DateTime($b->getDatePost()->format('Y-m-d H:i:s'));

		  	if ($ad == $bd) {
		    	return 0;
		  	}

		  	return $ad < $bd ? 1 : -1;
		});

        return $this->render('screenAddictBundle:Default:user.html.twig',
		['id_ami'=>$id_ami,
		'id_user'=>$this->getUser()->getId(),
        'username'=>$me->getUsername(),
        'fname'=>$me->getFname(),
        'name'=>$me->getName(),
        'bdate'=>$me->getBdate(),
		'messages'=>$messages
		]);
	}

    public function accountAction(Request $request){
		$changemailForm = $this->get('form.factory')->createNamedBuilder('mailForm')
			->add('mail', EmailType::class,array('label'=>'Email'))
			->add('newmail', EmailType::class,array('label'=>'Email'))
			->getForm();

		$changepwdForm = $this->get('form.factory')->createNamedBuilder('pwdForm')
			->add('password', PasswordType::class,array('label'=>'Password'))
			->add('newpassword', PasswordType::class,array('label'=>'New password'))
			->add('newpasswordconfirm', PasswordType::class,array('label'=>'Confirm new password'))
			->getForm();

		$errormail = '';		//message d'erreur quand le mail entré ne correspond pas au mail en BD
		$errorpwd1 = '';		//message d'erreur quand le mot de passe entré ne correspond pas au mot de passe en BD
		$errorpwd2 = '';		//message d'erreur quand les deux nouveaux mots de passe ne sont pas identiques

		if($request->isMethod('POST'))
		{
			if ($request->request->has('mailForm'))
			{
				$changemailForm->handleRequest($request);
		        if ($changemailForm->isSubmitted() && $changemailForm->isValid())
		        {
					$mail = $changemailForm->getData();
					$newmail = $mail['newmail'];
					$user = $this->getUser();
					if($mail['mail'] == $user->getMail())
					{
						$em = $this->getDoctrine()->getManager();
						$user->setMail($newmail);
		    			$em->flush();
			            return $this->redirectToRoute('account');
					}
					else
					{
						$errormail = "Cet email ne correspond pas à votre email";
					}
		        }
			}

			if ($request->request->has('pwdForm'))
			{
				$changepwdForm->handleRequest($request);
		        if ($changepwdForm->isSubmitted() && $changepwdForm->isValid())
		        {
					$pwd = $changepwdForm->getData();
					$someuser = new User();
					$user = $this->getUser();
					$passwordEncoder = $this->get('security.password_encoder');
					$validPassword = $passwordEncoder->isPasswordValid(
					    $user, 					// the encoded password
					    $pwd['password'],       // the submitted password
					    $user->getSalt()
					);

					if($validPassword)
					{
						if($pwd['newpassword'] == $pwd['newpasswordconfirm'])
						{
							$em = $this->getDoctrine()->getManager();
							$newcryptedpwd = $passwordEncoder->encodePassword($someuser, $pwd['newpassword']);
				            $user->setPassword($newcryptedpwd);
			    			$em->flush();
							return $this->redirectToRoute('account');
						}
						else
						{
							$errorpwd2 = 'Veuillez entrer le même mot de passe';
						}
					}
					else
					{
						$errorpwd1 = "Ce n'est pas le bon mot de passe";
					}
		        }
		    }
		}


        return $this->render('screenAddictBundle:Default:account.html.twig',
		['mailForm' => $changemailForm->createView(),
		'errormail' => $errormail,
		'pwdForm' => $changepwdForm->createView(),
		'errorpwd1' => $errorpwd1,
		'errorpwd2' => $errorpwd2]);
    }

	public function generateRandomString($length = 10)
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
}
