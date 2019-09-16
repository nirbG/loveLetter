var Form={
    module:{},
};

Form.module.submit=(function(){
    return{
        start : function(){
            var var1=Form.module.name.init($("#name"));
            var var2=Form.module.email.init($("#email"));
            var var3=Form.module.date.init($("#date"));
            if(var1 && var2 && var3){
                console.log("ok");
                res=true;
            }else{
                console.log("erreur");
                res=false;
            }
            return res;
        },
        surligne :function(champ, erreur){
            if(erreur) {
                champ.css("backgroundColor", "red");
            }else {
                champ.css("backgroundColor", "white");
            }
        }
    };
})();

Form.module.email=(function () {
    return{
        init : function (email) {
            console.log(email.val());
            var regex = /^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/;
            if(!regex.test(email.val()))
            {
                Form.module.submit.surligne(email, true);
                Form.module.erreur.show(email);
                return false;
            }
            else
            {
                Form.module.submit.surligne(email, false);
                Form.module.erreur.hide(email);
                return true;
            }
        }
    }
})();
Form.module.name=(function () {
    return{
        init : function (name) {
            var res=true;
            if(name.val()==""){
                Form.module.submit.surligne(name, true);
                Form.module.erreur.show(name);
                res=false;
            }else{
                Form.module.erreur.hide(name);
                Form.module.submit.surligne(name, false);
            }
            return res;
        }
    }
})();
Form.module.date=(function () {
    return{
        init : function (date) {
            res=false;
            var s=date.val().split("/");
            if(s.length!=0){
                if(s.length==3){
                    var d=new Date(s[2],parseInt(s[1])-1,s[0]);
                    if(d.getDate()==s[0]){
                        if(d.getMonth()==parseInt(s[1])-1){
                            if(d.getFullYear()==s[2]){
                                Form.module.erreur.hide(date);
                                Form.module.submit.surligne(date, false);
                                res=true;
                            }else{
                                Form.module.erreur.show(date);
                                Form.module.submit.surligne(date, true);
                            }
                        }else{
                            Form.module.erreur.show(date);
                            Form.module.submit.surligne(date, true);
                        }
                    }else {
                        Form.module.erreur.show(date);
                        Form.module.submit.surligne(date, true);
                    }
                }else{
                    Form.module.erreur.show(date);
                    Form.module.submit.surligne(date, true);
                }
            }else{
                Form.module.erreur.show(date);
                Form.module.submit.surligne(date, true);

            }
            return res;
        }
    }
})();
Form.module.erreur=(function () {
    return{
        show : function (champ) {
            console.log($(champ.parent()).find("p"));
            $(champ.parent()).find("p").show();
        },
        hide : function (champ) {
            console.log($(champ.parent()));
            champ.parent().find("p").hide();
        }
    }
})();

window.addEventListener("load",function () {
    $("#email").blur(function (e) {
        Form.module.email.init($("#email"));
    });
    $("#name").blur(function (e) {
        Form.module.name.init($("#name"));
    });
    $("#date").blur(function (e) {
        Form.module.date.init($("#date"));
    });


});
