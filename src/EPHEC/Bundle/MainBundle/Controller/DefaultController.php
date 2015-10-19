<?php

namespace EPHEC\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EPHECMainBundle:Default:index.html.twig');
    }
}
