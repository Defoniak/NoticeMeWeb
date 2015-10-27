<?php

namespace EPHEC\Bundle\NoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EPHEC\Bundle\NoteBundle\Entity\Alarm;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //usermanager get current user
        //
        $tab[] = array('desc' => 'Rendez-vous chez le dentiste!', 'date' => '16/08/2015 8H30', 'lat' => 50.8504500, 'long' => 4.3487800);
        $tab[] = array('desc' => 'Rendez vous chez le docteur', 'date' => '10/11/2015 8H30', 'lat' => 50.541441396370274, 'long' => 4.856913942187475);
        $tab[] = array('desc' => 'Rendez vous avec la mère de Nico', 'date' => '12/11/2015 18H30', 'lat' => 50.7608912624449, 'long' => 4.758136789062519);
        $tab[] = array('desc' => 'Convention', 'date' => '12/11/2015 18H30', 'lat' => 48.8647964666206, 'long' => 2.363014357812517);
        $tab[] = array('desc' => 'testtresèloing', 'date' => '12/11/2015 18H30', 'lat' => 53.527320580555646, 'long' => -8.409070368750008);
        $note = json_encode($tab);

        //ajout form on test ca va jamais marcher
        $alarm = new Alarm();
        $em = $this->getDoctrine()->getManager();
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
        //fin ajout form
        if ($this->get('request')->getMethod() == 'POST'){
            //[datealarm => 29-10-2015 17:50, latitude => 48.28319289548349, longitude => 3.603515625, title => qsqsd, memo => sfsdfdsfdsf, save => , _token => Q9EYrSsS4eeSUSoEBboLMKYaB8A86coEcoukqoo8qlM]
            $alarm->setGroup($this->getUser()->getGroup()[0]);
            $date = $_POST["form"]["datealarm"];
            $alarm->setDateAlarm((new \DateTime())->setDate(substr($date,6,4),substr($date,3,2),substr($date,0,2))->setTime(substr($date,11,2),substr($date,14,2)));
            $alarm->setLatitude($_POST['form']['latitude']);
            $alarm->setLongitude($_POST['form']['longitude']);
            $alarm->setTitle($_POST['form']['title']);
            $alarm->setMemo($_POST['form']['memo']);
            dump($alarm);
            $em->persist($alarm);
            $em->flush();
        }

        return $this->render('EPHECNoteBundle:Default:index.html.twig', array('note' => $note, 'form' => $form->createView()));
    }
}
