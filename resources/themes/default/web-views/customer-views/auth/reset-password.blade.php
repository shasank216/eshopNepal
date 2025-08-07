@extends('layouts.front-end.app')

@section('title', translate('Reset_Password'))

@section('content')
    <div class="container py-4 py-lg-5 my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <h2 class="h3 mb-4">{{ translate('reset_your_password')}}</h2>
                <p class="font-size-md">{{ translate('change_your_password_in_two_easy_steps.')}} {{ translate('this_helps_to_keep_your_new_password_secure.')}}</p>
                <ol class="list-unstyled font-size-md">
                    <li><span class="text-primary mr-2">{{ translate('1')}}.</span>{{ translate('new_Password.')}}</li>
                    <li><span class="text-primary mr-2">{{ translate('2')}}.</span>{{ translate('confirm_Password.')}}
                    </li>
                </ol>
                <div class="card py-2 mt-4">
                    
                    @if(isset($otp) && $otp->phone)
                    <form class="card-body needs-validation" novalidate method="POST"
                          action="{{url('customer/auth/reset-password')}}">
                        @csrf
                        <div class="form-group d-none">
                            <input type="text" name="identify" value="{{$otp->phone}}" required>
                        </div>

                        <div class="form-group">
                            <label for="si-otp">{{ translate('Otp')}}</label>
                            <input class="form-control" name="otp" type="text" id="si-otp" required
                                   placeholder="{{ translate('enter_otp') }}">
                            <div class="invalid-feedback">{{ translate('please_provide_valid_otp.')}}</div>
                        </div>

                        <div class="form-group">
                            <label for="si-password">{{ translate('new_password')}}</label>
                            <div class="password-toggle">
                                <input class="form-control rtl" name="password" type="password" id="si-password"
                                       required placeholder="{{ translate('enter_new_password') }}">
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input" type="checkbox"><i
                                        class="czi-eye password-toggle-indicator"></i><span
                                        class="sr-only">{{ translate('show_password')}} </span>
                                </label>
                                <div class="invalid-feedback">{{ translate('please_provide_valid_password.')}}</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="si-password">{{ translate('confirm_password')}}</label>
                            <div class="password-toggle rtl">
                                <input class="form-control" name="confirm_password" type="password" id="si-password"
                                       required placeholder="{{ translate('enter_confirm_password') }}">
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input" type="checkbox"><i
                                        class="czi-eye password-toggle-indicator"></i><span
                                        class="sr-only">{{ translate('show_password')}} </span>
                                </label>
                                <div class="invalid-feedback">{{ translate('please_provide_valid_password.')}}</div>
                            </div>
                        </div>
                        
                        @else
                        <form class="card-body needs-validation" novalidate method="POST"
                          action="{{request('customer.auth.password-recovery')}}">
                        @csrf
                        <div class="form-group d-none">
                            <input type="text" name="reset_token" value="{{$token}}" required>
                        </div>
                        

                        <div class="form-group">
                            <label for="si-password">{{ translate('new_password')}}</label>
                            <div class="password-toggle">
                                <input class="form-control rtl" name="password" type="password" id="si-password"
                                       required placeholder="{{ translate('enter_new_password') }}">
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input" type="checkbox"><i
                                        class="czi-eye password-toggle-indicator"></i><span
                                        class="sr-only">{{ translate('show_password')}} </span>
                                </label>
                                <div class="invalid-feedback">{{ translate('please_provide_valid_password.')}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="si-password">{{ translate('confirm_password')}}</label>
                            <div class="password-toggle rtl">
                                <input class="form-control" name="confirm_password" type="password" id="si-password"
                                       required placeholder="{{ translate('enter_confirm_password') }}">
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input" type="checkbox"><i
                                        class="czi-eye password-toggle-indicator"></i><span
                                        class="sr-only">{{ translate('show_password')}} </span>
                                </label>
                                <div class="invalid-feedback">{{ translate('please_provide_valid_password.')}}</div>
                            </div>
                        </div>
                        @endif

                        <button class="btn btn--primary" type="submit">{{ translate('reset_password') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
