<?php

namespace screenAddictBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('screenAddictBundle:Default:index.html.twig');
    }
}
