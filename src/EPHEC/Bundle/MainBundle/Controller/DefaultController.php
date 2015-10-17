<?php

namespace EPHEC\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        echo "main";
        return $this->render('EPHECMainBundle:Default:index.html.twig', array('name' => 'test'));
        //return $this->render('app:Ressources:views:default:base2.html.twig', array('name' => 'test'));
    }
    public function loginAndroidAction($name, $test)
    {
        return $this->render('app:Ressources:views:default:base2.html.twig', array('name' => $name, 'test' =>$test) );
    }
}
