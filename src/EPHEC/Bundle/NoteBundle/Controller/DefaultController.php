<?php

namespace EPHEC\Bundle\NoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EPHEC\Bundle\NoteBundle\Entity\Alarm;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        //usermanager get current user
        //
        $tab[]= array('desc'=>'Rendez-vous chez le dentiste!', 'date'=>'16/08/2015 8H30', 'lat'=>50.8504500, 'long'=>4.3487800);
        $tab[]= array('desc'=>'Rendez vous chez le docteur', 'date'=>'10/11/2015 8H30', 'lat'=>50.541441396370274, 'long'=>4.856913942187475);
        $tab[]= array('desc'=>'Rendez vous avec la mère de Nico', 'date'=>'12/11/2015 18H30', 'lat'=>50.7608912624449, 'long'=>4.758136789062519);
        $tab[]= array('desc'=>'Convention', 'date'=>'12/11/2015 18H30', 'lat'=>48.8647964666206, 'long'=>2.363014357812517);
        $tab[]= array('desc'=>'testtresèloing', 'date'=>'12/11/2015 18H30', 'lat'=>53.527320580555646, 'long'=>-8.409070368750008);
        $note = json_encode($tab);

        //ajout form on test ca va jamais marcher
        $alarm = new Alarm();
        $formBuilder = $this->get('form.factory')->createBuilder('form', $alarm);
        $formBuilder
            //->add('datealarm','text')
            ->add('datealarm', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'input' => 'datetime'
            ])
            ->add('latitude','text')
            ->add('longitude','text')
            ->add('title','text')
            ->add('memo', 'textarea')
            ->add('save','submit')
        ;
        $form = $formBuilder->getForm();
        //fin ajout form

        return $this->render('EPHECNoteBundle:Default:index.html.twig', array('note' => $note,'form' => $form->createView()));
    }
}
