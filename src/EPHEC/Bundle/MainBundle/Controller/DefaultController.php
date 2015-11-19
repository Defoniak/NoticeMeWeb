<?php

namespace EPHEC\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Exception;
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
        $key = "Notice Me Sempai";
        $mdp = (isset($_POST["password"]))?$this->decrypt($_POST["password"], $key):"test";
        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";

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
            $value = ($encoder->isPasswordValid($result->getPassword(),$mdp,$result->getSalt())) ? $result->getId(): 0;

            //$value = ($bool == true)?1:0;

            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>$value));
        }
    }

    public function androidMemoListAction(){
        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";
        $key = "Notice Me Sempai";
        $password = (isset($_POST["password"]))?$this->decrypt($_POST["password"], $key):"test";
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByEmail($mail);
        $em = $this->getDoctrine()->getManager();

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $bool = ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) ? 1 : 0;
        if($bool === 0) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));

        $groups = $user->getGroup();
        $alarms = array();
        foreach($groups as $group){
            $query = $em->getRepository("EPHECNoteBundle:Alarm")->createQueryBuilder('a')
                ->where('a.deletedAt IS NULL')
                ->andwhere('a.group = :groups')
                ->setParameter('groups', $group)
                ->getQuery();

            $alarms[]= $query->getResult();
            //FIN D AJOUT DE CODE EXPLOSION\
        }


        $tab2 = array();
        //$i = 0;
        if(!empty($alarms) && !empty($alarms[0])){
            $empty = 0;
            foreach($alarms[0] as $value){
                //$i++;
                $tab2[] = array('title'=>$value->getTitle(),'desc'=>$value->getMemo(),'date'=>date_format($value->getDatealarm(), 'Y-m-d H:i:s'),
                    'lat'=>$value->getLatitude(),'long'=>$value->getLongitude(), 'id'=>$value->getId());
            }

        }
        else { $empty = 1;}

        $value = json_encode(array("json" => $tab2));
        return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>$value));
    }

    public function androidGetUserAction(){
        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";
        $key = "Notice Me Sempai";
        $password = (isset($_POST["password"]))?$this->decrypt($_POST["password"], $key):"test";
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByEmail($mail);

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $bool = ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) ? 1 : 0;
        if($bool === 0) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));

        $tab = array("firstname" => $user->getFirstname(), "lastname" => $user->getLastname());

        $value = json_encode($tab);
        return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>$value));
    }

    public function androidEditUserAction(){
        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";
        $newMail = (isset($_POST["mail"]))?$_POST["mail"]:"test@yopmail.com";
        $firstname = (isset($_POST["firstname"]))?$_POST["firstname"]:"blop";
        $lastname = (isset($_POST["lastname"]))?$_POST["lastname"]:"biblop";
        $key = "Notice Me Sempai";
        $password = (isset($_POST["password"]))?$this->decrypt($_POST["password"], $key):"test";
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByEmail($mail);

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $bool = ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) ? 1 : 0;
        if($bool === 0) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));

        $user->setEmail($newMail);
        $user->setFirstname($firstname);
        $user->setLastName($lastname);
        try {
            $userManager->updateUser($user);
        }
        catch(Exception $e){
            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));
        }

        return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>1));
    }

    public static function encrypt($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = DefaultController::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }
    private static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    public static function decrypt($sStr, $sKey) {
        $decrypted= mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            base64_decode($sStr),
            MCRYPT_MODE_ECB
        );
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
}
