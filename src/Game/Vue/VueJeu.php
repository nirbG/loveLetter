<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 19/11/2017
 * Time: 15:54
 */

namespace Game\Vue;

use Game\Models\Carte;
use Game\Models\Joueur;
use Game\Models\Message;
use Game\Models\User;
use Slim\Slim;

class VueJeu{
    private $content;
    function __construct($req=null){
        $this->content=$req;
    }
    function render($id){
        $cont="";
        $js="";
        switch ($id){
            case 0:
                $cont=$this->partie();
                $load="<script src='theme/Js/jeu.js'></script>";
                $js=<<<END
                    <script src="theme/Js/logout.js"></script>
                    $load
                    <script src="theme/Js/Modal.js"></script>       
END;
                break;
            case 1:
                $cont=$this->gagnant();
                break;
        }
        $cont = <<<END
        <!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="theme/ressource/logo.png">
    <title>Love Letter</title>
    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="bootstrap/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="bootstrap/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="bootstrap/js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Custom styles for this template -->
    <link href="bootstrap/css/main.css" rel="stylesheet">
    <link href="bootstrap/css/lightBox.css" rel="stylesheet">
  </head>
    <body style="background-image:url('theme/ressource/plateau.jpg');background-size: 100%;">
        $cont
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
        <!--script src="bootstrap/js/vendor/holder.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="bootstrap/js/ie10-viewport-bug-workaround.js"></script>    
        $js
    </body>
</html>
END;
        echo $cont;
    }
    function partie(){
        /*
        $listU="";

        foreach ($this->content["joueur"] as $j){
            $u=$j->User;
            $listU.="<li>$u->pseudo  $u->id</li>";
        }
*/
        $salle=$this->content["partie"];
        $cartes=$this->content["cartes"];
        $app = \Slim\Slim::getInstance();
        $urlDeco=$app->urlFor("logout",["id"=>$_SESSION["idsalle"]]);
        $res=<<<END
        <div id="partie" class="container-fluid">
            <button id="Decobutton" data="$urlDeco" style="float: right; ">Déconexion <span class="glyphicon glyphicon-share"></span></button>
            <button id="help" class="btn-lg" style="float: right;border-radius: 150px;"><span class="glyphicon glyphicon-question-sign"></span></button>
            <div style="position: absolute;margin: 0%;">
                <h4 id="salle" data="$salle->id">$salle->nom</h4>
                <ul>
                </ul>
            </div>
        </div>
        <div id="myModal" class="modal">
        <!-- Modal content -->
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h2>AIDE</h2>
                </div>
                <div class="modal-body" style="max-height: 500px;overflow-y : scroll;">
                    <header class="page-header">
                        <h1>Rappel touche</h1>
                    </header>
                    <p>Pour piocher ou pour jouer une carte il faut utiliser le clic gauche</p>
                    <p>Pour afficher la carte en plus grand il faut utiliser le clic droit</p>
                    <header class="page-header">
                        <h1>Rappel régle</h1>
                    </header>
                    <h4>But :</h4>
                    <span> Finir le jeu avec la carte ayant le rang le plus élevé afin d'être celui 
                    qui portera la lettre d'amour à la princesse Annette.</span>
                    <h4>Mise en place :</h4>
                    <span>Mélangez les 16 cartes afin de former une pioche, prendre la première carte, sans 
                    la regarder et la retirer du jeu.
                    Si vous jouez à 2, piochez 3 cartes et disposez les face visible sur la table, elles ne 
                    seront pas utilisées pendant cette manche.
                    Chaque joueur pioche 1 carte et la garde secrète.</span>
                    <h4>Déroulement du jeu :</h4>
                    <span>Le premier joueur pioche une carte et l'ajoute à sa main. 
                    Il choisi une de ses cartes, la joue devant lui face visible et en applique les effets 
                    (voir la liste des cartes). Les cartes jouées restent devant chacun des joueurs dans 
                    l'ordre où elles ont été jouées.
                    Une fois les effets de la cartes appliqués, c'est au joueur suivant de jouer.
                    Si  un  joueur  est  éliminé  de  manche,  il  défausse  sa  main  face  visible,  mais  ne 
                    pioche pas de nouvelle carte.</span>
                    <h4>Fin de jeu :</h4>
                    <span>Lorsqu'un  joueur  pioche  la  dernière  carte,  il  joue  son  tour  et  la  manche  se 
                    termine.
                    Love  Letter  se  joue  en  plusieurs  manches,  chaque  manche  représentant  une 
                    journée, à la fin de la journée la princesse lit la lettre qui lui a été apporté, le 
                    joueur gagne alors un point.
                    Lorsqu'elle a reçu un certain nombre de lettres du même joueur, elle en tombe 
                    amoureuse et le joueur en question gagne la partie
                    Le nombre de lettres nécessaires pour conquérir le cœur de princesse dépend du 
                    nombre de joueurs. (2 joueurs = 7 points ;  3 joueurs = 5 points ; 4 joueurs = 4 
                    points) </span>
                    <header class="page-header"><h1>les Cartes</h1></header>
                    $cartes
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
END;
        return $res;

    }
    function gagnant(){
        $app=\Slim\Slim::getInstance();
        $url=$app->urlFor("accueil");
        $winner=$this->content["winner"];
        $text=<<<END
                <h1 id="salle" >Tu as perdu,</h1>
                <h4>la princess est tombé sous le charme de $winner->pseudo.</h4>
END;
        if($winner->id==$this->content["joueur"]){
            $text=<<<END
                <h1 id="salle" >Tu as gagné,</h1>
                <h4>la princess est tombé sous ton charme.</h4>
END;

        }
        $res=<<<END
        <div id="partie" class="container-fluid">
            <div class="resultat" >
                 $text
                 <a href="$url" class="btn btn-block "><span class="glyphicon glyphicon-share-alt"></span> Retour a l'accueil</a>
            </div>
        </div>
END;
        return $res;
    }
    function jeuxAdv($id){
        $cont="";

        if($id=="") {

            $cont .= <<<END
        <div id="mainAdv" class="row">
            <div class="col-lg-offset-4 col-lg-1">
                <h3>Score: 0</h3>
            </div>
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/vide.png">
            </div>
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/vide.png">
            </div>
        </div>
        <div id="plateauAdv" class="row carte">
            <div class="col-lg-1 center ">
                 <img src="theme/ressource/cartes/vide.png">
            </div>
        </div>
END;
        }else{
            $jeux=$this->content["jeux"][$id];
            $user=User::where("id","=",$id)->first();
            $point=$jeux["score"];
            $score=<<<END
            <div class="col-lg-offset-4 col-lg-1">
                <h3>Score: $point</h3>
                <p>$user->pseudo</p>
            </div>
END;
            if($jeux["nbCarte"]==0){
                $cont .= <<<END
        <div id="mainAdv" class="row">
            $score
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/vide.png">
            </div>
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/vide.png">
            </div>
        </div>
END;
            }else {
                if ($jeux["nbCarte"] == 2) {
                    $cont .= <<<END
        <div id="mainAdv" class="row">
            $score
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/Pioche.png">
            </div>
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/Pioche.png">
            </div>
        </div>
END;
                } else {
                    $cont .= <<<END
        <div id="mainAdv" class="row">
            $score
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/Pioche.png">
            </div>
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/vide.png">
            </div>
        </div>
END;
                }
            }
            $cont.="<div id='plateauAdv' class='row carte'>";

            if(sizeof($jeux["plateau"])!=0){
                foreach ($jeux["plateau"] as $p){
                    $c=Carte::where("id","=",$p->idCarte)->first();
                    $cont.="<div class='col-lg-1 '>
                            <img data-img='theme/ressource/cartes/$c->image' src='theme/ressource/cartes/$c->image'>
                        </div>";
                }
            }else{
                $cont.=<<<END
            <div class="col-lg-1 center ">
                 <img src="theme/ressource/cartes/vide.png">
            </div>
END;
            }

            $cont.="</div>";
        }
        return $cont;
    }

