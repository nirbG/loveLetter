<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 19/11/2017
 * Time: 15:54
 */

namespace Game\Controleur;

use Game\Models\Contient;
use Game\Models\Defausse;
use Game\Models\Desc;
use Game\Models\Joueur;
use Game\Models\Partie;
use Game\Models\Pioche;
use Game\Models\Plateau;
use Game\Models\Possede;
use Game\Models\User;
use Game\Models\Carte;
use \Game\Vue\VueJeu;

class ControleurJeu
{

    /*
     * ajout le client a une salle
     */
    function rejoindreSalle($id){
        //si les joueur n'est pas co il doir se connecter
        $app=\Slim\Slim::getInstance();
        if(!isset($_SESSION['profile'])){
            $app->redirect($app->urlFor('seConnecter',["erreur"=>"input"]));
        }
        //on initialise la variable de session
        $_SESSION["effet"]=0;//determine si un effet est en cour sinon elle est égale a 0
        $_SESSION['idsalle'] = $id;//determine l'id de la sallle
        $_SESSION['p'] = null;//pour tester
        //on récupère la partie
        $partie=Partie::where("id",'=',$id)->first();
        //on test s'il y a encore de la place
        if ($partie->nbMax > $partie->nbJoueur) {
            //on modifie les nombre de place occupé
            $partie->nbJoueur++;
            $partie->save();
            //on supprime l'ancien au cas ou il ne se soit pas deconnecter
            Joueur::where("id_user","=",$_SESSION["profile"]["userid"])->delete();
            $this->suppMainEtPlat($_SESSION["profile"]["userid"]);
            //on ajoute le joueur a la partie
            $j = new Joueur();
            $j->id_partie = $id;
            $j->id_user = $_SESSION["profile"]["userid"];
            $j->ordre = $partie->nbJoueur;
            $j->save();
            //on supprime l'ancien au cas ou il ne se soit pas deconnecter
            //on teste s'il y a assez de joueur pour commencer la partie
            if ($partie->nbMax == $partie->nbJoueur) {
                // choisi un joueur aléatoirement
                $randId = $partie->joueurs()->select('id')->inRandomOrder()->first()->id;
                Joueur::where("id_user","=",$randId)->update(["action"=>"joue"]);
                $partie->joue = $randId;
                $partie->save();
                $this->distribue($id);
            }
            //on redirige vers la salle de jeux
            $app->redirect($app->urlFor("salle",["id"=>$id]));
        }else{
            //il n'y a plus de place donc on redirige vers l'accueil
            $app->redirect($app->urlFor("accueil"));
        }
    }
    /*
     * affiche la salle
     */
    function salle($id){
        $app=\Slim\Slim::getInstance();
        //on initialise la variable qu'on va envoyer a la vue
        //on récupère les joueurs
        $joueur=Joueur::where("id_partie","=",$id)->get();
        //on récupère la partie
        $partie=Partie::where("id",'=',$id)->first();
        $c="";
        //on récupère les cartes
        $cartes=Carte::take(8)->get();
        foreach ($cartes as $carte){
            //on prepare les donné pour l'aide
            $c.="<h4>$carte->nomCarte :</h4>
              <p>Rang : $carte->rang</p>
              <p>Nb carte dans le jeux: $carte->nbCarte</p>
              <p>Effet : $carte->effet</p>";
        }
        //on initialise la variable a transmettre a la vue
        $jeux=["joueur"=>$joueur,"nbJ"=>$partie->nbMax,"partie"=>$partie,"cartes"=>$c];
        $vue=new VueJeu($jeux);
        echo $vue->render(0);
    }

    /*
     * retire l'utilisateur de la salle
     */
    function logout(){
        $app=\Slim\Slim::getInstance();
        //on récupère l'id de la session
        $id=$_SESSION['idsalle'];
        //on recupère la partie
        $partie=Partie::where("id",'=',$id)->first();
        //on decrement le nombre de joueur
        $partie->nbJoueur=$partie->nbJoueur-1;
        $partie->save();
        //on supprime le jeux du joueur et le joueur
        Joueur::where([["id_partie","=",$id],["id_user","=",$_SESSION["profile"]["userid"]]])->delete();
        Possede::where("idJoueur","=",$_SESSION["profile"]["userid"])->delete();
        Plateau::where("idJoueur","=",$_SESSION["profile"]["userid"])->delete();
        //on supprime la variable de session
        unset($_SESSION['idsalle']);
        //on le renvoi à l'accueil
        $app->redirect($app->urlFor("accueil"));
    }

