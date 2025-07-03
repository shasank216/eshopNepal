@extends('layouts.front-end.app')

@section('title', translate('forgot_Password'))

@section('content')
    @php($verification_by = getWebConfig(name: 'forgot_password_verification'))
    <div class="container py-4 py-lg-5 my-4 rtl">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-start">
                <h2 class="h3 mb-4">{{ translate('forgot_your_password') }}?</h2>
                <p class="font-size-md">
                    {{ translate('change_your_password_in_three_easy_steps.') }}
                    {{ translate('this_helps_to_keep_your_new_password_secure.') }}
                </p>
                @if ($verification_by == 'email')
                    <ol class="list-unstyled font-size-md p-0">
                        <li>
                            <span class="text-primary mr-2">{{ translate('1') }}.</span>
                            {{ translate('use_your_registered_email_address') }}
                        </li>
                        <li>
                            <span class="text-primary mr-2">{{ translate('2') }}.</span>
                            {{ translate('we_will_send_you_a_temporary_password_recovery_link_in_your_email') }}
                        </li>
                        <li>
                            <span class="text-primary mr-2">{{ translate('3') }}.</span>
                            {{ translate('Click_the_recovery_link_to_change_your_password_on_our_secure_website') }}
                        </li>
                    </ol>

                    <div class="card py-1 mt-3" style="max-width: 300px;">
                        <form class="card-body p-3 needs-validation" action="{{ route('customer.auth.forgot-password') }}"
                              method="post">
                            @csrf

                            <div class="form-group mb-2">
                                <label for="recover_type" class="small mb-1">Recover Type</label>
                                <select name="recover_type" id="recover_type" class="form-control form-control-sm">
                                    <option value="email" selected>Email</option>
                                    <option value="phone">Phone</option>
                                </select>
                            </div>

                            <div id="email_section" class="form-group mb-2">
                                <label for="recover-email" class="small mb-1">{{ translate('email_address') }}</label>
                                <input class="form-control form-control-sm" type="email" name="identity"
                                       id="recover-email" required
                                       placeholder="{{ translate('ex') }}: demo@example.com">
                                <div class="invalid-feedback small">
                                    {{ translate('please_provide_valid_email_address.') }}
                                </div>
                            </div>

                            <div id="phone_section" class="form-group mb-2 d-none">
                                <label for="recover-phone" class="small mb-1">{{ translate('phone_number') }}</label>
                                <input class="form-control form-control-sm" type="tel"
                                       id="recover-phone"
                                       placeholder="{{ translate('ex') }}: 9800000000">
                                <div class="invalid-feedback small">
                                    {{ translate('please_provide_valid_phone_number.') }}
                                </div>
                            </div>

                            <button class="btn btn--primary btn-sm mt-2" type="submit">
                                {{ translate('get_new_password') }}
                            </button>
                        </form>
                    </div>
                @else
                    <ol class="list-unstyled font-size-md p-0">
                        <li>
                            <span class="text-primary mr-2">{{ translate('1') }}.</span>
                            {{ translate('use_your_registered_phone_number') }}.
                        </li>
                        <li>
                            <span class="text-primary mr-2">{{ translate('2') }}.</span>
                            {{ translate('we_will_send_you_a_temporary_OTP_in_your_phone_number') }}.
                        </li>
                        <li>
                            <span class="text-primary mr-2">{{ translate('3') }}.</span>
                            {{ translate('use_the_OTP_code_to_change_your_password_on_our_secure_website.') }}
                        </li>
                    </ol>

                    <div class="card py-2 mt-4">
                        <form class="card-body needs-validation" action="{{ route('customer.auth.forgot-password') }}"
                              method="post">
                            @csrf
                            <div class="form-group">
                                <label for="recover-phone">{{ translate('phone_number') }}</label>
                                <input class="form-control" type="text" name="identity" required
                                       placeholder="{{ translate('enter_your_phone_number') }}">
                                <div class="invalid-feedback">
                                    {{ translate('please_provide_valid_phone_number') }}
                                </div>
                            </div>
                            <button class="btn btn--primary" type="submit">{{ translate('send_OTP') }}</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const recoverType = document.getElementById("recover_type");
        const emailSection = document.getElementById("email_section");
        const phoneSection = document.getElementById("phone_section");
        const emailInput = document.getElementById("recover-email");
        const phoneInput = document.getElementById("recover-phone");

        recoverType.addEventListener("change", function () {
            if (recoverType.value === "email") {
                emailSection.classList.remove("d-none");
                phoneSection.classList.add("d-none");

                emailInput.setAttribute("name", "identity");
                phoneInput.removeAttribute("name");
                emailInput.required = true;
                phoneInput.required = false;
            } else {
                phoneSection.classList.remove("d-none");
                emailSection.classList.add("d-none");

                phoneInput.setAttribute("name", "identity");
                emailInput.removeAttribute("name");
                phoneInput.required = true;
                emailInput.required = false;
            }
        });
    });
</script>
@endpush
