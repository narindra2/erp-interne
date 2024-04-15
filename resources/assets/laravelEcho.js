import Echo from 'laravel-echo';
var initLaravelEchoPusher = function () {
    
    window.Pusher = require('pusher-js');
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: process.env.MIX_PUSHER_APP_KEY,
        cluster: process.env.MIX_PUSHER_APP_CLUSTER,
        forceTLS: true,
        // Cette ligne est doit etre decommenter dans le serveur
        // authEndpoint: '/erp/public/broadcasting/auth'
    });
  
    console.log("initLaravelEchoPusher");
}
var listingLaravelEchoPusher = function () {
    window.Echo.private('App.Models.User.' + authUser.id)
        .notification((notification) => {
            handleNotification(notification)
        });
        console.log("listingLaravelEchoPusher");
}
initLaravelEchoPusher();
listingLaravelEchoPusher();

// initLaravelEchoRedis()
// listingLaravelEchoRedis();