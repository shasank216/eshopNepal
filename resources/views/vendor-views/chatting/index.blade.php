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
                            {{-- <li class="nav-item" role="presentation">
                                <a class="nav-link bg-transparent p-2 {{ request('type') == 'delivery-man' ? 'active' : '' }}"
                                    href="{{ route('vendor.messages.index', ['type' => 'delivery-man']) }}">
                                    {{ translate('delivery_Man') }}
                                </a>
                            </li> --}}
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
    
@endpush
