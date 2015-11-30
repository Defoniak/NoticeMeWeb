<?php

namespace EPHEC\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Exception;
use Assetic\Asset;
use EPHEC\Bundle\NoteBundle\Entity\Alarm;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EPHECMainBundle:Default:index.html.twig');
    }

    public function snakeAction(){
        return $this->render('EPHECMainBundle:Default:snake.html.twig');
    }

    public function androidLoginAction()
    {
        $password = $this->getPassword();
        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";
        $userManager = $this->get('fos_user.user_manager');

        $result = $userManager->findUserByEmail($mail);
        if(is_null($result)){
            $newUser = $userManager->createUser();
            $newUser->setEmail($mail);
            $newUser->setPlainPassword($password);
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
            $value = ($encoder->isPasswordValid($result->getPassword(),$password,$result->getSalt())) ? $result->getId(): 0;

            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>$value));
        }
    }

    public function androidMemoListAction(){
        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";
        $password = $this->getPassword();
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByEmail($mail);
        if(is_null($user)) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>-1));
        $em = $this->getDoctrine()->getManager();

        $bool = $this->checkUser($user,$password);
        if($bool === 0) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>-2));

        $groups = $user->getGroup();
        $alarms = array();
        foreach($groups as $group){
            $query = $em->getRepository("EPHECNoteBundle:Alarm")->createQueryBuilder('a')
                ->where('a.deletedAt IS NULL')
                ->andwhere('a.group = :groups')
                ->setParameter('groups', $group)
                ->getQuery();

            $alarms[]= $query->getResult();
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
        $password = $this->getPassword();
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByEmail($mail);
        if(is_null($user)) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));

        $bool = $this->checkUser($user,$password);
        if($bool === 0) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));

        $tab = array("firstname" => $user->getFirstname(), "lastname" => $user->getLastname());

        $value = json_encode(array("json" => $tab));
        return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>$value));
    }

    public function androidEditUserAction(){
        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";
        $newMail = (isset($_POST["mail"]))?$_POST["mail"]:"test@yopmail.com";
        $firstname = (isset($_POST["firstname"]))?$_POST["firstname"]:"blop";
        $lastname = (isset($_POST["lastname"]))?$_POST["lastname"]:"biblop";
        $password = $this->getPassword();

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByEmail($mail);
        if(is_null($user)) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));
        $bool = $this->checkUser($user,$password);
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

    public function androidEditMemoAction(){
        $em = $this->getDoctrine()->getManager();

        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";
        $password = $this->getPassword();
        $id = (isset($_POST["idmemo"]))?$_POST["idmemo"]:16;
        $title = (isset($_POST["title"]))?$_POST["title"]:"super test modif";
        $desc = (isset($_POST["description"]))?$_POST["description"]:"super test modif";
        $date = (isset($_POST["date"]))?$_POST["date"]:new \DateTime();
        $lon = (isset($_POST["longitude"]))?$_POST["longitude"]:"4.614397287368774";
        $lat = (isset($_POST["latitude"]))?$_POST["latitude"]:"50.717024472511845";
        $address = (isset($_POST["address"]))?$_POST["address"]:"Belgium";

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByEmail($mail);
        if(is_null($user)) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));
        $bool = $this->checkUser($user,$password);
        if($bool === 0) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));

        if($id == -1){
            $groups = $user->getGroup();
            $newAlarm = new Alarm();
            $newAlarm->setTitle($title);
            $newAlarm->setMemo($desc);
            $newAlarm->setDatealarm(new \DateTime($date));
            $newAlarm->setDatevalid(new \DateTime($date));
            $newAlarm->setAddress($address);
            $newAlarm->setLongitude($lon);
            $newAlarm->setLatitude($lat);
            $newAlarm->setGroup($groups[0]);

            $em->persist($newAlarm);
            try{
                $em->flush();
            }
            catch(Exception $e){
                return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));
            }

            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>$newAlarm->getId()));
        }
        else{
            $alarm = $em->getRepository("EPHECNoteBundle:Alarm")->find($id);
            $alarm->setTitle($title);
            $alarm->setMemo($desc);
            $alarm->setAddress($address);
            $alarm->setDatealarm(new \DateTime($date));
            $alarm->setLongitude($lon);
            $alarm->setLatitude($lat);
            try{
                $em->flush();
            }
            catch(Exception $e){
                return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));
            }

            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>1));
        }
    }

    public function androidRemoveMemoAction(){
        $em = $this->getDoctrine()->getManager();

        $mail = (isset($_POST["username"]))?$_POST["username"]:"test@yopmail.com";
        $password = $this->getPassword();
        $id = (isset($_POST["id"]))?$_POST["id"]:16;

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByEmail($mail);
        if(is_null($user)) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));
        $bool = $this->checkUser($user,$password);
        if($bool === 0) return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));

        //$em->remove($em->getRepository("EPHECNoteBundle:Alarm")->find($id));
        $alarm = $em->getRepository("EPHECNoteBundle:Alarm")->find($id);
        $alarm->setDeletedAt(new \DateTime());
        try{
            $em->flush();
        }
        catch(Exception $e){
            return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>0));
        }

        return $this->render('EPHECMainBundle:Default:androidlogin.html.twig', array("value"=>1));
    }

    public function checkUser($user, $password){
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) ? 1 : 0;
    }

    public function getPassword(){
        $key = "Notice Me Sempai";
        return (isset($_POST["password"]))?$this->decrypt($_POST["password"], $key):"test";
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
