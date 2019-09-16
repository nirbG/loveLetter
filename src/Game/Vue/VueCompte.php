<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/11/2017
 * Time: 13:54
 */

namespace Game\Vue;


class VueCompte
{
    private $content;
    function __construct($req=null){
        $this->content=$req;
    }

    public function render($id){
        $cont="";
        $app=\Slim\Slim::getInstance();
        //initialisation des routes
        $urlA=$app->urlFor("accueil");
        $urlC=$app->urlFor("compte");
        $urlCo=$app->urlFor('seConnecter',['erreur'=>"input"]);
        $urlCompte=$app->urlFor("compte");

        $urldeco=$app->urlFor("deconnexion");
        $js="";
        switch ($id){
            case 0:
                $cont=$this->formCo($app);
                $js="<script type='text/javascript' src='theme/js/form.js'></script>";
                break;
            case 1:
                $cont=$this->formInsc($app);
                $js="<script type='text/javascript' src='theme/js/formInsc.js'></script>";
                break;
            case 3:
                $cont=$this->ajoutP($app);
                $js="<script type='text/javascript' src='theme/js/formCreationP.js'></script>";
                break;
            case 5:
                $cont=$this->detail($this->content);
                $chart=$this->chart($this->content);
                $js="<script type='text/javascript' src='MDB/js/mdb.min.js'></script>
                     $chart";
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
              <a class="navbar-brand" href="$urlC"><img src="theme/ressource/love-letter-logo.png"></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="$urlA">Home</a></li>
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
        <p>&copy; 2016 Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
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
    <script src="theme/js/deco.js"></script>
    $js
  </body>
</html>

END;
        return $html;
    }

    public  function formCo($app){
        $insc=$app->urlFor("formInscription",['erreur'=>"input"]);
        $co=$app->urlFor("connexion");
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
            <form class="form-horizontal col-lg-offset-3 col-lg-6" onsubmit="return Form.module.submit.start()"  action="$co" method="post">
                <div class="form-group">
                    <legend>Se Connecter</legend>
                    $r
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="text" class="col-lg-offset-1 control-label">Login : </label>
                        <div class="col-lg-10 col-lg-offset-1">
                            <input name="email" type="email" class="form-control" id="email">
                            <span class="help-block">Veuillez saisir un email correct</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="textarea" class="col-lg-offset-1 control-label">Mot de passe : </label>
                        <div class="col-lg-10 col-lg-offset-1">
                            <input name="password" type="password" class="form-control" id="mdp">
                            <span class="help-block">Veuillez saisir un mot de passe</span>
                        </div>
                    </div>
                </div>
                <a class="col-lg-offset-4 col-lg-4" href="$insc">Inscrivez vous!</a>
                <div class="form-group">
                     <button class="pull-right btn btn-default btn-block btn-LoveLetter">Envoyer</button>
                </div>
            </form>
        </div>
END;
        return $html;

    }
    public  function formInsc($app){

        $r="";
        if($this->content!=""){
            $r=<<<END
            <div class="alert alert-danger alert-dismissable col-lg-12">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                $this->content
            </div>
END;
        }
        $insc=$app->urlFor("inscription");
        $html=<<<END
    <div class="page-banner">
        <img class="" src="theme/ressource/page-banner.jpg" alt="First slide">
    </div>
    <div class="FormCo container">
        <form class="form-horizontal col-lg-offset-3 col-lg-6" onsubmit="return Form.module.submit.start()"  action="$insc" method="post">
            <div class="form-group">
                <legend>S'inscrire</legend>
                $r
            </div>
             <div class="row">
                    <div class="form-group">
                        <label for="textarea" class="col-lg-offset-1 control-label">*mail : </label>
                        <div class="col-lg-10 col-lg-offset-1">
                            <input name="email" type="email" class="form-control" id="email">
                            <span class="help-block">Veuillez saisir un mail correct</span>
                        </div>
                    </div>
                </div>
            <div class="row">
                <div class="form-group">
                    <label for="textarea" class="col-lg-offset-1 control-label">*Login : </label>
                    <div class="col-lg-10 col-lg-offset-1">
                        <input name="pseudo" type="text" class="form-control" id="pseudo">
                        <span class="help-block">Veuillez saisir un pseudo correct</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="textarea" class="col-lg-offset-1 control-label">*Mot de passe : </label>
                    <div class="col-lg-10 col-lg-offset-1">
                        <input name="password" type="password" class="form-control" id="password">
                        <span class="help-block">Veuillez saisir un mot de passe</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="textarea" class="col-lg-offset-1 control-label">*Confirmer le Mot de passe : </label>
                    <div class="col-lg-10 col-lg-offset-1">
                        <input name="confirm" type="password" class="form-control" id="confirm">
                        <span class="help-block">le mot de passe est invalide ou non identique aux precendent</span>
                    </div>
                </div>
            </div>
            <span>(*) champ obligatoire</span>
            <div class="form-group">
                <button type="submit" class="pull-right btn btn-default btn-block btn-LoveLetter">S'inscrire</button>
            </div>
        </form>
    </div>

END;
        return $html;

    }
    function detail($u){
        $app=\Slim\Slim::getInstance();
        //initialisation des routes
        $urldeco=$app->urlFor("deconnexion");
        $can="";
        if($u->partieGagner==0&&$u->PartiePerdu==0) {
            $can ="<p style='text-align: center'>Aucun statistique n'est disponible.</p>";
        }
        $cont=<<<END
        <div class="page-banner">
          <img class="" src="theme/ressource/page-banner.jpg" alt="First slide">
        </div>
        <div class="container">
        <header class="page-header">
            <h1>$u->pseudo</h1>
        </header>
            <div class="col-sm-offset-2 col-sm-4">
                 $can
                 <canvas id="pieChart"></canvas>
            </div>
            <div class="col-offset-sm-1 col-sm-5" style="float: right;">
              <form class="" action="$urldeco" method="get">
                <div class="form-group ">
                    <button id="Decobutton" class="btn btn-primary btn-sm col-lg-12">déconnexion</button>
                </div>
              </form>
            </div>
        </div>  
        <header class="page-header">
        </header>
END;
        return $cont;
    }
    function chart($u){
        $cont= <<<END
        <script type='text/javascript'>
        var ctxP = document.getElementById("pieChart").getContext('2d');
        var myPieChart = new Chart(ctxP, {
            type: 'pie',
    data: {
                labels: ["Gagné", "Perdu"],
        datasets: [
            {
                data: [$u->partieGagner, $u->PartiePerdu],
                backgroundColor: ["#8f1a35", "#5a5a5a"],
                hoverBackgroundColor: ["#82162d", "#484848"]
            }
        ]
    },
    options: {
                responsive: true
    }
});</script>
END;
        return $cont;
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
            <form class="form-halertorizontal col-lg-offset-3 col-lg-6" onsubmit="return Form.module.submit.start()"  action="$CréP" method="post">
                <legend>Créer une partie :</legend>
                $r
                <div id="formrow" class="row ">
                    <div class="form-group">
                        <label for="textarea" class="col-lg-offset-1 control-label"> * Nom de la partie :</label>
                        <div class="col-lg-10 col-lg-offset-1">
                            <input name="nom" type="text" class="form-control" id="nom">
                            <span class="help-block">Veuillez saisir un nom de partie</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textarea" class="col-lg-offset-1 control-label"> * Nombre de joueur :</label>
                        <div class="col-lg-10 col-lg-offset-1">
                            <div><INPUT type= "radio" name="nbJ" value="2"  checked> 2 joueurs</div>
                            <div><INPUT type= "radio" name="nbJ" value="3"> 3 joueurs</div>
                            <div><INPUT type= "radio" name="nbJ" value="4"> 4 joueurs</div>
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