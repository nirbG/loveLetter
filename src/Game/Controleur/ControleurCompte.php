<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/11/2017
 * Time: 13:54
 */

namespace Game\Controleur;

use Game\Models\Partie;
use Game\Models\User;
use Game\Vue\VueCompte;
use Game\utils\Authentification;

class ControleurCompte{

    /*
     * affichage du formulaire pour se connecter
     * $erreur id de l'erreur rencontré durant le traitement
     */
    function formCo($erreur){
        $vue=new VueCompte(ControleurCompte::erreur($erreur));
        echo $vue->render(0);
    }

    /*
     * connecte le client
     */
    function connexion(){
        $app=\Slim\Slim::getInstance();
        //on recupère les donné du formulaire
        $email = $app->request->post('email');
        $pass = $app->request->post('password');
        //on test s'il existe
        $erreur=Authentification::authenticate($email,$pass);
        //on test s'il y a une erreur
        if($erreur!=null) {
            //si oui on le redirige sur la meme page est on transmet l'idé de l'erreur
            $app->redirect($app->urlFor('seConnecter',['erreur'=>$erreur]));
        }
        //sinon on le redirige vers l'accueil
        $app->redirect($app->urlFor('accueil'));
        //$app->redirect($_SERVER["HTTP_REFERER"]);
    }

    /*
     * affiche le formulaire d'inscription
     */
    function formInsc($erreur){
        //on a ficche le formulaire avec l'erreur encontré a l'utilisateur
        $vue=new VueCompte(ControleurCompte::erreur($erreur));
        echo $vue->render(1);
    }

    /*
     * inscrit le client
     */
    function sInscrire(){
        $app = \Slim\Slim::getInstance();
        //on récupère les valeur du formulaire
        $email = $app->request->post('email');
        $pass = $app->request->post('password');
        $confirm = $app->request->post('confirm');
        $pseudo= $app->request->post('pseudo');
        //on crée le compte
        $erreur=Authentification::createUser($email,$pseudo,$pass,$confirm);
        //on test s'il y a une erreur
        if($erreur!=null){
            //si oui on le redirige vers la meme page et on lui transmet l'id de l'erreur rencontré
            $app->redirect($app->urlFor('formInscription',['erreur'=>$erreur]));
        }
        //sinon on le redirige pour qu'il se connecte
        $app->redirect($app->urlFor('seConnecter',['erreur'=>"input"]));

    }

    /*
     * on affiche le compte du client
     */
    function compte(){
        //on récuprère ses donnés
        $u=User::where("id","=",$_SESSION["profile"]["userid"])->first();
        $vue=new VueCompte($u);
        echo $vue->render(5);
    }

    /*
     * on deconnecte le client
     */
    function deconnexion(){
        $app = \Slim\Slim::getInstance();
        //on récupère ses donnés
        $client = User::where('id', '=',  $_SESSION['profile']['userid'])->first();
        //on indique qu'il s'est deconnecté
        $client->co=0;
        $client->save();
        //on supprime la variable de session
        unset( $_SESSION['profile']);
        //on le redirige vers l'accueil
        $app->redirect($app->urlFor('accueil'));
    }

    /*
    * renvoie vers un formuliare pour crée une partie
    */
    function ajoutPartie($erreur){
        //on récupère l'erreur
        $erreur=ControleurCompte::erreur($erreur);
        $app=\Slim\Slim::getInstance();
        //on test s'il est connecté
        if(isset($_SESSION["profile"])) {
            //on affiche le formulaire
            $vue = new VueCompte($erreur);
            $html = $vue->render(3);
        }else{
            //on le redirige vers le formulaire pour se connecter
            $app->redirect($app->urlFor('seConnecter',["erreur"=>"input"]));
        }
        echo $html;
    }

    /*
    * crée une partie dans la base de donné
    */
    function creationPartie(){
        $app=\Slim\Slim::getInstance();
        //on récupère les données du formulaire
        $name = $app->request->post('nom');
        $nbj = $app->request->post('nbJ');
        //on crée la partie
        $p=new Partie();
        $p->nom=$name;
        $p->nbMax=$nbj;
        $p->createur=$_SESSION["profile"]["userid"];
        $p->save();
        //on le redirige vers la salle
        $app->redirect($app->urlFor('rejoindreSalle',["id"=>$p->id]));
    }

    /*
     * determine l'erreur rencontré
     */
     static function erreur($id){
        switch ($id){
            case '1':
                return "le compte n'existe pas";
                break;
            case '2':
                return 'ce compte existe deja';
                break;
            case '3':
                return "l'une ou plusieur de valeur saisi ne sont pas correct ou non rempli";
                break;
            case '4':
                return "le mot de passe saisi n'est pas correct";
                break;
            case '5':
                return "vous n'avez pas rempli les champs obligatoire ou il ne sont pas identique";
                break;
            case '6':
                return "le compte est deja utilisé";
                break;
            case 'input':
                return "";
                break;
        }
    }
}