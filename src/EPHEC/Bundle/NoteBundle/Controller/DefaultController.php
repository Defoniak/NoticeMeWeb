<?php

namespace EPHEC\Bundle\NoteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $tab[]= array('desc'=>'Rendez-vous chez le dentiste!', 'date'=>'16/08/2015 8H30', 'lat'=>50.8504500, 'long'=>4.3487800);
        $tab[]= array('desc'=>'Rendez vous chez le docteur', 'date'=>'10/11/2015 8H30', 'lat'=>50.541441396370274, 'long'=>4.856913942187475);
        $tab[]= array('desc'=>'Rendez vous avec la mère de Nico', 'date'=>'12/11/2015 18H30', 'lat'=>50.7608912624449, 'long'=>4.758136789062519);
        $note = json_encode($tab);
        return $this->render('EPHECNoteBundle:Default:index.html.twig', array('note' => $note));
    }
}