    /*
     * on test si la partie a démarré
     */
    function start(){
        //on test si on est liée a la partie
        if (isset($_SESSION['idsalle'])){

            $id = $_SESSION['idsalle']; // id de la partie

            $tempsInit = time();//on initialise le temps
            //on récupère la partie si elle n'a pas encore commencer
            $partie=Partie::where("id",'=',$id)->whereColumn('nbMax','>','nbJoueur')->first();

            // tant que la partie n'est pas pleine attendre
            // arret de la boucle à ~25 secondes pour eviter le Maximum execution time /!\
            while ( (time()-$tempsInit) < 5 && $partie != null ){
                sleep(1);
                //on reteste
                $partie=Partie::where("id",'=',$id)->whereColumn('nbMax','>','nbJoueur')->first();
            }
            if ($partie == null){ // si partie est pleine
                // inialisation de la partie pour le joueur
                echo json_encode(Array('start'=>1));
            }else{
                // si la boucle s'arrete à ~25 secondes
                echo json_encode(Array('start'=>0,"nbJoueurRestant"=>$partie->nbMax-$partie->nbJoueur)); // le client devra refaire une requete
            }
        }
    }

    //nettoye la partie -> pour faire des test
    function clear($id){
        $partie=Partie::where("id",'=',$id)->first();
        $partie->nbJoueur = 0;
        $partie->save();
        Joueur::where('id_partie','=',$id)->delete();
    }

    /*
     * distribue les carte
     */
    function distribue($id){
        //supprime l'ancien jeu s'il existe
        $this->suppJeu($id);
        //créer une ppioche
        $pioche=$this->creerPioche($id);
        //mise en place du jeux
        $this->premiereDistribution($id,$pioche);
    }

    /*
     * le client pioche
     */
    function piocher(){
        //on récupère le nombre de carte
        $nbC=Possede::where("idJoueur","=",$_SESSION["profile"]["userid"])->count();
        //on récupère l'id du joueur
        $idJoueur = Partie::where("id", "=", $_SESSION["idsalle"])->first()->joue;
        // var_dump($idJoueur);
        // on test s'il a - de 2 carte et que c'est sont tour
        if($nbC<2 && $idJoueur == $_SESSION["profile"]["userid"]) {
            //recupère la pioche
            $pioche = Pioche::where("idPartie", "=", $_SESSION["idsalle"])->first();
            //on recupère le user
            $user = User::where("id", "=", $_SESSION["profile"]["userid"])->first();
            //on recupèrer les carte de la pioche
            $carte = $pioche->cartes()->get()->toArray();
            if($pioche->cartes()->count()>0) {
                $carte = $pioche->cartes()->get()->toArray();
                //defini le nb de carte restante
                $nbC = sizeof($carte);
                //on pioche une carte au hasard
                $numC = (int)rand(0, $nbC);
            }else{
                //sinon on  tire la derniere carte
                $numC=0;
            }
            $c = $carte[$numC];

            //ajout main
            $p = new Possede();
            $p->idCarte = $c['id'];
            $p->idJoueur = $user->id;
            $p->save();
            //on récupère la main
            $m=Possede::where('idJoueur','=',$_SESSION['profile']["userid"])->get();
            //on prépare les variable pour le test
            $c7=false;
            $c56=false;
            //on parcour la main
            foreach ($m as $card) {
                $card = $card->Carte()->first();
                //on test s'il posséde une countess
                if ($card->rang == 7) {
                    $c7 = true;
                }
                //on test s'il posséde un prince ou un king
                if ($card->rang == 5 || $card->rang == 6) {
                    $c56 = true;
                }
            }
            if ($c7 && $c56) {
                //s'il les test sont vrai alors on applique l'effet de la countess
                $this->effect(7);
            }
            //supp la carte de la pioche
            Contient::where([["idCarte", "=", $c['id']], ["idPioche", "=", $pioche->idPioche]])->delete();
        }
    }

