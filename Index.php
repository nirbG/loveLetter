<?php
require 'vendor/autoload.php';
use \Game\Controleur\ControleurAccueil;


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
//explication du jeu
$app->get('/regle',function(){(new ControleurAccueil())->regle();})->name('regle');
//formConnexion
$app->get("/seConnecter",function(){(new ControleurAccueil())->formCo();})->name("seConnecter");
//formulaire inscription
$app->get('/formInscription',function(){echo "LoveLetter";})->name('formInscription');
//Inscription
$app->post('/Inscription',function(){echo "LoveLetter";})->name('inscription');
//connexion
$app->post('/connexion',function(){echo "LoveLetter";})->name('connexion');

$app->run();