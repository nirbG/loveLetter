<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/11/2017
 * Time: 19:56
 */
namespace Game\Vue;

class VueAccueil{

    function __construct(){
    }

    public function render($id){
        $app=\Slim\Slim::getInstance();

        //initialisation des routes
        $urlRegle=$app->urlFor("regle");
        $urlCo=$app->urlFor("seConnecter");
        $js="";
        switch ($id){
            case 0:
                $cont=$this->accueil();
                break;
            case 1:
                $cont=$this->formCo();
                $js="<script type='text/javascript' src='theme/js/form.js'></script>";
                break;
            case 2:
                $cont=$this->formCo();
                break;
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
    $js
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
                <li><a href="$urlRegle">Régles</a></li>
                <li><a href="#contact">Contact</a></li>
              </ul>
              <form class="navbar-form navbar-right inline-form" action="$urlCo" method="get">
                <div class="form-group">
                <button id="compte" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-user"></span></button>
                </div>
              </form>
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
      </footer>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="bootstrap/js/vendor/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>

END;
    return $html;
    }

    public function accueil(){
        $cont="";
        for($i=1;$i<=10;$i++){
            $cont.=<<<END
            <div class=" col-lg-3 panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Partie $i</h3>      
               </div>
               <div class="panel-body">
                   <p>nbJoueur: <span class="badge">/2</span><p>
                   <a href="#" class="btn btn-block "><span class="glyphicon glyphicon-share-alt"></span> Renjoindre</a>
               </div>    
            </div>
END;

        }
        $html=<<<END
         <div class="carousel-inner" role="listbox">
        <div class="item active">
          <img class="first-slide" src="theme/ressource/Love-Letter-Header.jpg" alt="First slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>Example headline.</h1>
              <p>Note: If you're viewing this page via a <code>file://</code> URL, the "next" and "previous" Glyphicon buttons on the left and right might not load/display properly due to web browser security rules.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Sign up today</a></p>
            </div>
          </div>
        </div>
        <div class="item">
          <img class="second-slide" src="theme/ressource/Love-Letter-Header2.jpg" alt="Second slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>Another example headline.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Learn more</a></p>
            </div>
          </div>
        </div>
        <div class="item">
          <img class="third-slide" src="theme/ressource/Love-Letter-Header2.jpg" alt="Third slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>One more for good measure.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
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
   <div id="salon" class="container">
     <header class="page-header">
        <h1>Chercher une Partie</h1>
     </header>
        <div class="row">
            $cont
        </div>        
    </div>
END;

        return $html ;
    }

    public  function formCo(){
        $html=<<<END
        <form action="co.html" method="get" onsubmit="return Form.module.submit.start()" >
            <fieldset >
                <legend>Personalia</legend>
                <h8 style="display: block">Name :<input id="name" type='text' name='name' value=''><p style="color:red;display: none"> name required</p> </h8>
                <h8 style="display: block">Email :<input id="email" type='text' name='email' value=''> <p style="color:red;display: none"> email required</p></h8>
                <h8 style="display: block">Date of birth :<input id="date" type='date' name='date' value=''> <p style="color:red;display: none"> date required</p></h8>
            </fieldset >
            <input  id='save' type='submit' value='submit'>
        </form>
END;
        return $html;

    }
}