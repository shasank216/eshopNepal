@extends('layouts.front-end.app')

@section('title', auth('customer')->user()->f_name.' '.auth('customer')->user()->l_name)

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush

@section('content')
    <div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
        <div class="row">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9 __customer-profile px-0">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <h5 class="font-bold m-0 fs-16">{{ translate('profile_Info') }}</h5>
                            <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="..." fill="white"/>
                                </svg>
                            </button>

                            <div class="text-end d-none d-lg-block">
                                <div class="dropdown">
                                    <button class="btn border border-base px-2 py-1" type="button" data-toggle="dropdown">
                                        <i class="tio-more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu delete-account-dropdown-menu">
                                        <a class="dropdown-item call-route-alert" href="javascript:"
                                           data-route="{{ route('account-delete',[$customerDetail['id']]) }}"
                                           data-message="{{translate('want_to_delete_this_account')}}?">
                                            {{translate('delete_account')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner">
                            <form class="row mt-3 px-sm-2 pb-2" action="{{route('user-password-update')}}" method="post"
                                  id="profile_form" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password" class="mb-2 text-capitalize">{{ translate('new_password') }}</label>
                                        <div class="password-toggle">
                                            <input class="form-control" name="password" type="password"
                                                   placeholder="{{ translate('minimum_8_characters_long') }}" id="password">
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox">
                                                <i class="tio-hidden password-toggle-indicator"></i>
                                                <span class="sr-only">{{ translate('show_password') }}</span>
                                            </label>
                                        </div>
                                        <span class="text-danger mx-1 password-error"></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="confirm_password" class="mb-2 text-capitalize">{{ translate('confirm_password') }}</label>
                                        <div class="password-toggle">
                                            <input class="form-control" name="password_confirmation" type="password"
                                                   placeholder="{{ translate('minimum_8_characters_long') }}" id="confirm_password">
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox">
                                                <i class="tio-hidden password-toggle-indicator"></i>
                                                <span class="sr-only">{{ translate('show_password') }}</span>
                                            </label>
                                        </div>
                                        <div id='message' class="mt-1"></div>
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn--primary px-4 fs-14 font-semi-bold py-2">
                                        {{ translate('update') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="bottom-sticky_offset"></div>
    <div class="bottom-sticky_ele bg-white d-md-none p-3">
        <button type="submit" class="btn btn--primary w-100 update-account-info">
            {{translate('update')}}
        </button>
    </div>
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
@endpush