    /*
     * applique les effets
     */
    function effect($rang){
        //on récupère la pioche
        $pioche = Pioche::where("idPartie", "=", $_SESSION["idsalle"])->first();
        $app=\Slim\Slim::getInstance();
        //on effectue un effet selon le rang transmis a cette fonction
        switch ($rang){
            case 8:
                $id=$_SESSION["profile"]["userid"];
                //on élimine le joueur
                Joueur::where('id_user','=',$id)->update(["action" => "éliminé"]);
                // on crée la description
                $d=new Desc();
                $d->idPartie=$_SESSION["idsalle"];
                $d->desc="<p><strong>".$_SESSION["profile"]["login"]."</strong> est éliminé car il a joué la princess</p>";
                $d->save();
                break;
            case 7:
                // on récupère la countess
                $c=Carte::where("rang","=",7)->first();
                //on la supprime de la mai,
                Possede::where([['idCarte', '=', $c->id],['idJoueur', '=', $_SESSION["profile"]["userid"]]])->delete();
                //on l'ajoute au plateau
                $p=new Plateau();
                $p->idJoueur=$_SESSION["profile"]["userid"];
                $p->idCarte=$c->id;
                $p->save();
                //on crée la description de l'evenement
                $d=new Desc();
                $d->idPartie=$_SESSION["idsalle"];
                $d->desc="<p><strong>".$_SESSION["profile"]["login"]."</strong> a defaussé la countess car il a un prince ou un king en main</p>";
                $d->save();
                break;
            case 6:
                //on récupère les id
                $idMoi=$_SESSION["profile"]["userid"];
                $idLui=$_POST['id'];
                //on récupère leur carte
                $mainMoi= Possede::where('idJoueur', '=',$idMoi)->first()->idCarte;
                $mainLui= Possede::where('idJoueur', '=',$idLui)->first()->idCarte;
                //on les echanges
                Possede::where('idJoueur', '=',$idMoi)->update(["idCarte" => $mainLui]);
                Possede::where('idJoueur', '=',$idLui)->update(["idCarte" => $mainMoi]);
                $u=User::where("id","=",$idLui)->first();
                //on crée l'historique
                $d=new Desc();
                $d->idPartie=$_SESSION["idsalle"];
                $d->desc="<p><win>".$_SESSION["profile"]["login"]."</win> a echanger sa main avec <loose>$u->pseudo</loose></p>";
                $d->save();
                break;
            case 5:
                //on récupère les valeur du joueur
                $id=$_POST["id"];
                //on récupère la main
                $main=Possede::where('idJoueur', '=',$id)->first();
                //on deffause la carte
                $p=new Plateau();
                $p->idJoueur=$main->idJoueur;
                $p->idCarte=$main->idCarte;
                $p->save();
                //on supprime la carte de sa main
                Possede::where('idJoueur', '=',$id)->delete();
                //on pioche
                $carte = $pioche->cartes()->get()->toArray();
                if($pioche->cartes()->count()!=0) {
                    if ($pioche->cartes()->count() > 0) {
                        $carte = $pioche->cartes()->get()->toArray();
                        //defini le nb de carte restante
                        $nbC = sizeof($carte);
                        //on pioche une carte au hasard
                        $numC = (int)rand(0, $nbC);
                    } else {
                        $numC = 0;
                    }
                    $c = $carte[$numC];
                    //ajout main
                    $main = new Possede();
                    $main->idCarte = $c['id'];
                    $main->idJoueur = $p->idJoueur;
                    $main->save();
                    //supp la carte de la pioche
                    Contient::where([["idCarte", "=", $c['id']], ["idPioche", "=", $pioche->idPioche]])->delete();
                }
                //on crée la description
                $d=new Desc();
                $d->idPartie=$_SESSION["idsalle"];
                $d->desc="<p><win>".$_SESSION["profile"]["login"]."</win> a fait defauser et piocher <loose>????</loose></p>";
                $d->save();
                break;
            case 4:
                //on protége le joueur de tous les effet pendant un tour
                $idMoi=$_SESSION["profile"]["userid"];
                Possede::where('idJoueur', '=',$idMoi)->update(["action" => "protege"]);
                //on crée la description
                $d=new Desc();
                $d->idPartie=$_SESSION["idsalle"];
                $d->desc="<p><strong>".$_SESSION["profile"]["login"]."</strong> est protégé pour un tour </p>";
                $d->save();
                break;
            case 3:
                //on récupère l'id du joueur
                $id=$_POST["id"];
                //on récupère l'id de l'adversaire
                $idadv=$_POST["idadv"];
                //on récupère l'adversaire
                $adv=User::where("id","=",$idadv)->first();
                //on récupère les  mains de joueurs
                $main=Possede::where('idJoueur', '=',$id)->first()->Carte()->first();
                $mainadv=Possede::where('idJoueur', '=',$idadv)->first()->Carte()->first();
                //on test si le joueur a une main inferieur a celle de l'adversaire
                if($main->rang<$mainadv->rang){
                    //on élimine le joueur
                    Joueur::where("id_user", "=", $id)->update(["action" => "éliminé"]);
                    // il defausse sa carte
                    $plat=new Plateau();
                    $plat->idJoueur=$id;
                    $plat->idCarte=Possede::where("idJoueur", "=", $id)->first()->idCarte;
                    $plat->save();
                    //on supprime sa main
                    Possede::where("idJoueur", "=", $id)->delete();
                    //on crée la description
                    $d=new Desc();
                    $d->idPartie=$_SESSION["idsalle"];
                    $d->desc="<p><win>".$adv->pseudo." [".$mainadv->nomCarte."]</win> a éliminé <loose>".$_SESSION["profile"]["login"]."[".$main->nomCarte."]</loose> </p>";
                    $d->save();
                }else{
                    //on test si l'adversaire a une main inferieur a celle u joueur
                    if($main->rang>$mainadv->rang){
                        //on élimine le joueur
                        Joueur::where("id_user", "=", $idadv)->update(["action" => "éliminé"]);
                        // il defausse sa carte
                        $plat=new Plateau();
                        $plat->idJoueur=$idadv;
                        $plat->idCarte=Possede::where("idJoueur", "=", $idadv)->first()->idCarte;
                        $plat->save();
                        //on supprime sa main
                        Possede::where("idJoueur", "=", $idadv)->delete();
                        //on crée la description
                        $d=new Desc();
                        $d->idPartie=$_SESSION["idsalle"];
                        $d->desc="<p><win>".$_SESSION["profile"]["login"]." [".$main->nomCarte."]</win> a éliminé <loose>".$adv->pseudo." [".$mainadv->nomCarte."]</loose></p>";
                        $d->save();
                    }
                }
                break;
            case 2:
                //on récupère l'id du joueur
                $ida=$_POST["idadv"];
                $u=User::where("id","=",$ida)->first();
                //on crée la description
                $d=new Desc();
                $d->idPartie=$_SESSION["idsalle"];
                $d->desc="<p><win>".$_SESSION["profile"]["login"]."</win> a vu le jeu de <loose>$u->pseudo</loose></p>";
                $d->save();
                break;
            case 1:
                //on récupère l'id de l'adversaire
                $ida=$_POST["idJ"];
                //on récupère le user
                $u=User::where("id","=",$ida)->first();
                //on récupère sa main
                $main=Possede::where('idJoueur', '=',$ida)->first()->Carte()->first();
                //on recup notre main
                $mainCli=Possede::where('idJoueur', '=',$_SESSION["profile"]["userid"])->first()->Carte()->first();
                // on récupère les info de la carte choisi
                $rang=$_POST["rang"];
                //on test s'il le client a trouvé la carte de l'adversaire
                if($rang==$main->rang){
                    //on elimine l'adversaire
                    Joueur::where("id_user", "=", $ida)->update(["action" => "éliminé"]);
                    //il defausse sa carte
                    $plat=new Plateau();
                    $plat->idJoueur=$ida;
                    $plat->idCarte=$main->id;
                    $plat->save();
                    //on supprime ca main
                    Possede::where("idJoueur", "=", $ida)->delete();
                    //on créé la description
                    $d=new Desc();
                    $d->idPartie=$_SESSION["idsalle"];
                    $d->desc="<p><win>".$_SESSION["profile"]["login"]."[".$mainCli->nomCarte."]</win> a éliminé <loose>".$u->pseudo."[".$main->nomCarte."]</loose></p>";
                    $d->save();
                }
                break;
        }
        //on fait tourner
        $partie = Partie::where("id", "=", $_SESSION["idsalle"])->first();
        $j = Joueur::where('id_user', '=', $_SESSION["profile"]["userid"])->first();
        //on test si le joueur n'est pas éliminé suite a leffet appliqué
        if($j->action != "éliminé") {
            //s'il n'est pas éliminé on le fait attendre
            Joueur::where("id_user", "=", $j->id_user)->update(["action" => "attend"]);
        }
        $find=false;
        //on parcour les joueur tant qu'on a pas trouver un joueur non éliminé
        while(!$find) {
            $ordreLastJoueur = $j->ordre;
            //on test si l'ordre est inferieur au nombre de joueur
            if ($ordreLastJoueur < $partie->nbJoueur) {
                //si oui on l'augmente
                $ordre = $ordreLastJoueur + 1;
            } else {
                //sinon on recommence a 1
                $ordre = 1;
            }
            //on selectionne le joueur
            $j = Joueur::where(["id_partie" => $partie->id, "ordre" => $ordre])->first();
            //on test s'il est éliminé
            if ($j->action != "éliminé") {
                //s'il ne l'est pas c'est son tour
                Joueur::where("id_user", "=", $j->id_user)->update(["action" => "joue"]);
                $find = true;
            }
        }
        //on modifie la partie
        $partie->joue = $j->id_user;
        $partie->save();
        //est on réinisialise la variable de session
        $_SESSION["effet"]=0;
    }
    /*
     * pose un carte
     */
    function jouer($idC){
        //on récupère le nombre de carte du joueur
        $nbC=Possede::where("idJoueur","=",$_SESSION["profile"]["userid"])->count();
        //on test s'il a 2 carte
        if($nbC==2) {
            //on selection la carte que le joueur veut jouer
            $carte = Possede::where([['idCarte', '=', $idC], ['idJoueur', '=', $_SESSION["profile"]["userid"]]])->first();
            $c=$carte->Carte()->first();
            //on la place deavnt
            $p = new Plateau();
            $p->idCarte = $carte->idCarte;
            $p->idJoueur = $_SESSION["profile"]["userid"];
            $p->save();
            //on test si la carte posé n'est pas la countess
            if ($c->rang != 7) {
                //si oui on moddifie la variable de session
                $_SESSION["effet"] = $c->rang;
                // on modifie le satus du joueur
                Joueur::where("id_user", "=", $_SESSION["profile"]['userid'])->update(["action" => "effet"]);
            }else{
                $partie = Partie::where("id", "=", $_SESSION["idsalle"])->first();
                $j = Joueur::where('id_user', '=', $_SESSION["profile"]["userid"])->first();
                //on test si le joueur n'est pas éliminé suite a leffet appliqué
                if($j->action != "éliminé") {
                    //s'il n'est pas éliminé on le fait attendre
                    Joueur::where("id_user", "=", $j->id_user)->update(["action" => "attend"]);
                }
                $find=false;
                $_SESSION["p"]=0;
                //on parcour les joueur tant qu'on a pas trouver un joueur non éliminé
                while(!$find) {
                    $ordreLastJoueur = $j->ordre;
                    //on test si l'ordre est inferieur au nombre de joueur
                    if ($ordreLastJoueur < $partie->nbJoueur) {
                        //si oui on l'augmente
                        $ordre = $ordreLastJoueur + 1;
                    } else {
                        //sinon on recommence a 1
                        $ordre = 1;
                    }
                    //on selectionne le joueur
                    $j = Joueur::where(["id_partie" => $partie->id, "ordre" => $ordre])->first();
                    //on test s'il est éliminé
                    if ($j->action != "éliminé") {
                        //s'il ne l'est pas c'est son tour
                        Joueur::where("id_user", "=", $j->id_user)->update(["action" => "joue"]);
                        $find = true;
                    }
                }
                //on modifie la partie
                $partie->joue = $j->id_user;
                $partie->save();
                //est on réinisialise la variable de session
                $_SESSION["effet"]=0;
            }
            //supp la carte de la de sa main
            Possede::where([['idCarte', '=', $idC], ['idJoueur', '=', $_SESSION["profile"]["userid"]]])->delete();
            //on crée la description
            $d=new Desc();
            $d->idPartie=$_SESSION["idsalle"];
            $d->desc="<p><strong>".$_SESSION["profile"]["login"]." </strong> a joué <strong>".$c->nomCarte."</strong> </p>";
            $d->save();
        }
    }

