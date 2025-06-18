<div class="container rtl pb-5 px-0 px-md-3">
    <div class="seller-card">
        <div class="h-100">
            <div class="card-body">
                <div class="row d-flex justify-content-between product-head-border">
                    <div class="seller-list-title">
                        <h5 class="home-title text-capitalize">
                            {{ translate('top_sellers') }}
                        </h5>
                    </div>
                    <div class="form-inline ml-auto">
                        <a class="text-capitalize view-all-text web-text-primary" href="{{ route('vendors') }}">
                            {{ translate('view_all') }}
                            <i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1' }}"></i>
                        </a>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="others-store-slider owl-theme owl-carousel">
                        @foreach ($top_sellers as $seller)
                            @php
                                $shop = $seller->shop;
                                $isClosed = $shop->temporary_close || (
                                    $shop->vacation_status &&
                                    $current_date >= $shop->vacation_start_date &&
                                    $current_date <= $shop->vacation_end_date
                                );
                            @endphp

                            <article class="others-store-card text-capitalize">
                                <a class="w-100" href="{{ route('shopView', ['id' => $shop->id]) }}">
                                    <div class="overflow-hidden other-store-banner">
                                        <img class="w-100 h-100 object-cover" loading="lazy"
                                            src="{{ getValidImage(path: 'storage/app/public/shop/banner/' . $shop->banner, type: 'shop-banner') }}"
                                            alt="{{ $shop->name }} banner">
                                    </div>
                                    <div class="name-area">
                                        <div class="position-relative">
                                            <div class="overflow-hidden other-store-logo rounded-full">
                                                <img class="rounded-full" loading="lazy"
                                                    src="{{ getValidImage(path: 'storage/app/public/shop/' . $shop->image, type: 'shop') }}"
                                                    alt="{{ $shop->name }} logo">
                                            </div>

                                            @if ($isClosed)
                                                <span class="temporary-closed position-absolute text-center rounded-full p-2">
                                                    <span>{{ translate('closed_now') }}</span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="info pt-2">
                                            <h5>{{ $shop->name }}</h5>
                                            <div class="d-flex align-items-center">
                                                <h6 class="web-text-primary">
                                                    {{ number_format($seller->average_rating, 1) }}
                                                </h6>
                                                <i class="tio-star text-star mx-1"></i>
                                                <small>{{ translate('rating') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-area">
                                        <div class="info-item">
                                            <h6 class="web-text-primary">
                                                {{ $seller->review_count < 1000 ? $seller->review_count : number_format($seller->review_count / 1000, 1) . 'K' }}
                                            </h6>
                                            <span>{{ translate('reviews') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <h6 class="web-text-primary">
                                                {{ $seller->product_count < 1000 ? $seller->product_count : number_format($seller->product_count / 1000, 1) . 'K' }}
                                            </h6>
                                            <span>{{ translate('products') }}</span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Geolocation logic with reload protection --}}
<script>
    if (!window.location.search.includes("lat=")) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const url = new URL(window.location.href);
            url.searchParams.set('lat', lat);
            url.searchParams.set('lng', lng);

            // Delay the redirect to ensure HTML finishes rendering
            setTimeout(() => {
                window.location.href = url.toString();
            }, 100);
        });
    }
</script>
