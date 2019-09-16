<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/11/2017
 * Time: 19:54
 */
namespace Game\Controleur;
use Game\Controleur\ControleurCompte;
use Game\Models\Contient;
use Game\Models\Pioche;
use Game\Vue\VueAccueil;
use Game\Models\Partie;
use Game\Models\Carte;
use Illuminate\Contracts\Support\Arrayable;
use Slim\Slim;
use Illuminate\Support\Facades\DB;

class ControleurAccueil{

    /*
     * fonction qui defini l'accueil
     */
    function accueil(){
        //on recupère les partie qui n'ont pas commencé
        $partie=Partie::whereColumn('nbMax','>','nbJoueur')->get();
        //on definit une des variable qu'on va transmettre à la vue
        $lPartie=Array();
        $i=0;
        //on parcour les partie
        foreach ($partie as $p){
            //on place dans la case i du tableau les donné liée a la partie et a son auteur
            $lPartie[$i]=Array("partie"=>$p,"createur"=>$p->auteur()->first( ));
            $i++;
        }
        //on recupère les c8 type de carte
        $c=Carte::take(8)->get();
        //on crée la vue
        $vue=new VueAccueil(["partie"=>$lPartie,"cartes"=>$c]);
        $html=$vue->render(0);
        //on l'affiche
        echo $html;
    }


}