    /*
     * supprime le jeu
     */
    function suppJeu($id){
        //on cherhce s'ils existent des pioche
        $pio=Pioche::where("idPartie",'=',$id)->get();
        if($pio->count()>0) {
            //on parcour chaque pioche
            foreach ($pio as $p) {
                //on supp la pioche
                Defausse::where("idPioche","=","$p->idPioche")->delete();
                Contient::where("idPioche", "=", $p->idPioche)->delete();
                $p->delete();
            }
        }
        //on recupère les joueurs
        $js=Joueur::where("id_partie","=",$id)->get();
        foreach ($js as $j){
            //on supprime leur main et leur plateau
            $this->suppMainEtPlat($j->id_user);
        }
    }

    /*
     * on supp la main et le plateau d'un joueur passé en parametre
     */
    function suppMainEtPlat($id){
        Possede::where('idJoueur','=',$id)->delete();
        Plateau::where('idJoueur','=',$id)->delete();
    }
    /*
     * on supp la main d'un joueur passé en parametre
    */
    function suppMain($id){
        Possede::where('idJoueur','=',$id)->delete();
    }

    /*
    * on sup le plateau d'un joueur passé en parametre
    */
    function suppPlat($id){
        Plateau::where('idJoueur','=',$id)->delete();
    }

