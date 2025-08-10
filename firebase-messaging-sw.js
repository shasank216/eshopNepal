importScripts('https://www.gstatic.com/firebasejs/10.5.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.5.0/firebase-messaging-compat.js');

// Firebase config
firebase.initializeApp({
    apiKey: "AIzaSyAExY995mKyNywHSY6QyJil2hHP0-cmXoQ",
    authDomain: "enepalshop-e4dfe.firebaseapp.com",
    projectId: "enepalshop-e4dfe",
    storageBucket: "enepalshop-e4dfe.firebasestorage.app",
    messagingSenderId: "293350781632",
    appId: "1:293350781632:web:27adb086f566dead1c7325",
    measurementId: "G-XG435EJYQ1"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Background message received:', payload);

    const notificationTitle = payload.notification?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: payload.notification?.icon || '/default-icon.png',
        requireInteraction: true, // keep it visible until clicked
        data: {
            url: buildRedirectUrl(payload) // store final URL in data for click handler
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Helper to build the redirect URL with GET params from payload.data
function buildRedirectUrl(payload) {
    const baseUrl = "https://enepalshop.com/vendor/messages/index/customer";
    if (payload.data && Object.keys(payload.data).length > 0) {
        const queryString = new URLSearchParams(payload.data).toString();
        return `${baseUrl}?${queryString}`;
    }
    return baseUrl;
}

// Handle click on the notification
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const targetUrl = event.notification.data?.url || "https://enepalshop.com";
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            // If the page is already open, focus it
            for (let client of windowClients) {
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise, open a new window/tab
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
