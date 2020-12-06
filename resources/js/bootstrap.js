window.axios = require("axios");
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

import Echo from 'laravel-echo'
window.Pusher = require('pusher-js')
window.Echo = new Echo({
   broadcaster: 'pusher',
   key: 'ea1780cfd1557c881957',
   cluster: 'eu',
   encrypted: false
});
window.Noty = require('noty');

window.isUpdateAvailable = new Promise(function(resolve, reject) {
    if ("serviceWorker" in navigator) {
        window.addEventListener("load", function() {
            navigator.serviceWorker
                .register("/service-worker.js")
                .then(reg => {
                    reg.onupdatefound = () => {
                        const installingWorker = reg.installing;
                        installingWorker.onstatechange = () => {
                            switch (installingWorker.state) {
                                case "installed":
                                    if (navigator.serviceWorker.controller) {
                                        resolve(true);
                                    } else {
                                        resolve(false);
                                    }
                                    break;
                            }
                        };
                    };
                })
                .catch(err => window.console.error("[SW ERROR]", err));
        });
    }
});
