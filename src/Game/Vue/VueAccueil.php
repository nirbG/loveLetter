<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/11/2017
 * Time: 19:56
 */
namespace Game\Vue;

class VueAccueil{

    private $content;
    function __construct($req=null){
        $this->content=$req;
    }

    public function render($id){
        $app=\Slim\Slim::getInstance();

        //initialisation des routes
        $urlCo=$app->urlFor('seConnecter',['erreur'=>'input']);

        $urlCompte=$app->urlFor("compte");
        $js="";
        switch ($id){
            case 0:
                $cont=$this->accueil($app);
                break;
            case 1:
                $cont="";
                $js="<script type='text/javascript' src='theme/js/form.js'></script>";
                break;
            case 2:
                $cont=$this->ajoutP($app);
                $js="<script type='text/javascript' src='theme/js/formCreationP.js'></script>";
                break;

        }
        $bCo=<<<END
<form class="navbar-form navbar-right inline-form" action="$urlCo" method="get">
                <div class="form-group">
                    <button id="compte" class="btn btn-primary btn-sm">Se connecter</button>
                </div>
              </form>
END;

        if(isset($_SESSION['profile'])){
            $bCo=<<<END
<form class="navbar-form navbar-right inline-form" action="$urlCompte" method="get">
                <div class="form-group">
                <button id="compte" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-user"></span></button>
                </div>
              </form>
END;

        }
        $html=<<<END
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
  </head>
<!-- NAVBAR
================================================== -->
  <body>
    <div class="navbar-wrapper">
        <div class="container-fluid">
        <nav class="navbar navbar-inverse navbar-static-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#"><img src="theme/ressource/love-letter-logo.png"></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="">Home</a></li>
                <li><a href="#relge">Régles</a></li>
                <li><a href="#card">Les Cartes</a></li>
              </ul>
                $bCo              
            </div>
          </div>
        </nav>
        </div>
      </div>
    </div>
    $cont
    </div><!-- /.container -->
      <footer class="container-fluid">
        <p class="pull-right"><a href="#">Back to top</a></p>
        <div class="col-lg-12"><img src="theme/ressource/UL_LOGO_BLANC_RVB.png"></div>
      </footer>

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
         <script src="theme/js/ancre.js"></script>
         <script src="theme/js/deco.js"></script>
  </body>
</html>

END;
        return $html;
    }

