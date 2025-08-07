@if (isset($product))
    <div class="rtl">
        <div class="row g-4 pt-2 mt-0 pb-2 __deal-of align-items-start">
            <div class="col-md-3 col-sm-12">
                <div class="deal_of_the_day h-100">
                    @if (isset($deal_of_the_day->product))
                        <div class="d-flex justify-content-center align-items-center product-head-border">
                            <h4 class="for-feature-title __text-22px font-bold text-center m-0">
                                {{ translate('deal_of_the_day') }}
                            </h4>
                        </div>
                        <div class="recommended-product-card mt-0 min-height-auto">
                            <a href="{{ route('product', $product->slug) }}">
                                <div class="d-flex justify-content-center align-items-center __pt-20 __m-20-r">
                                    <div class="position-relative">
                                        <img class="__rounded-top aspect-1 h-auto" alt=""
                                            src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $deal_of_the_day->product['thumbnail'], type: 'product') }}">
                                        @if ($deal_of_the_day->discount > 0)
                                            <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                                                <span class="direction-ltr d-block">
                                                    @if ($deal_of_the_day->discount_type == 'percent')
                                                        -{{ round($deal_of_the_day->discount, !empty($decimal_point_settings) ? $decimal_point_settings : 0) }}%
                                                    @elseif($deal_of_the_day->discount_type == 'flat')
                                                        -{{ webCurrencyConverter(amount: $deal_of_the_day->discount) }}
                                                    @endif
                                                </span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="__i-1 bg-transparent text-center mb-0">
                                    <div class="px-0 d-flex flex-column">
                                        <p class="bold-subtitle m-0"> {{ $product->brand->name }}</p>
                                        @php($overallRating = getOverallRating($deal_of_the_day->product['reviews']))
                                        @if ($overallRating[0] != 0)
                                            <div class="rating-show">
                                                <span class="d-inline-block font-size-sm text-body">
                                                    @for ($inc = 1; $inc <= 5; $inc++)
                                                        @if ($inc <= (int) $overallRating[0])
                                                            <i class="tio-star text-warning"></i>
                                                        @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1)
                                                            <i class="tio-star-half text-warning"></i>
                                                        @else
                                                            <i class="tio-star-outlined text-warning"></i>
                                                        @endif
                                                    @endfor
                                                    <label class="badge-style review-text-container">
                                                        {{ count($deal_of_the_day->product['reviews']) }} 
                                                        <span class="review-text">reviews</span>
                                                    </label>
                                                </span>
                                            </div>
                                        @endif
                                        <a href="{{ route('product', $product->slug) }}">
                                            <span class="bold-title">
                                                {{ Str::limit($product['name'], 23) }}
                                            </span>
                                        </a>
                                        <div
                                            class="mb-3 pt-1 d-flex flex-wrap justify-content-center align-items-center text-center gap-8">
                                            @if ($product->discount > 0)
                                                <p class="product-price m-0">
                                                    {{ webCurrencyConverter(amount: $product->unit_price) }}
                                                    <del class="product-discount-price">
                                                        {{ webCurrencyConverter(
                                                            amount: $product->unit_price - getProductDiscount(product: $product, price: $product->unit_price),
                                                        ) }}
                                                    </del>
                                                </p>
                                            @else
                                                <p class="product-without-discount m-0">
                                                    {{ webCurrencyConverter(amount: $product->unit_price) }}
                                                    {{-- <del style="margin-left: 10px; color: #000;  font-weight: 500; font-size: 16px;">
                                                        {{ webCurrencyConverter(
                                                            amount: $product->unit_price - getProductDiscount(product: $product, price: $product->unit_price),
                                                        ) }}
                                                    </del> --}}
                                                </p>
                                            @endif
                                        </div>
                                        <button
                                            class="btn btn--primary font-bold px-4 rounded-10 text-uppercase w-100 action-buy-now-this-product"
                                            data-product-id="{{ $product->id }}">
                                            {{ translate('buy_now') }}
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @else
                        @if (isset($product))
                            <div class="d-flex justify-content-center align-items-center py-4">
                                <h4
                                    class="font-bold fs-16 m-0 align-items-center text-uppercase text-center px-2 web-text-primary">
                                    {{ translate('recommended_product') }}
                                </h4>
                            </div>
                            <div class="recommended-product-card mt-0">
                                <a href="{{ route('product', $product->slug) }}">
                                    <div class="d-flex justify-content-center align-items-center __pt-20 __m-20-r">
                                        <div class="position-relative">
                                            <img src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}"
                                                alt="">
                                            @if ($product->discount > 0)
                                                <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                                                    <span class="direction-ltr d-block">
                                                        @if ($product->discount_type == 'percent')
                                                            -{{ round($product->discount, !empty($decimal_point_settings) ? $decimal_point_settings : 0) }}%
                                                        @elseif($product->discount_type == 'flat')
                                                            -{{ webCurrencyConverter(amount: $product->discount) }}
                                                        @endif
                                                    </span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="__i-1 bg-transparent text-center mb-0 min-height-auto">
                                        <div class="px-0 pb-0 d-flex flex-column">
                                            @php($overallRating = getOverallRating($product['reviews']))
                                            @if ($overallRating[0] != 0)
                                                <div class="rating-show">
                                                    <span class="d-inline-block font-size-sm text-body">
                                                        @for ($inc = 0; $inc < 5; $inc++)
                                                            @if ($inc <= (int) $overallRating[0])
                                                                <i class="tio-star text-warning"></i>
                                                            @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                                                                <i class="tio-star-half text-warning"></i>
                                                            @else
                                                                <i class="tio-star-outlined text-warning"></i>
                                                            @endif
                                                        @endfor
                                                        <label class="badge-style review-text-container"> 
                                                            {{ count($product->reviews) }}
                                                            <span class="review-text">reviews</span>
                                                        </label>
                                                    </span>
                                                </div>
                                            @endif
                                            <h6 class="font-semibold pt-1">
                                                {{ Str::limit($product['name'], 30) }}
                                            </h6>
                                            <div
                                                class="mb-4 pt-1 d-flex flex-wrap justify-content-center align-items-center text-center gap-8">
                                                @if ($product->discount > 0)
                                                    <del class="__text-12px __color-9B9B9B">
                                                        {{ webCurrencyConverter(amount: $product->unit_price) }}
                                                    </del>
                                                @endif
                                                <span class="text-accent __text-22px text-dark">
                                                    {{ webCurrencyConverter(
                                                        amount: $product->unit_price - getProductDiscount(product: $product, price: $product->unit_price),
                                                    ) }}
                                                </span>
                                            </div>
                                            <button
                                                class="btn btn--primary font-bold px-4 rounded-10 text-uppercase get-view-by-onclick w-100"
                                                data-link="{{ route('product', $product->slug) }}">
                                                {{ translate('View Details') }}
                                            </button>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-md-9 col-sm-12">
                <div class="">
                    <div class="d-flex justify-content-between align-items-center mb-4 product-head-border">
                        <div class="text-center">
                            <span class="for-feature-title __text-22px font-bold text-center">
                                {{ translate('latest_products') }}
                            </span>
                        </div>
                        <div class="mr-1">
                            <a class="text-capitalize view-all-text web-text-primary"
                                href="{{ route('products', ['data_from' => 'latest']) }}">
                                {{ translate('view_all') }}
                                <i
                                    class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1' }}"></i>
                            </a>
                        </div>
                    </div>
                    {{-- <div class="row mt-0 g-2">
                        @foreach ($latest_products as $product)
                            <div class="col-xl-3 col-sm-4 col-md-6 col-lg-4 col-6">
                                <div>
                                    @include('web-views.partials._inline-single-product',['product'=>$product,'decimal_point_settings'=>$decimal_point_settings])
                                </div>
                            </div>
                        @endforeach
                    </div> --}}
                </div>
                <div class="owl-theme owl-carousel latest-slider">
                    @foreach ($latest_products as $product)
                        <div>
                            <a href="{{ route('product', $product->slug) }}">
                                @include('web-views.partials._inline-single-product', [
                                    'product' => $product,
                                    'decimal_point_settings' => $decimal_point_settings,
                                ])
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