    function jeux($id){
        $cont="";
        $jeux=$this->content["jeux"][$id];
        $user=User::where("id","=",$id)->first();
        $point=$jeux["score"];
        $score=<<<END
            <div class=" col-lg-1">
                <h3>Score: $point</h3>
                <p>$user->pseudo</p>
            </div>
END;
        $cont .= "<div id='plateau' class='row carte'>";
        if(sizeof($jeux["plateau"])!=0){
            foreach ($jeux["plateau"] as $p) {
                $c = Carte::where("id", "=", $p->idCarte)->first();
                $cont .= "<div class='col-lg-1 '>
                            <img data-img='theme/ressource/cartes/$c->image' src='theme/ressource/cartes/$c->image'>
                        </div>";
            }
        } else {
            $cont .= <<<END
            <div class="col-lg-1 center ">
                 <img src="theme/ressource/cartes/vide.png">
            </div>
END;
        }
        $cont .= "</div>";
        if ($jeux["nbCarte"] == 0) {
            $cont .= <<<END
        <div id="main" class="row">
            <div class="col-lg-offset-5 col-lg-1">
                <img src="theme/ressource/cartes/vide.png">
            </div>
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/vide.png">
            </div>
            $score
        </div>
END;
        } else {
            if ($jeux["nbCarte"] == 2) {
                $carte="";
                $i=0;
                foreach ($jeux["main"] as $p) {
                    $res="";
                    if($i==0){
                        $res="col-lg-offset-5";
                        $i++;
                    }
                    $c = Carte::where("id", "=", $p->idCarte)->first();
                    $carte .= "<div class='col-lg-1 $res'>
                            <img data-img='theme/ressource/cartes/$c->image' src='theme/ressource/cartes/$c->image'>
                        </div>";
                }
                $cont .= <<<END
        <div id="main" class="row">
            $carte
            $score
        </div>
END;
            } else {
                $carte="";
                foreach ($jeux["main"] as $p) {
                    $c = Carte::where("id", "=", $p->idCarte)->first();
                    $carte .= "<div class='col-lg-1 col-lg-offset-5'>
                            <img data-img='theme/ressource/cartes/$c->image' src='theme/ressource/cartes/$c->image'>
                        </div>";
                }
                $cont .= <<<END
        <div id="main" class="row carte">
            $carte
            <div class="col-lg-1">
                <img src="theme/ressource/cartes/vide.png">
            </div>
            $score
        </div>
END;
            }
        }

        return $cont;
    }
}
