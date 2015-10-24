<?php

namespace EPHEC\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EPHECMainBundle:Default:index.html.twig');
    }

    public function androidLoginAction()
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserBy(array('email' => $_POST['username'],'password' => $_POST['password']));
        $value = (is_null($user))?0:1;

        return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>$value));
    }
}