    /*
     * on initialise la pioche
     */
    function creerPioche($id){
        //crée une pioche
        $pioche=new Pioche();
        $pioche->idPartie=$id;
        $pioche->save();

        //on recupère toutes les cartes
        $cs=Carte::all();

        //on les ajoute une à une
        foreach ($cs as $c){
            $con=new Contient();
            $con->idPioche=$pioche->idPioche;
            $con->idCarte=$c->id;
            $con->save();
        }
        //on retourne la pioche
        return $pioche;
    }

    /*
     * on fait la premiere distribution
     */
    function premiereDistribution($id,$pioche){
        //on recupere la partie
        $partie=Partie::where("id","=",$id)->first();
        //on recupere les joueurs
        $user=$partie->joueurs()->get();//->toArray();
        //on tire 3 cartes
        for($i=0;$i<3;$i++){
            //on recupère la nouvelle pioche
            $carte=$pioche->cartes()->get()->toArray();
            $nbC=sizeof($carte);
            //on tire une carte a hasard
            $numC=(int) rand ( 0 ,$nbC-1 );
            $c=$carte[$numC];
            //on place la carte sur le plateau
            $p=new Defausse();
            $p->idCarte=$c['id'];
            $p->idPioche=$pioche->idPioche;
            $p->save();
            //supp la carte de la pioche
            Contient::where([["idCarte","=",$c['id']],["idPioche","=",$pioche->idPioche]])->delete();
        }
        //on distribue les carte au joueur
        foreach ($user as $u){
            //on recupère les cartes
            $carte=$pioche->cartes()->get()->toArray();
            $nbC=sizeof($carte);
            //echo $nbC."<br>";
            //on pioche au hasard
            $numC=(int) rand ( 0 ,$nbC-1 );
            $c=$carte[$numC];

            //ajout main
            $p=new Possede();
            $p->idCarte=$c['id'];
            $p->idJoueur=$u->id;
            $p->save();

            //supp la carte de la pioche
            Contient::where([["idCarte","=",$c['id']],["idPioche","=",$pioche->idPioche]])->delete();
        }
    }

