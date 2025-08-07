@extends('layouts.back-end.app-seller')

@section('title', translate('chatting_Page'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/support-ticket.png') }}" alt="">
                {{ translate('chatting_List') }}
            </h2>
        </div>

        <div class="row">

            <div class="col-xl-3 col-lg-4 chatSel">
                <div class="card card-body px-0 h-100">
                    <div class="inbox_people">
                        <form class="search-form mb-4 px-20" id="chat-search-form">
                            <div class="search-input-group">
                                <i class="tio-search search-icon" aria-hidden="true"></i>
                                <input id="myInput" type="text" aria-label="Search customers..."
                                    class="overflow-hidden"
                                    placeholder="{{ request('type') == 'customer' ? translate('search_customers') : translate('search_delivery_men') }}...">
                            </div>
                        </form>
                        <ul class="nav nav-tabs gap-3 mb-3 mx-4" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link bg-transparent p-2 {{ request('type') == 'customer' ? 'active' : '' }}"
                                    href="{{ route('vendor.messages.index', ['type' => 'customer']) }}">
                                    {{ translate('customer') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link bg-transparent p-2 {{ request('type') == 'delivery-man' ? 'active' : '' }}"
                                    href="{{ route('vendor.messages.index', ['type' => 'delivery-man']) }}">
                                    {{ translate('delivery_Man') }}
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="customers" role="tabpanel"
                                aria-labelledby="pills-home-tab">
                                <div class="inbox_chat d-flex flex-column" id="chat_users">
                                    @include('vendor-views.chatting.chat_users')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="col-xl-9 col-lg-8 mt-4 mt-lg-0">
                <div class="card card-body card-chat justify-content-center Chat">

                    @include('vendor-views.chatting.list_user_message')

                </div>
            </section>

        </div>
        <span id="chatting-post-url"
            data-url="{{ Request::is('vendor/messages/index/customer') ? route('vendor.messages.message') . '?user_id=' : route('vendor.messages.message') . '?delivery_man_id=' }}"></span>
        <span id="image-url" data-url="{{ dynamicStorage(path: 'storage/app/public/chatting/') }}"></span>
    </div>
@endsection

@push('script')
    <!-- Firebase App (core) -->
    <!-- Firebase SDKs -->
    <script src="https://www.gstatic.com/firebasejs/10.5.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.5.0/firebase-messaging-compat.js"></script>

    <!-- Your Chat JS (if required for triggering refresh) -->
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor/chatting.js') }}"></script>

    <script>
        // Initialize Firebase
        const firebaseConfig = {
            apiKey: "AIzaSyAExY995mKyNywHSY6QyJil2hHP0-cmXoQ",
            authDomain: "enepalshop-e4dfe.firebaseapp.com",
            projectId: "enepalshop-e4dfe",
            storageBucket: "enepalshop-e4dfe.firebasestorage.app",
            messagingSenderId: "293350781632",
            appId: "1:293350781632:web:27adb086f566dead1c7325",
            measurementId: "G-XG435EJYQ1"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // Ask user for permission and get token
        function initFirebaseMessagingRegistration() {
            if (Notification.permission === "granted") {
                getFcmToken();
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(function(permission) {
                    if (permission === "granted") {
                        getFcmToken();
                    } else {
                        console.warn("Notification permission not granted");
                    }
                });
            } else {
                console.warn("Notification permission previously denied");
            }
        }

        function getFcmToken() {
            messaging.getToken({
                vapidKey: "BPmDxlZdN0DqFX2xMIKXBi1DjoA_fyvv9ECmJFr-CVuOcSjEN0L_Siz1PM-Wpczw7vYNS2L5aSYD2wBUQGfZWvo"
            }).then(function(token) {
                if (token) {
                    console.log("FCM Token:", token);
                    // Send token to server
                    $.ajax({
                        url: "/vendor/auth/registration/fcm-token",
                        method: "POST",
                        data: {
                            _token : "{{ csrf_token() }}", 
                            token: token 
                        },
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function() {
                            console.log("Token stored on server");
                        },
                        error: function(err) {
                            console.error("Token storage failed:", err);
                        }
                    });
                } else {
                    console.warn("No token received");
                }
            }).catch(function(err) {
                console.error("Error retrieving token:", err);
            });
        }

        // Foreground message listener
        messaging.onMessage(function(payload) {
            console.log("[Foreground] Message received:", payload);

            const userId = payload?.data?.user_id;

            if (userId) {
                // Trigger your chat UI refresh
                $('.get-ajax-message-view[data-user-id="' + userId + '"]').trigger('click');
            }

            // Optional: Show browser notification
            const { title, body, icon } = payload.notification || {};
            if (title && body) {
                new Notification(title, {
                    body: body,
                    icon: icon || '/default-icon.png'
                });
            }
        });

        // Register service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then(function(registration) {
                    console.log("Service Worker registered:", registration.scope);
                    // Attach service worker to Firebase
                })
                .catch(function(err) {
                    console.error("Service Worker registration failed:", err);
                });
        }

        // Initialize when document is ready
        $(document).ready(function () {
            initFirebaseMessagingRegistration();
        });
    </script>

@endpush
