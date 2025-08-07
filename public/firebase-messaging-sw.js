importScripts("https://www.gstatic.com/firebasejs/10.5.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.5.0/firebase-messaging-compat.js");

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
    const title = payload.notification.title;
    const options = {
        body: payload.notification.body,
        icon: payload.notification.icon
    };

    self.registration.showNotification(title, options);
});
