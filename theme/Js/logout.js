/**
 * Created by User on 22/11/2017.
 */
var Logout={
    module:{},
};

 Logout.module.deco=(function(){

     return {
        start: function () {
            $("#Decobutton").click(function () {
                if (confirm("Voulez vous vraimment quitter?") == true) {
                    location=$(this).attr("data");
                } else {

                }
            });
        },
     };

})();
window.addEventListener("load",function () {
    Logout.module.deco.start();
});