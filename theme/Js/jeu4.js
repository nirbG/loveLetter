/**
 * Created by quent on 22/11/2017.
 */
jeu = {
    start : function () {
        $.ajax({
            type : 'POST',
            url : 'jeu/start',
            dataType:'json'
        }).done(function (data) {
            console.log(data);
            if (data.start==1){
                console.log("init");
                load.module.load.refresh();
            }else {
                $("#search").remove();
                var bar="<div id='search' class='row center col-lg-offset-6 col-lg-4'><div class=' progress progress-striped'><div class='progress-bar progress-bar-warning'></div></div></div>";
                $("#partie").append(bar);
                var j=" joueurs."
                if(data.nbJoueurRestant==1){
                    j=" joueur."
                }
                $("#search").append($("<h4 class='msgS'>").text("en attente de "+data.nbJoueurRestant+j));
                console.log("relancer la requete");
                jeu.timer(0);
                jeu.start();
            }
        })
    },
    timer :function (n) {
        $(".progress-bar").css("width", n + "%");
        if(n < 100) {
            setTimeout(function() {
                jeu.timer(n + 25);
            }, 1000);
        }
    }
}

jeu.start();

var load={
    module:{},
};

load.module.load=(function() {
    return{
        refresh : function () {
            load.module.load.start();
        },
        start : function () {
            var id=$("#salle").attr("data");
            var pr=$.ajax('salle'+id+'/load',
                {type : 'GET',
                    dataType: "json",
                    context : this,
                    xhrFields:{withCredentials:true}
                });
            pr.done(function (d,s,jqXHR) {
                $('#partie>.row').remove();
                $('#adversaire').remove();
                console.log(d);

                if(d.status!="terminer") {
                    var div="<div id='adversaire' class=' col-sm-10 col-sm-offset-1'></div>";
                    $("#partie").append(div);
                    for (var z = 1; z < d.joueur.length; z++) {
                        var plateauadv = "<div id='plateauadv" + z + "' class=' col-sm-4 '></div>";
                        $("#adversaire").append(plateauadv);
                        var carteAdv = "Pioche.png";
                        var jeux = d.joueur[z];
                        var jeux = d.joueur[z];
                        var user = jeux.user;
                        var point = jeux.score;
                        var score = "<div class=" + "'col-sm-4'><h3>Score:" + point + "</h3><p>" + user.pseudo + "</p></div>";
                        var cont = "";
                        var carte = "";
                        if (d.joueur[z].action == "resultat" || d.joueur[z].action == "éliminé") {
                            carteAdv = "vide.png";
                        }
                        if (jeux.nbCarte == 2) {
                            for (var i = 0; i < jeux.main.length; i++) {
                                carte += "<div class='col-sm-3 '><img src='theme/ressource/cartes/Pioche.png'></div>";
                            }
                            cont += "<div id='mainAdv' class='row'>" + score + " " + carte + "</div>";
                        } else {
                            carte += "<div class='col-sm-3 '><img src='theme/ressource/cartes/" + carteAdv + "'></div>";
                            cont += "<div id='main' class='row'>" + score + " " + carte + "</div>";
                        }
                        $("#plateauadv" + z).append(cont);
                        cont = "<div id='plateauAdv' class='row carte'>";
                        console.log(jeux.plateau.length);
                        if (jeux.size != 0) {
                            for (i = 0; i < jeux.size; i++) {
                                cont += "<div class='col-sm-2 '><img data-img='theme/ressource/cartes/" + jeux.plateau[i].image + "' src='theme/ressource/cartes/" + jeux.plateau[i].image + "'> </div>";
                            }
                        } else {
                            cont += "<div class='col-sm-2 center '><img src='theme/ressource/cartes/vide.png'></div>";
                        }
                        cont += "</div>";
                        $("#plateauadv" + z).append(cont);
                    }
                    pio="";
                    var nbC = d.pioche;
                    var def = "";
                    var joue = d.tour.pseudo;
                    var tour = "<div  class='col-lg-1 col-lg-offset-3'><h5>C'est au tour de <strong>" + joue + "</strong></h5></div>";
                    for (i = 0; i < d.defausse.length; i++) {
                        def += "<div class='col-lg-1 carte '><img data-img='theme/ressource/cartes/" + d.defausse[i].image + "' src='theme/ressource/cartes/" + d.defausse[i].image + "'></div>";
                    }
                    if (nbC == 0) {
                        pio += "<div id='pioche' class='row'>" + tour + "<div id='pioche' class='col-lg-1'><img src='theme/ressource/cartes/vide.png'></div>" + def + "</div>";
                    } else {
                        pio += "<div id='pioche' class='row'>" + tour + "<div id='pioche' class='col-lg-1'><img src='theme/ressource/cartes/Pioche.png'></div>" + def + "</div>";
                    }
                    jeux = d.joueur[0];
                    $("#partie").append(pio);
                    pme = "<div id='plateau' class='row carte'>";
                    console.log(jeux.plateau.length);
                    if (jeux.size != 0) {
                        for (i = 0; i < jeux.size; i++) {
                            pme += "<div class='col-sm-1 '><img data-img='theme/ressource/cartes/" + jeux.plateau[i].image + "' src='theme/ressource/cartes/" + jeux.plateau[i].image + "'> </div>";
                        }
                    } else {
                        pme += "<div class='col-sm-1 center '><img src='theme/ressource/cartes/vide.png'></div>";
                    }
                    $("#partie").append(pme);
                    user = jeux.user;
                    point = jeux.score;
                    score = "<div class=" + "'col-sm-1 '><h3>Score:" + point + "</h3><p>" + user.pseudo + "</p></div>";
                    carte = "";
                    me="";
                    if (jeux.nbCarte == 2) {
                        for (i = 0; i < jeux.main.length; i++) {
                            var res = "";
                            if (i == 0) {
                                res = "col-sm-offset-5";
                            }
                            carte += "<div class='col-sm-1 " + res + "'><img idc='" + jeux.main[i].id + "' data-img='theme/ressource/cartes/" + jeux.main[i].image + "' src='theme/ressource/cartes/" + jeux.main[i].image + "'></div>";
                        }

                    } else {
                        var carteid;
                        var carteimg;
                        if(d.joueur[0].action=="resultat" || d.joueur[0].action=="éliminé"){
                            carteimg="vide.png";
                            carteid="";
                        }else{
                            carteid=jeux.main[0].id;
                            carteimg=jeux.main[0].image;
                        }
                        carte += "<div class='col-sm-1 col-sm-offset-6'><img idc='" + carteid + "' data-img='theme/ressource/cartes/" + carteimg + "' src='theme/ressource/cartes/" + carteimg + "'></div>";

                    }
                    me += "<div id='main' class='row carte'>" + carte + " " + score + "</div>";
                    $("#partie").append(me);
                    load.module.lightBox.start();
                    load.module.action.start();

                }else{
                    location.href=d.redirect;
                }
                if(d.effet!=0){
                    switch (d.effet){
                        case 1:
                            load.module.guard.effect(d);
                            break;
                        case 2:
                            load.module.priest.effect(d);
                            break;
                        case 3:
                            load.module.baron.effect(d);
                            break;
                        case 4:
                            load.module.handmaid.effect(d);
                            break;
                        case 5:
                            load.module.prince.effect(d);
                            break;
                        case 6:
                            load.module.king.effect(d);
                            break;
                        case 7:
                            load.module.countess.effect(d);
                            break;
                        case 8:
                            load.module.princess.effect(d);
                            break;
                    }
                }

                if(d.joueur[0].action !="joue"&& d.joueur[0].action !="effet") {
                    setTimeout(function(){
                        load.module.load.start();
                    }, 5000);
                }
            });
            pr.fail(function(jqXHR, status, error){
                console.log("error load :"+status+" "+error );
            });
        }

    }

})();

load.module.guard=(function() {
    return {effect: function (data) {
        var $body = $('body');
        $thumbnail = $(this);
        var $blackout = $("<div class='blackout'>").css("display", "none");
        var back="<div style='	background-color: black; position:absolute; z-index:-1; top:0; left:0; right:0; bottom:0; opacity:0.5;'></div>";
        var h4 = $("<h1>").text("click sur l'utilisateur,que tu veux eliminer").css("margin", "0");
        var user ="<div id='users' class='row'>";
        for(var i=1;i<data.joueur.length;i++){
            var u=data.joueur[i].user;
            if(data.joueur[i].action!="éliminé") {
                user += "<div class='user col-sm-6' data='" + u.id + "'><img src='theme/ressource/user.png'><h4>" + u.pseudo + "</h4></div>";
            }
        }
        user+="</div>";
        $blackout.append(back);
        $blackout.append(h4);
        $blackout.append(user);
        // Ce block ne s'execute pas maintenant, mais au prochain click sur notre "blackout". Il se lit comme suit :
        // Au clic sur le fond...
        // On ajoute notre lightbox au body.
        $body.append($blackout);
        //Et enfin nous la faisons apparaitre progressivement.
        $blackout.fadeIn();
        $(".blackout .user").click(function () {
            var id =$(this).attr('data');

            $(".blackout").remove();
            var $body = $('body');
            $thumbnail = $(this);
            var $blackout = $("<div class='blackout'>").css("display", "none");
            var back = "<div style='	background-color: black; position:absolute; z-index:-1; top:0; left:0; right:0; bottom:0; opacity:0.5;'></div>";
            var h1 = $("<h1>").text("essaye de deviner qu'elle carte il a en main").css("margin", "0");
            var user ="<div id='guard' class='row'>";
            for (var i = 0; i < data.carte.length; i++) {
                var c = data.carte[i];
                user += "<div class='crte col-sm-3' data='" + c.rang + "'><img src='theme/ressource/cartes/"+c.image+"'></div>";
            }
            user += "</div>";
            $blackout.append(back);
            $blackout.append(h1);
            $blackout.append(user);
            // Ce block ne s'execute pas maintenant, mais au prochain click sur notre "blackout". Il se lit comme suit :
            // Au clic sur le fond...
            // On ajoute notre lightbox au body.
            $body.append($blackout);
            //Et enfin nous la faisons apparaitre progressivement.
            $blackout.fadeIn();
            $(".blackout .crte").click(function () {
                var r = $(this).attr('data');
                var datas = {rang : r ,idJ : id };
                var pr = $.ajax('salle/effect' + 1 + '',
                    {
                        type: 'POST',
                        //dataType: "json",
                        context: this,
                        xhrFields: {withCredentials: true},
                        data: datas
                    });
                pr.done(function (d, s, jqXHR) {
                    console.log("guard");
                    load.module.load.start();
                    $(".blackout").remove();
                });
                pr.fail(function (jqXHR, status, error) {
                    console.log("error play card :" + status + " " + error);
                });
            });

        });
    }
    }
})();
load.module.priest=(function() {
    return {
        effect: function () {
            if (confirm("Priest") == true) {
                var datas={id:0};
                var pr = $.ajax('salle/effect' + 2 + '',
                    {
                        type: 'POST',
                        //dataType: "json",
                        context: this,
                        xhrFields: {withCredentials: true},
                        data:datas
                    });
                pr.done(function (d, s, jqXHR) {
                    console.log("priest");
                    load.module.load.start();
                });
                pr.fail(function(jqXHR, status, error){
                    console.log("error play card :"+status+" "+error);
                });
            }
        }
    }
})();
load.module.baron=(function() {
    return {effect: function (data) {
        var $body = $('body');
        $thumbnail = $(this);
        var $blackout = $("<div class='blackout'>").css("display", "none");
        var back="<div style='	background-color: black; position:absolute; z-index:-1; top:0; left:0; right:0; bottom:0; opacity:0.5;'></div>";
        var h4 = $("<h1>").text("click sur l'utilisateur,que tu veux defier").css("margin", "0");
        var user ="<div id='users' class='row'>";
        for(var i=1;i<data.joueur.length;i++){
            var u=data.joueur[i].user;
            if(data.joueur[i].action!="éliminé") {
                user += "<div class='user col-sm-6' data='" + i + "'><img src='theme/ressource/user.png'><h4>" + u.pseudo + "</h4></div>";
            }
        }
        user+="</div>";
        $blackout.append(back);
        $blackout.append(h4);
        $blackout.append(user);
        // Ce block ne s'execute pas maintenant, mais au prochain click sur notre "blackout". Il se lit comme suit :
        // Au clic sur le fond...
        // On ajoute notre lightbox au body.
        $body.append($blackout);
        //Et enfin nous la faisons apparaitre progressivement.
        $blackout.fadeIn();
        $(".blackout .user").click(function () {
            var idus = $(this).attr('data');
            $(".blackout").remove();
            var $body = $('body');
            $thumbnail = $(this);
            var $blackout = $("<div class='blackout'>").css("display", "none");
            var user = "<div id='baron' class='row'>";
            var j1=data.enListe[idus];
            var j=data.joueur[0];
            var c = data.carte[i];
            var back = "<div style='	background-color: black; position:absolute; z-index:-1; top:0; left:0; right:0; bottom:0; opacity:0.5;'></div>";
            if (j.main[0].rang<j1.main[0].rang){
                var h4 = $("<h1>").text("tu es eliminé").css("margin", "0");
            }else{
                if (j.main[0].rang == j1.main[0].rang){
                    var h4 = $("<h1>").text("personne n'est éliminer :").css("margin", "0");
                }else {
                    var h4 = $("<h1>").text("tu as eliminé :" + j1.user.pseudo).css("margin", "0");
                }
            }
            user += "<div class='crte col-sm-6' '><img src='theme/ressource/cartes/"+j1.main[0].image+"'></div>";
            user += "<div class='crte col-sm-6' '><img src='theme/ressource/cartes/"+j.main[0].image+"'></div>";
            user += "</div>";
            $blackout.append(back);
            $blackout.append(h4);
            $blackout.append(user);
            // Ce block ne s'execute pas maintenant, mais au prochain click sur notre "blackout". Il se lit comme suit :
            // Au clic sur le fond...
            // On ajoute notre lightbox au body.
            $body.append($blackout);
            //Et enfin nous la faisons apparaitre progressivement.
            $blackout.fadeIn();
            $blackout.click(function () {
                var r = $(this).attr('data');
                var datas = {id: j.user.id,idadv :j1.user.id};
                var pr = $.ajax('salle/effect' + 3 + '',
                    {
                        type: 'POST',
                        //dataType: "json",
                        context: this,
                        xhrFields: {withCredentials: true},
                        data: datas
                    });
                pr.done(function (d, s, jqXHR) {
                    console.log("baron");
                    load.module.load.start();
                    $(".blackout").remove();
                });
                pr.fail(function (jqXHR, status, error) {
                    console.log("error play card :" + status + " " + error);
                });
            });

        });
    }
    }
})();
load.module.handmaid=(function() {
    return {
        effect: function () {
            if (confirm("Handmaid") == true) {
                var datas={id:0};
                var pr = $.ajax('salle/effect' + 4 + '',
                    {
                        type: 'POST',
                        //dataType: "json",
                        context: this,
                        xhrFields: {withCredentials: true},
                        data:datas
                    });
                pr.done(function (d, s, jqXHR) {
                    console.log("handmaid");
                    load.module.load.start();
                });
                pr.fail(function(jqXHR, status, error){
                    console.log("error play card :"+status+" "+error);
                });
            }
        }
    }
})();
load.module.prince=(function() {
    return {
        effect: function (data) {
            var $body = $('body');
            $thumbnail = $(this);
            var $blackout = $("<div class='blackout'>").css("display", "none");
            var back="<div style='	background-color: black; position:absolute; z-index:-1; top:0; left:0; right:0; bottom:0; opacity:0.5;'></div>";
            var h4 = $("<h1>").text("click sur l'utilisateur qui va defausser une carte puis en piocher une").css("margin", "0");
            var user = "<div id='users' class='row'>";
            for(var i=0;i<data.joueur.length;i++){
                var u=data.joueur[i].user;
                if(data.joueur[i].action!="éliminé") {
                    user += "<div class='user col-sm-6' data='" + u.id + "'><img src='theme/ressource/user.png'><h4>" + u.pseudo + "</h4></div>";
                }
            }
            user+="</div>";
            $blackout.append(back);
            $blackout.append(h4);
            $blackout.append(user);
            // Ce block ne s'execute pas maintenant, mais au prochain click sur notre "blackout". Il se lit comme suit :
            // Au clic sur le fond...
            // On ajoute notre lightbox au body.
            $body.append($blackout);
            //Et enfin nous la faisons apparaitre progressivement.
            $blackout.fadeIn();
            $(".blackout .user").click(function () {
                iddef=$(this).attr('data');
                var datas={id : iddef};
                var pr = $.ajax('salle/effect' + 5 + '',
                    {
                        type: 'POST',
                        //dataType: "json",
                        context: this,
                        xhrFields: {withCredentials: true},
                        data:datas
                    });
                pr.done(function (d, s, jqXHR) {
                    console.log("prince");
                    $( ".blackout" ).remove();
                    load.module.load.start();
                });
                pr.fail(function(jqXHR, status, error){
                    console.log("error play card :"+status+" "+error);
                });
            });

        }
    }
})();
load.module.king=(function() {
    return {
        effect: function () {
            if (confirm("King") == true) {
                var datas={id:0};
                var pr = $.ajax('salle/effect' + 6 + '',
                    {
                        type: 'POST',
                        //dataType: "json",
                        context: this,
                        xhrFields: {withCredentials: true},
                        data:datas
                    });
                pr.done(function (d, s, jqXHR) {
                    console.log("king");
                    load.module.load.start();
                });
                pr.fail(function(jqXHR, status, error){
                    console.log("error play card :"+status+" "+error);
                });
            }
        }
    }
})();
load.module.princess=(function() {
    return {
        effect: function () {
            if (confirm("Princess") == true) {
                var datas={id:0};
                var pr = $.ajax('salle/effect' + 8 + '',
                    {
                        type: 'POST',
                        //dataType: "json",
                        context: this,
                        xhrFields: {withCredentials: true},
                        data:datas
                    });
                pr.done(function (d, s, jqXHR) {
                    console.log("princess");
                    load.module.load.start();
                });
                pr.fail(function(jqXHR, status, error){
                    console.log("error play card :"+status+" "+error);
                });
            }
        }
    }
})();

