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

messaging.onBackgroundMessage(function (payload) {
    console.log('[firebase-messaging-sw.js] Background message received:', payload);

    const notificationTitle = payload.notification?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: payload.notification?.icon || '/default-icon.png',
        requireInteraction: true,
        data: {
            url: buildRedirectUrl(payload) // URL for click handler
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Build redirect URL based on payload.data.type
function buildRedirectUrl(payload) {
    const type = payload.data?.type;

    if (type === 'customer' && payload.data?.shop_id) {
        // User panel push → open chat with shop_id
        return `https://enepalshop.com/chat/seller?id=${payload.data.shop_id}`;
    }

    // Seller panel push → preserve old logic
    const baseUrl = "https://enepalshop.com/vendor/messages/index/customer";
    if (payload.data && Object.keys(payload.data).length > 0) {
        const queryString = new URLSearchParams(payload.data).toString();
        return `${baseUrl}?${queryString}`;
    }

    return baseUrl;
}

// Handle click on the notification
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const targetUrl = event.notification.data?.url || "https://enepalshop.com";

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            // If the target page is already open, focus it
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