    public function accueil($app){
        $urlAjP=$app->urlfor("ajoutPartie",["erreur"=>"input"]);
        $cont="";
        foreach ($this->content["partie"] as $p) {
            $crea=$p["createur"];
            $p=$p["partie"];
            $urlsalle=$app->urlfor("rejoindreSalle",["id"=>$p->id]);
            $cont.=<<<END
            <div class=" col-sm-3 panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">$p->nom</h3>      
               </div>
               <div class="panel-body">
                   <p>nbJoueur: <span class="badge">$p->nbJoueur/$p->nbMax</span><p>
                   <p>crée par : $crea->pseudo</p>
                   <a href="$urlsalle" class="btn btn-block "><span class="glyphicon glyphicon-share-alt"></span> Renjoindre</a>
               </div>    
            </div>
END;
        }
        $cont.=<<<END
            <div class="addPartie col-sm-3 panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Créer une Partie</h3>     
                </div>  
                <div class="panel-body">
                    <a href="$urlAjP" class=""><img src="theme/ressource/plus-black-symbol_icon-icons.png"></a
                </div>
            </div>
        </div>
END;
        $ind="";
        $card="";
        $i=0;
        foreach ($this->content["cartes"] as $c) {
            if($i==0){
                $active="active";
            }else{
                $active="";
            }
            $ind.=<<<N
                <li data-target="#card" data-slide-to="$i" class="$active"></li>
N;
            $card.=<<<CARD
        <div class="item $active">
          <div class="col-lg-8 col-sm-push-2">
          <img class=" col-lg-8 " src="theme/ressource/cartes/$c->image" alt="First slide">
          <div class="desc">
            <div class="">
              <h1>$c->nomCarte</h1>
              <h4>Rang : $c->rang</h4>
              <h4>Nb carte dans le jeux: $c->nbCarte</h4>
              <h5>Effet : $c->effet</h5>
            </div>
          </div>
          </div>
        </div>
CARD;
            $i++;
        }

        $regle=<<<END
            <div class="row regle">
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
                <h4>Pourqoui aimer ce jeu : </h4>
                <ul>
                    <li>Explicable en quelques secondes</li>
                    <li>Jouable en quelques minutes</li>
                    <li>Une mécanique originale tellement elle est épurée: un choix parmi deux cartes à chaque tour. Point.</li>
                    <li>Une pointe amusante de tactique et de bluff</li>
                </ul>
            </div>
            <header class="page-header"><h1>les Cartes</h1></header>
      <div class="row">
      <div id="card" class=" card col-lg-12 carousel  slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        $ind
      </ol>
      <div class="carousel-inner" role="listbox">
        $card
      </div>
      <a class="left carousel-control " href="#card" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#card" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
    </div>
    <header class="page-header"></header>
END;


        $html=<<<END
            <!-- Carousel
    ================================================== -->
    <div id="phoneheader"class="page-banner">
          <img class="" src="theme/ressource/page-banner.jpg" alt="First slide">
        </div>
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="item active">
          <img class="first-slide" src="theme/ressource/Love-Letter-Header.jpg" alt="First slide">
          <div class="container">
            <div class="carousel-caption">
            <div class="backG"></div>
              <h1>Quand l’amour ne tient qu’à une lettre...</h1>
               <p>Votre but est de faire parvenir une lettre d’amour à la Princesse. Malheureusement, elle s’est enfermée dans 
               le palais et vous devrez dépendre d’intermédiaires pour livrer votre message.</p>
            </div>
          </div>
        </div>
        <div class="item">
          <img class="second-slide" src="theme/ressource/Love-Letter-Header2.jpg" alt="Second slide">
          <div class="container">
            <div class="carousel-caption">
              <div class="backG"></div>
              <h1>Durant la partie,</h1>
              <p>vous conservez secrètement une carte en main. Il s’agit de la personne qui tient votre billet doux 
              pour la Princesse. Vous devez vous assurer qu’à la fin de la journée, la personne la plus proche de la 
              Princesse ait en main votre message, vous assurant ainsi que cette dernière le lira en premier!</p>
            </div>
          </div>
        </div>
      </div>
      <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
    <!-- /.carousel -->
   <div id="salon" class="container">
     <header class="page-header">
        <h1>Chercher une Partie</h1>
     </header>
     <div class="row">
         $cont
     </div>  
     <header class="page-header">
        <h1 id="relge">Régle du jeux</h1>
     </header>
        $regle
     </div>
END;

        return $html ;
    }

    public  function ajoutP($app){
        $CréP=$app->urlFor("creationPartie");
        $r="";
        if($this->content!=""){
            $r=<<<END
                <div id="erreurForm" class="col-lg-12 alert alert-danger alert-dismissable ">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    $this->content
                </div>
END;
        }
        $html=<<<END
        <div class="page-banner">
          <img class="" src="theme/ressource/page-banner.jpg" alt="First slide">
        </div>
        <div class="FormCo container">
            <form class="form-halertorizontal col-lg-offset-3 col-lg-6" action="$CréP" method="post">
                <legend>Créer une partie :</legend>
                $r
                <div class="row">
                <div class="form-group">
                    <label for="textarea" class="col-lg-offset-1 control-label"> * Nom de la partie :</label>
                    <div class="col-lg-10 col-lg-offset-1">
                        <input name="nom" type="text" class="form-control" id="nom">
                        <span class="help-block">Veuillez saisir un nom de partie</span>
                    </div>
                </div>
            </div>
                <div class="form-group">
                   <button type="submit" class="pull-right btn btn-default btn-block btn-LoveLetter">Créer la partie</button>
                </div>
            </form>
         </div>
END;
        return $html;

    }
}