<?php
require 'vendor/autoload.php';
use \Game\Controleur\ControleurAccueil;
use \Game\Controleur\ControleurCompte;
use \Game\Controleur\ControleurJeu;


//conection a la base de donnée
$db = new \Illuminate\Database\Capsule\Manager();
$db->addConnection(parse_ini_file('src/conf/conf.ini'));
$db->setAsGlobal();
$db->bootEloquent();

//connection a FrameWork
$app=new \Slim\Slim;

session_start();
//home
$app->get('/',function(){(new ControleurAccueil())->accueil();})->name('accueil');
//rejoindre le jeu
$app->get('/rejoindreSalle:id',function($id){(new ControleurJeu())->rejoindreSalle($id);})->name('rejoindreSalle');
//la salle
$app->get('/salle:id',function($id){(new ControleurJeu())->salle($id);})->name('salle');
//la salle
$app->get('/salle:id/load',function($id){(new ControleurJeu())->load();})->name('load');
//distribuCarte
$app->get('/salle:id/distribue',function ($id){(new ControleurJeu())->distribue($id);})->name('distribue');
//Piocher
$app->get('/salle/Piocher',function (){(new ControleurJeu())->Piocher();})->name('Piocher');
//Jouer
$app->get('/salle/Jouer:idc',function ($idc){(new ControleurJeu())->Jouer($idc);})->name('Jouer');
//Jouer
$app->post('/salle/effect:rang',function ($rang){(new ControleurJeu())->effect($rang);})->name('effect');
//ranger le jeu
$app->get('/rangerJeu:id',function($id){(new ControleurJeu())->rangerJeu($id);})->name('rangerJeu');
//resultat de la partie
$app->get('/finPartie:id',function($id){(new ControleurJeu())->finPartie($id);})->name('finPartie');
//quitter la salle
$app->get('/logout',function (){(new ControleurJeu())->logout();})->name('logout');
//ajouter une Partie
$app->get('/AjoutPartie:erreur',function($erreur){(new ControleurCompte())->ajoutPartie($erreur);})->name('ajoutPartie');
//ajouter une Partie
$app->post('/CreationPartie',function(){(new ControleurCompte())->creationPartie();})->name('creationPartie');
//formConnexion
$app->get("/seConnecter:erreur",function($erreur){(new ControleurCompte())->formCo($erreur);})->name("seConnecter");
//formConnexion
$app->get("/monCompte",function(){(new ControleurCompte())->compte();})->name("compte");
//formulaire inscription
$app->get('/formInscription:erreur',function($erreur){(new ControleurCompte())->formInsc($erreur);})->name('formInscription');
//Inscription
$app->post('/Inscription',function(){(new ControleurCompte())->sInscrire();})->name('inscription');
//connexion
$app->post('/connexion',function(){(new ControleurCompte())->connexion();})->name('connexion');
//deconnexion
$app->get('/deconnexion',function(){(new ControleurCompte())->deconnexion();})->name('deconnexion');
//demande si le jeu est commencé
$app->post('/jeu/start',function(){(new ControleurJeu())->start();})->name('start');
//nettoye la partie -> pour faire des test
$app->get('/salle:id/clear',function($id){(new ControleurJeu())->clear($id);})->name('salleClear');

$app->run();