    /*
     * load les donné de jeu
     */
    function load(){
        //on initialise les variable du json
        $redi="";
        $jeux = Array();
        $status="";
        //on recupère la partie
        $partie=Partie::where("id",'=',$_SESSION["idsalle"])->first();
        //on teste si la partie na toujour pas de gagnant
        if($partie->idgagnant==0) {
            //on test s'il y a aucun joueur qui est entrain d'appliquer un effet
            if(Joueur::Where(["id_partie"=>$_SESSION["idsalle"],"action"=>"effet"])->get()->count()==0){
                //on teste si la manche est fini
                $status = $this->finMAnche($partie);
            }
            //on initialise les variable de la case jeux
            $main = Array();
            $plateau = Array();
            //on recupère les donné du joueur
            $j = Joueur::where("id_user", "=", $_SESSION["profile"]["userid"])->first();
            $score = $j->score;
            //on recupère sa main
            $pos = Possede::where("idJoueur", "=", $_SESSION["profile"]["userid"])->get();
            $z = 0;
            foreach ($pos as $p) {
                $main[$z] = Carte::where("id", "=", $p->idCarte)->first();
                $z++;
            }
            //on récupère son plateau
            $plat = Plateau::where("idJoueur", "=", $_SESSION["profile"]["userid"])->orderby('date')->get();
            $z = 0;
            foreach ($plat as $p) {
                $plateau[$z] = Carte::where("id", "=", $p->idCarte)->first();
                $z++;
            }
            //on récupère le user
            $u = User::where("id", "=", $_SESSION["profile"]["userid"])->first();
            //on ajoute le joueur au debut
            $jeux[0] = array("action"=>$j->action,"user" => $u, "main" => $main, "score" => $score, "nbCarte" => $pos->count(), "plateau" => $plateau, "size" => $z--);
            //on recupère les adversaire
            $i = 1;
            $joueur = Joueur::where([["id_partie", "=", $_SESSION["idsalle"]], ["id_user", "<>", $_SESSION["profile"]["userid"]]])->get();
            foreach ($joueur as $js) {
                $score = $js->score;
                //on recupère sa main
                $pos = Possede::where("idJoueur", "=", $js->id_user)->get();
                $z = 0;
                foreach ($pos as $p) {
                    $main[$z] = Carte::where("id", "=", $p->idCarte)->first();
                    $z++;
                }
                //on récupère son plateau
                $plat = Plateau::where("idJoueur", "=", $js->id_user)->orderby('date')->get();
                $z = 0;
                foreach ($plat as $p) {
                    $plateau[$z] = Carte::where("id", "=", $p->idCarte)->first();

                    $z++;
                }
                $u = User::where("id", "=", $js->id_user)->first();
                //on l'ajoute au tableau
                $jeux[$i] = array("action"=>$js->action,"user" => $u, "main" => $main, "score" => $score, "nbCarte" => $pos->count(), "plateau" => $plateau, "size" => $z--);
                $i++;
            }
            //on recupère la pioche
            $p = Pioche::where("idPartie", "=", $_SESSION["idsalle"])->first();
            //on récupère la deffause
            $defausse=Array();
            $d=Defausse::where("idPioche","=",$p->idPioche)->get();
            $i=0;
            //on parour les carte
            foreach ($d as $card){
                $card=$card->carte()->first();
                $defausse[$i]=$card;
                $i++;
            }
            //on récupère le nombre de carte
            $nbc=$p->cartes()->count();
        }else{
            //la partie est fini
            $status="terminer";
        }
        if($status=="terminer"){
            //on initialise la redirection
            $app=\Slim\Slim::getInstance();
            $defausse=0;
            $redi=$app->urlFor("rangerJeu",["id"=>$_SESSION["idsalle"]]);
            $nbc = 0;
        }
        //on envoie le json
        $ef=$_SESSION["effet"];
        //on récuère la description
        $hist=Desc::where("idPartie","=",$_SESSION["idsalle"])->get();
        echo json_encode(array("historique"=>$hist,"carte"=>Carte::take(7)->get(),"effet"=>$ef,"joueur" => $jeux,"defausse"=>$defausse,"tour"=>$partie->entrainDeJouer, "pioche" => $nbc, "status" => $status,"redirect"=>$redi));
    }