load.module.action=(function() {
    return {
        start : function () {

            $("#main img").click(function (event) {

                if( event.which == 1) {
                    load.module.action.jouer($(this));
                    console.log("play");
                }
            });

            $("#pioche img").click(function (event) {
                if( event.which == 1) {
                    console.log("piocher");
                    load.module.action.piocher();
                }
            })
        },
        piocher : function () {
            var pr=$.ajax('salle/Piocher',
                {type : 'GET',
                    context : this,
                    xhrFields:{withCredentials:true}
                });
            pr.done(function (d,s,jqXHR) {
                console.log("done");
                load.module.load.start();
            });
            pr.fail(function(jqXHR, status, error){
                console.log("status :"+status+" erreur :"+error);
            });
        },
        jouer : function (carte) {
            console.log(carte.attr("idc"));
            var pr=$.ajax('salle/Jouer'+carte.attr("idc"),
                {type : 'GET',
                    context : this,
                    xhrFields:{withCredentials:true}
                });
            pr.done(function (d,s,jqXHR) {
                load.module.load.start();
            });
            pr.fail(function(jqXHR, status, error){
                console.log("error play card :"+status);
            });

        }
    }
})();

load.module.lightBox=(function () {
    return{
        start : function () {
            $(".carte img").on("contextmenu",function(event){
                console.log(event.which);
                if( event.which == 3) {
                    if ($(".blackout").length < 1) {
                        var $body = $('body');
                        $thumbnail = $(this);
                        var $blackout = $("<div class='blackout'>").css("display", "none");
                        var $img = $("<img>").attr("src", $thumbnail.data("img"));
                        var $h4 = $("<h4>").text("click sur la carte pour la reduire").css("margin", "0");
                        $blackout.append($h4);
                        $blackout.append($img);
                        // Ce block ne s'execute pas maintenant, mais au prochain click sur notre "blackout". Il se lit comme suit :
                        // Au clic sur le fond...
                        $img.click(function () {
                            // On fait disparaitre progressivement la lightbox...
                            $blackout.fadeOut(function () {
                                // Puis on la supprime.
                                $blackout.remove();
                            })
                        });
                        // On ajoute notre lightbox au body.
                        $body.append($blackout);
                        //Et enfin nous la faisons apparaitre progressivement.
                        $blackout.fadeIn();
                        // Ces trois petites lignes permettent de centrer l'image en hauteur
                        if ($img.height() < $blackout.height()) {
                            $img.css("marginTop", ($blackout.height() - $img.height()) / 2);
                        }
                    }
                }
                return false;
            })
        }
    }
})();


