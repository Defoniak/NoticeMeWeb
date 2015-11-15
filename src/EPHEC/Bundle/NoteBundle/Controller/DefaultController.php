<?php

namespace EPHEC\Bundle\NoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EPHEC\Bundle\NoteBundle\Entity\Alarm;
use EPHEC\Bundle\NoteBundle\Repository;

class DefaultController extends Controller
{
    public function indexAction($val=0)
    {
        $tab2 = array();
        $alarm = new Alarm();
        $em = $this->getDoctrine()->getManager();
        $groups = $this->getUser()->getGroup();
        $alarms = array();
        foreach($groups as $group){
            //$alarms[] = $em->getRepository("EPHECNoteBundle:Alarm")->findBy(array('group'=>$group)); // alarms ==> tableau d'alarmes
            // AJOUT DE CODE EXPLOSION
            /*$repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('EPHECNoteBundle:Alarm')
            ;*/
            //$alarms[] = $repository->findNotArchivedAlarms($group); //EXPLOSION

            /** @var  $repository \Doctrine\ORM\EntityManager */
            $query = $em->getRepository("EPHECNoteBundle:Alarm")->createQueryBuilder('a')
                ->where('a.deletedAt IS NULL')
                ->andwhere('a.group = :groups')
                ->setParameter('groups', $group)
                ->getQuery();
            $alarms[]= $query->getResult();
            //FIN D AJOUT DE CODE EXPLOSION\
        }
        //ajouter un boucle
        //print_r($alarms[0]);
        if(!empty($alarms) && !empty($alarms[0])){
            $empty = 0;
            foreach($alarms[0] as $value){
                $tab2[] = array('desc'=>$value->getTitle(),'date'=>date_format($value->getDatealarm(), 'Y-m-d H:i:s'),
                    'lat'=>$value->getLatitude(),'long'=>$value->getLongitude(), 'id'=>$value->getId());
            }

        }
        else { $empty = 1;}

        $note = json_encode($tab2);

        $formBuilder = $this->get('form.factory')->createBuilder('form', $alarm);
        $formBuilder
            ->add('datealarm', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'input' => 'datetime'
            ])
            ->add('latitude', 'text')
            ->add('longitude', 'text')
            ->add('title', 'text')
            ->add('memo', 'textarea')
            ->add('save', 'submit');
        $form = $formBuilder->getForm();
        //fin ajout form
        return $this->render('EPHECNoteBundle:Default:index.html.twig', array('note' => $note, 'form' => $form->createView(),
            'empty' => $empty, 'val' => $val ));
    }
    public function addMemoAction(){
        if ($this->get('request')->getMethod() == 'POST') {
            if(isset($_POST["form"]["datealarm"]) && isset($_POST["form"]["latitude"])
                && isset($_POST["form"]["longitude"]) && isset($_POST["form"]["title"]) && isset($_POST["form"]["memo"])){
                $alarm = new Alarm();
                $em = $this->getDoctrine()->getManager();
                $alarm->setGroup($this->getUser()->getGroup()[0]);
                $date = $_POST["form"]["datealarm"];
                $alarm->setDateAlarm((new \DateTime())->setDate(substr($date, 6, 4), substr($date, 3, 2),
                    substr($date, 0, 2))->setTime(substr($date, 11, 2), substr($date, 14, 2)));
                $alarm->setLatitude($_POST['form']['latitude']);
                $alarm->setLongitude($_POST['form']['longitude']);
                $alarm->setTitle($_POST['form']['title']);
                $alarm->setMemo($_POST['form']['memo']);
                $em->persist($alarm);
                $em->flush();
                $id=$alarm->getId();
                $res = json_encode(array('res' => true,'id' => $id));
                return $this->render('EPHECNoteBundle:Default:ajax.html.twig', array('res' => $res));
            }
            else return $this->render('EPHECNoteBundle:Default:ajax.html.twig', array('res' => false));
        }
        else return $this->render('EPHECNoteBundle:Default:ajax.html.twig', array('res' => false));
    }
    public function editMemoAction($idMemo){

        // why r u doing this shitbro
        $request = $this->get('request');

        if (is_null($idMemo)) {
            $postData = $request->get('testimonial');
            $id = $postData['id'];
        }

        $em = $this->getDoctrine()->getEntityManager();
        $alarm = $em->getRepository("EPHECNoteBundle:Alarm")->find($idMemo);
        $formBuilder = $this->get('form.factory')->createBuilder('form', $alarm);
        $formBuilder
            //->add('datealarm','text')
            ->add('datealarm', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'input' => 'datetime'
            ])
            ->add('latitude', 'text')
            ->add('longitude', 'text')
            ->add('title', 'text')
            ->add('memo', 'textarea')
            ->add('save', 'submit');
        $form = $formBuilder->getForm();

        if ($request->getMethod() == 'POST') {
            $date = $_POST["form"]["datealarm"];
            $alarm->setDateAlarm((new \DateTime())->setDate(substr($date, 6, 4), substr($date, 3, 2),
                substr($date, 0, 2))->setTime(substr($date, 11, 2), substr($date, 14, 2)));
            $alarm->setLatitude($_POST['form']['latitude']);
            $alarm->setLongitude($_POST['form']['longitude']);
            $alarm->setTitle($_POST['form']['title']);
            $alarm->setMemo($_POST['form']['memo']);
            $em->flush();

            return $this->redirect($this->generateUrl('ephec_note_homepage'));
        }

        return $this->render('EPHECNoteBundle:Default:edit.html.twig', array('id' => $idMemo,'form' => $form->createView()));
    }
    public function archiveMemoAction($idMemo){
        $em = $this->getDoctrine()->getEntityManager();
        $alarm = $em->getRepository("EPHECNoteBundle:Alarm")->find($idMemo);
        $alarm->setDeletedAt(new \DateTime('now'));
        $em->flush();

        $res = json_encode(array('res' => true));
        return $this->render('EPHECNoteBundle:Default:ajax.html.twig', array('res' => $res));

    }
    public function archiveListAction(){
        $tab = array();
        $em = $this->getDoctrine()->getManager();
        $groups = $this->getUser()->getGroup();
        $alarms = array();
        foreach($groups as $group){
            $alarms[] = $em->getRepository("EPHECNoteBundle:Alarm")->findBy(array('group'=>$group)); // alarms ==> tableau d'alarmes
        }
        if(!empty($alarms) && !empty($alarms[0])){
            $empty = 0;
            foreach($alarms[0] as $value){
                $tab[] = array('desc'=>$value->getTitle(),'date'=>date_format($value->getDatealarm(), 'Y-m-d H:i:s'),
                    'lat'=>$value->getLatitude(),'long'=>$value->getLongitude(), 'id'=>$value->getId(), 'deletedAt'=>$value->getDeletedAt());
            }

        }
        else { $empty = 1;}
        $note = json_encode($tab);

        return $this->render('EPHECNoteBundle:Default:archivedList.html.twig', array('note' => $note, 'empty' => $empty));
    }
    public function archiveListArchiveAction($idMemo){
        $em = $this->getDoctrine()->getEntityManager();
        $alarm = $em->getRepository("EPHECNoteBundle:Alarm")->find($idMemo);
        $alarm->setDeletedAt(new \DateTime('now'));
        $em->flush();

        $res = json_encode(array('res' => true));
        return $this->render('EPHECNoteBundle:Default:ajax.html.twig', array('res' => $res));
    }
    public function archiveListActiveAction($idMemo){
        $em = $this->getDoctrine()->getEntityManager();
        $alarm = $em->getRepository("EPHECNoteBundle:Alarm")->find($idMemo);
        $alarm->setDeletedAt(null);
        $em->flush();

        $res = json_encode(array('res' => true));
        return $this->render('EPHECNoteBundle:Default:ajax.html.twig', array('res' => $res));
    }
}
