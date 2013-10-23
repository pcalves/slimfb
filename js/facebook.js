// Load the FB SDK asynchronously
(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

window.fbAsyncInit = function() {
    // init FB JS SDK
    FB.init({
        appId      : '{{ config.facebook.appid }}',
        channelUrl : '{{ config.general.base_url }}/channel', // Channel File for x-domain communication
        status     : true, // check the login status upon init?
        cookie     : true, // set sessions cookies to allow your server to access the session?
        xfbml      : true  // parse XFBML tags on this page?
    });
    FB.Canvas.setAutoGrow();
    // watch for the like button being pressed, refresh page
    FB.Event.subscribe('edge.create', function(resp) {
        setTimeout(function() {
            try {
                window.location.reload();
            } catch(er) {}
        }, 100);
    });
};

// share app
function share(image) {
    var obj = {
        picture: image,
        method: 'feed',
        redirect_uri: '{{ config.facebook.canvas_page }}',
        link: '{{ config.facebook.canvas_page }}',
        description: '{{ config.metadata.description }}',
        // debug fb errors
        show_error: true
    };
    FB.ui(obj);
}
