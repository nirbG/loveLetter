var Form={
    module:{},
};

Form.module.submit=(function(){
    return{
        start : function(){
            var var1=Form.module.name.init($("#nom"));
            if(var1 ){
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
    Form.module.erreur.hide( $("#nom"));

    $("#nom").blur(function (e) {
        Form.module.name.init($("#nom"));
    });

});
