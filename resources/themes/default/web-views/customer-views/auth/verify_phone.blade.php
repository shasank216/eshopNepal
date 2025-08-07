@extends('layouts.front-end.app')

@section('title', translate('Verify Phone'))

@section('content')
    @php($verification_by = getWebConfig('Verify Phone'))
    <div class="container py-4 py-lg-5 my-4 rtl">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-start">
                <h2 class="h3 mb-4">{{ translate('Verify your phone')}}?</h2>
                
                <div class="card py-2 mt-4">
                    <form class="card-body needs-validation" action="{{route('customer.verify.phone.verify')}}"
                        method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="recover-email">OTP Code</label>
                            <input class="form-control" type="text" name="code" required placeholder="{{ translate('Enter OTP') }}">
                            <div class="invalid-feedback">
                                {{ translate('Please provide valid OTP.')}}
                            </div>
                        </div>
                        <button class="btn btn--primary" type="submit">Verify OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
