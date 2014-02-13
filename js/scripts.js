var app = {

    init: function() {
        FB.Canvas.setAutoGrow();
        app.bindUIActions();
    },

    bindUIActions: function() {
        // watch for the like button being pressed, refresh page
        FB.Event.subscribe('edge.create', app.reloadWindow());
    },

    reloadWindow: function() {
        setTimeout(function() {
            try {
                window.location.reload();
            } catch(er) {}
        }, 100);
    },

    share: function (image) {
        var obj = {
            method: "feed",
            link: vars.canvasUrl,
            picture: image,
            description: vars.description,
            // debug fb errors
            show_error: true
        };
        FB.ui(obj);
    }
};

// Load FB SDK asynchronously
(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));

window.fbAsyncInit = function() {
    // init FB JS SDK
    FB.init({
        appId      : vars.appId,
        // Channel File for x-domain communication
        channelUrl : vars.channelUrl,
        // check the login status upon init?
        status     : true,
        // set sessions cookies to allow your server to access the session?
        cookie     : true,
        // parse XFBML tags on this page?
        xfbml      : true
    });
    app.init();
};
