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

    public function xor_this($string) {

        // Let's define our key here
        $key = ('css is awesome !');

        // Our plaintext/ciphertext
        $text =$string;

        // Our output text
        $outText = '';

        // Iterate through each character
        for($i=0;$i<strlen($text);)
        {
            for($j=0;($j<strlen($key) && $i<strlen($text));$j++,$i++)
            {
                $outText .= $text{$i} ^ $key{$j};
                //echo 'i='.$i.', '.'j='.$j.', '.$outText{$i}.'<br />'; //for debugging
            }
        }
        return $outText;
    }

    public function androidLoginAction()
    {
        //if(! isset($_POST["password"])) $_POST["password"] = "50bcbe777bdb359fff286d026ea74ac97d85de182c638d84f8dbee4929028945229a0db7538aca8eecafe74ec4bfdafbe8deffd52725a4820cb8c59b6b429f931d1c6ce86c552c11f6341f88dc491418f9d1bded68df69ece62fb3404ba58bc749edbc619e0fb0554c2e9337a244a3dc25ede96804e316191fb4694443834f26";
        //$path = 'file://'.$this->get('kernel')->getRootDir() . '/../web/bundles/EPHEC/pubpriv.pem';
        //$key = openssl_get_privatekey($path);
        //dump($key);
        //$mdp = "";
        //$blop = openssl_private_decrypt(base64_decode($_POST["password"]),$mdp,$key);
        //dump($mdp);
        //dump($blop);

        //if(! isset($_POST["password"])) $_POST["password"] = "test";
        //$mdp = $this->xor_this($_POST["password"]);
        //dump($mdp);
        //dump($this->xor_this($mdp));
        //dump($this->xor_this("2322084"));
        $mdp = (isset($_POST["password"]))?$_POST["password"]:"test";
        $mail = (isset($_POST["username"]))?$_POST["username"]:"testblop@yopmail.com";

        $userManager = $this->get('fos_user.user_manager');

        $result = $userManager->findUserByEmail($mail);
        //dump($result);
        if(is_null($result)){
            $newUser = $userManager->createUser();

            $newUser->setEmail($mail);
            $newUser->setPlainPassword($mdp);
            $newUser->setUsername($mail);
            $newUser->setLastName("");
            $newUser->setFirstName("");
            $newUser->setEnabled(true);
            $userManager->updateUser($newUser);
            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>3));
        }
        else{
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($result);
            //$user = $userManager->findUserBy(array('email' => $mail,'password' => $userManager-> $encoder->encodePassword($mdp,$result->getSalt())));
            $value = ($encoder->isPasswordValid($result->getPassword(),$mdp,$result->getSalt())) ? 1: 0;

            //$value = ($bool == true)?1:0;

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
