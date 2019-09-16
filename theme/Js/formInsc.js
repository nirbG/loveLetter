var Form={
    module:{},
};

Form.module.submit=(function(){
    return{
        start : function(){
            var var1=Form.module.name.init($("#pseudo"));
            var var2=Form.module.email.init($("#email"));
            var var3=Form.module.mdp.init($("#confirm"),$("#password"));
            var var4=Form.module.name.init($("#password"));
            if(var1 && var2 && var3 && var4){
                console.log("ok");
                res=true;
            }else{
                console.log("erreur");
                res=false;
            }
            return res;
        },
    };
})();

Form.module.email=(function () {
    return{
        init : function (email) {
            var regex = /^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/;
            if(!regex.test(email.val()))
            {
                Form.module.erreur.show(email);
                $(email.parent()).addClass("has-error");
                return false;
            }
            else
            {
                Form.module.erreur.hide(email);
                $(email.parent()).removeClass("has-error");
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
                Form.module.erreur.show(name);
                $(name.parent()).addClass("has-error");
                res=false;
            }else{
                Form.module.erreur.hide(name);
                $(name.parent()).removeClass("has-error");
            }
            return res;
        }
    }
})();
Form.module.mdp=(function () {
    return{
        init : function (m2,m1) {
            var res=true;
            if(m1.val()!=m2.val()){
                Form.module.erreur.show(m2);
                $(m2.parent()).addClass("has-error");
                res=false;
            }else{
                Form.module.erreur.hide(m2);
                $(m2.parent()).removeClass("has-error");
            }
            return res;
        }
    }
})();
Form.module.erreur=(function () {
    return{
        show : function (champ) {
            $(champ.parent()).find("span").show();
        },
        hide : function (champ) {
            $(champ.parent()).find("span").hide();
        }
    }
})();

window.addEventListener("load",function () {
    Form.module.erreur.hide( $("#password"));
    Form.module.erreur.hide($("#email"));
    Form.module.erreur.hide($("#pseudo"));
    Form.module.erreur.hide($("#confirm"));
    $("#email").blur(function (e) {
        Form.module.email.init($("#email"));
    });
    $("#pseudo").blur(function (e) {
        Form.module.name.init($("#pseudo"));
    });
    $("#password").blur(function (e) {
        Form.module.name.init($("#password"));
    });
    $("#confirm").blur(function (e) {
        Form.module.mdp.init($("#confirm"),$("#password"));
    });
});