    /*
     * on test si c'est la fin de manche
     */
    function finMAnche($partie){
        //on initialise la variable a retourner
        $res="pasterminer";
        $idScorer=0;
        //on recupère la pioche
        $p=Pioche::where("idPartie","=",$_SESSION["idsalle"])->first();
        //on test si la pioche est vide
        $joueur = Joueur::where("id_partie","=",$partie->id);
        $jNonéliminer=$joueur->where("action","<>","éliminé");
        //on test s'il n'y a plus de carte ou qu'il reste plus que 1 joueur
        if (Contient::where("idPioche", '=', $p->idPioche)->count() == 0 || $jNonéliminer->get()->count()==1) {
            //on recupère les joueurs
            //on initialise j1 & j2
            $player = array();
            $i = 0;
            $uneCarte = true;
            $zeroCarte = false;
            //on recupère la main de chaque joueur
            foreach ($jNonéliminer->get() as $j) {
                $player[$i] = Possede::where("idJoueur", '=', $j->id_user)->get();
                //on teste si tous le monde n'a qu'une carte
                if ($player[$i]->count() != 1) {
                    $uneCarte = false;
                }
                $i++;
            }
            //on test si tous les joueur on atteint le status résultat
            if ($joueur->where("action", "=", "resultat")->get()->count() == $partie->nbMax) {
                $uneCarte = false;
                $zeroCarte = true;
            } else {
                //on test s'il y a au moins 1 joueur qui a atteint le status resultat
                if ($joueur->where("action", "=", "resultat")->get()->count() >= 1) {
                    $uneCarte = true;
                } else {
                    if ($uneCarte) {
                        //on test si personne n'a atteint le status resultat
                        if ($joueur->where("action", "=", "resultat")->get()->count() == 0 && $_SESSION["effet"] == 0) {
                            $js = $partie->players;
                            //on parcour les joueur
                            foreach ($js as $j) {
                                //on supprime la pioche
                                Contient::where("idPioche","=",$p->idPioche)->delete();
                                //on supprime le plateau
                                $this->suppPlat($j->id_user);
                                //on test s'il n'est pas éliminé
                                if($j->action !="éliminé") {
                                    //il deffause sa carte
                                    $carte = Possede::where('idJoueur', "=", $j->id_user)->first();
                                    $lastcard = new Plateau();
                                    $lastcard->idCarte = $carte->idCarte;
                                    $lastcard->idJoueur = $j->id_user;
                                    $lastcard->save();
                                    //on supprime la main
                                    $this->suppMain($j->id_user);
                                }
                            }
                        }
                    }
                }
            }
            //on teste s'il leur reste q'une carte chacun
            if ($uneCarte) {
                //on place le joueur au status resultat
                Joueur::where("id_user", "=", $_SESSION["profile"]["userid"])->update(["action" => "resultat"]);
                $res = "resultat";
            } else {
                if ($zeroCarte) {
                    $i = 0;
                    $idScorer = 0;
                    $max = 0;
                    //on recupère leur carte des joueur non éliminé et on teste qui a la plus élevé
                    foreach ($jNonéliminer->get() as $j) {
                        $player[$i] = Plateau::where("idJoueur", '=', $j->id_user)->first();
                        if($player[$i]!=null) {
                            $c1 = $player[$i]->Carte()->first();
                            //on teste si la cart est la plus grande
                            if ($c1->rang > $max) {
                                $idScorer = $i;
                                $max = $c1->rang;
                            }
                        }
                        $i++;
                    }
                    //on modifie le score du joueur
                    $j = Joueur::where(['id_user' => $player[$idScorer]->idJoueur])->first();
                    Joueur::where(['id_user' => $player[$idScorer]->idJoueur, 'id_partie' => $_SESSION["idsalle"]])->update(["score" => $j->score + 1]);
                    //on regrade si le joueur a gagner
                    $res = $this->gagnant($j->id_user,$partie);
                    // echo $res;
                    //on test si la partie est fini
                    if ($res != "terminer") {
                        //se elle n'est pas fini on redistribue
                        $res = "score";
                        //on fait attendre tous le monde
                        Joueur::where("id_partie", "=", $partie->id)->update(["action" => "attend"]);
                        //on fait jouer celui a qui c'est le tour
                        Joueur::where("id_user", "=", $partie->joue)->update(["action" => "joue"]);
                        //on supprime l'historique de la manche
                        Desc::where("idPartie","=",$_SESSION["idsalle"])->delete();
                        //on distribue
                        $this->distribue($_SESSION["idsalle"]);
                    }
                } else {
                    $res = "derniertour";
                }
            }
        }
        return $res;

    }

