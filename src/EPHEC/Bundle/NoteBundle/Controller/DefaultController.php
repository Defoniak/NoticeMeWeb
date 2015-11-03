<?php

namespace EPHEC\Bundle\NoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EPHEC\Bundle\NoteBundle\Entity\Alarm;
use EPHEC\Bundle\NoteBundle\Repository;

class DefaultController extends Controller
{
    public function indexAction($val=0)
    {
        //usermanager get current user
        //
        $tab[] = array('desc' => 'Rendez-vous chez le dentiste!', 'date' => '16/08/2015 8H30', 'lat' => 50.8504500, 'long' => 4.3487800);
        $tab[] = array('desc' => 'Rendez vous chez le docteur', 'date' => '10/11/2015 8H30', 'lat' => 50.541441396370274, 'long' => 4.856913942187475);
        $tab[] = array('desc' => 'Rendez vous avec la mère de Nico', 'date' => '12/11/2015 18H30', 'lat' => 50.7608912624449, 'long' => 4.758136789062519);
        $tab[] = array('desc' => 'Convention', 'date' => '12/11/2015 18H30', 'lat' => 48.8647964666206, 'long' => 2.363014357812517);
        $tab[] = array('desc' => 'testtresèloing', 'date' => '12/11/2015 18H30', 'lat' => 53.527320580555646, 'long' => -8.409070368750008);
        //$note = json_encode($tab);
        $tab2 = array();

        $alarm = new Alarm();
        $em = $this->getDoctrine()->getManager();
        $groups = $this->getUser()->getGroup();
        $alarms = array();
        foreach($groups as $group){
            //$alarms[] = $em->getRepository("EPHECNoteBundle:Alarm")->findBy(array('group'=>$group)); // alarms ==> tableau d'alarmes
            // AJOUT DE CODE EXPLOSION
            $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('EPHECNoteBundle:Alarm')
            ;
            $alarms[] = $repository->findNotArchivedAlarms($group); //EXPLOSION

            //FIN D AJOUT DE CODE EXPLOSION

        }
        //ajouter un boucle
        print_r($alarms[0]);
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
                $res = json_encode(array('res' => true));
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
        return $this->render('EPHECNoteBundle:Default:archivedList.html.twig');
    }
}
