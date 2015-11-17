<?php

namespace EPHEC\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Assetic\Asset;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EPHECMainBundle:Default:index.html.twig');
    }

    public function androidLoginAction()
    {
        $path = 'file://'.$this->get('kernel')->getRootDir() . '/../web/bundles/EPHEC/pubpriv.pem';
        $key = openssl_get_privatekey($path);
        dump($key);
        $mdp = "";
        openssl_private_decrypt($_POST["password"],$mdp,$key);
        $userManager = $this->get('fos_user.user_manager');

        $result = $userManager->findUserByEmail($_POST["username"]);
        if(is_null($result)){
            $newUser = $userManager->createUser();

            $newUser->setEmail($_POST["username"]);
            $newUser->setPlainPassword($mdp);
            $newUser->setLastName("");
            $newUser->setFirstName("");
            $newUser->setEnabled(true);
            $userManager->updateUser($newUser);
            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>3));
        }
        else{
            $user = $userManager->findUserBy(array('email' => $_POST['username'],'password' => $mdp));
            $value = (is_null($user))?0:1;

            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>$value));
        }
    }

    /*public function androidRegisterAction()
    {
        $key = openssl_get_privatekey("file://path/to/file.pem");
        $mdp = "";
        openssl_private_decrypt($_POST["password"],$mdp,$key);
        $um = $this->get('fos_user.user_manager');
        $user = $um->createUser();

    }*/
}