    /*
    * on test s'il y a un gagnant
    */
    function gagnant($id,$p){
        //on initialise la variable a retourner
        $res="score";
        //on recupère le joueur qui a scoré
        $j=Joueur::where("id_user","=",$id)->first();
        //par defaut on met le scoremax a 7
        $score=7;
        //on test s'il y a 3 joueur
        if($p->nbMax==3){
            //on met le scoremax a 5
            $score=5;
        }else{
            //on test s'il y a 4 joueur
            if($p->nbMax==4) {
                //on met le score max a4
                $score = 4;
            }
        }
        //on test s'il a atteint le nombre de point requis
        if($j->score>=$score){
            $res="terminer";
            //on modifie la partie car elle est fini
            Partie::where("id","=",$_SESSION["idsalle"])->update(["idgagnant"=>$id]);
        }
        return $res;
    }

    /*
     * on supprime toutes les donné liée au jeux
     */
    function rangerJeu($id){
        $app=\Slim\Slim::getInstance();
        //on suprimme les donné du joueur et la pioche
        $p=Pioche::where("idPartie","=",$id)->first();
        $part=Partie::where("id","=",$id)->first();
        $user=User::where("id","=",$_SESSION["profile"]["userid"])->first();
        $winner=$part->winner()->first();
        //on regarde si l'id du joueur est celui du gagnant
        if($winner->id==$user->id){
            //si oui on augmente c'est victoire
            $user->partieGagner++;
            $user->save();
        }else{
            //sinon c'est défaite
            $user->PartiePerdu++;
            $user->save();
        }
        Defausse::where("idPioche","=",$p->idPioche)->delete();
        Joueur::where([["id_partie","=",$id],["id_user","=",$_SESSION["profile"]["userid"]]])->delete();
        Possede::where("idJoueur","=",$_SESSION["profile"]["userid"])->delete();
        Plateau::where("idJoueur","=",$_SESSION["profile"]["userid"])->delete();
        //on supprime l'historique de la manche
        Desc::where("idPartie","=",$_SESSION["idsalle"])->delete();
        //on supprime la variable de session
        unset($_SESSION['idsalle']);
        //on le renvoi à l'accueil
        $app->redirect($app->urlFor("finPartie",["id"=>$id]));
    }
    function finPartie($id){
        //on reecupère la partie
        $partie=Partie::where("id","=",$id)->first();
        //on recupère le gagnant
        $winner=$partie->winner;
        $vue=new VueJeu(["partie"=>$partie,"winner"=>$winner,"joueur"=>$_SESSION["profile"]["userid"]]);
        //on va afficher les resultat de la partie et les renvoier a l'accueil
        echo $vue->render(1);
    }
}