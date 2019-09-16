
    setTimeout(function () {
        var pr = $.ajax('deconnexion',
            {
                type: 'GET',
                context: this,
                xhrFields: {withCredentials: true}
            });
        pr.done(function (d, s, jqXHR) {
            console.log("deco");
            location.href = "";
        });
        pr.fail(function (jqXHR, status, error) {
            console.log("error load :" + status + " " + error);
        });
    }, 600000);


