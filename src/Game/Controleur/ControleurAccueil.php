<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/11/2017
 * Time: 19:54
 */
namespace Game\Controleur;

use Game\Vue\VueAccueil;

class ControleurAccueil{

    function accueil(){
        $vue=new VueAccueil();
        $html=$vue->render(0);
        echo $html;
    }

    function formCo(){
        $vue=new VueAccueil();
        $html=$vue->render(1);
        echo $html;
    }

    function regle(){
        $vue=new VueAccueil();
        $html=$vue->render(2);
        echo $html;
    }

}