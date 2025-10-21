@extends('layouts.back-end.app')
@section('title', translate('pay_driver'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/earning_statictics.png') }}" alt=""
                    style="width:fit-content;">
                {{ translate('pay_driver') }}
            </h2>
        </div>
        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <form action="{{ route('admin.delivery-man.cash-pay', ['id' => $deliveryMan['id']]) }}" method="post">
                        @csrf
                        <div class="card-body">
                            <h5 class="mb-0 page-header-title d-flex align-items-center gap-2 border-bottom pb-3 mb-3">
                                <i class="tio-money"></i>
                                {{ translate('pay_driver') }}
                            </h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="d-flex flex-wrap gap-2 mt-3 title-color" id="chosen_price_div">
                                        <div class="product-description-label">{{ translate('current_balance') }}:
                                        </div>
                                        <div class="product-price">
                                            <strong>{{ $deliveryMan['wallet'] ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $deliveryMan->wallet->current_balance), currencyCode: getCurrencyCode(type: 'default')) : 0 }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="number" min="0.001" max="{{ $deliveryMan->wallet->current_balance }}"
                                            name="amount" class="form-control" step="0.001"
                                            placeholder="Enter Pay amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="submit" class="btn btn--primary px-4">{{ translate('pay') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection
