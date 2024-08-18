@extends('layouts.front-end.app')

@section('title', $web_config['name']->value . ' ' . translate('online_Shopping') . ' | ' . $web_config['name']->value .
    ' ' . translate('ecommerce'))

    @push('css_or_js')
        <meta property="og:image"
            content="{{ theme_asset(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
        <meta property="og:title" content="Welcome To {{ $web_config['name']->value }} Home" />
        <meta property="og:url" content="{{ env('APP_URL') }}">
        <meta property="og:description"
            content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">

        <meta property="twitter:card"
            content="{{ theme_asset(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
        <meta property="twitter:title" content="Welcome To {{ $web_config['name']->value }} Home" />
        <meta property="twitter:url" content="{{ env('APP_URL') }}">
        <meta property="twitter:description"
            content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">

        <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}" />
        <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
        <style>
            .latest-product-margin {
                border-bottom: 2px solid #efefef;
                margin-top: 5rem;
            }

            .brand-name {
                font-size: 16px;
                font-weight: 400;
                line-height: 24px;
                letter-spacing: 0.005em;
            }

            .product-name {
                font-size: 20px !important;
                font-weight: 600;
                line-height: 24px;
                letter-spacing: 0.0015em;
            }

            .product-price {
                font-size: 20px !important;
                font-weight: 600;
                line-height: 24px;
                letter-spacing: 0.0015em;
            }

            .bestSelling-section {
                margin-top: 1rem;
            }

            .home-title {
                font-size: 34px;
                font-weight: 600;
                line-height: 40px;
                letter-spacing: 0.0025em;
            }

            .products {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.product {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr;
    grid-template-rows: 1fr 1fr;
    grid-gap: 20px;
    width: 100%;
    max-width: 1200px;
}

.product-card {
    border: none;
    /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); */
    /* transition: transform 0.2s ease-in-out; */
}

.product-card {
    background-color: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    text-align: center;
}

.product-category {
    color: #888;
    font-size: 14px;
    margin-bottom: 5px;
}

.rating {
    margin-top: 5px;
}

.price {
    margin: 10px 0;
}

.new-price {
    color: #e60000;
    font-weight: bold;
    font-size: 20px;
}

.old-price {
    color: #999;
    text-decoration: line-through;
    margin-left: 10px;
}

.shop-now {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 15px;
}

.countdown {
    display: flex;
    justify-content: space-between;
    margin: 10px 0;
}

.time {
    background-color: #e6e6e6;
    padding: 5px 10px;
    border-radius: 4px;
}

.availability {
    margin: 10px 0;
    font-size: 14px;
    color: #555;
}

.icons {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.icons button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
}
        </style>
    @endpush

@section('content')
    <div class="__inline-61">
        @php($decimalPointSettings = !empty(getWebConfig(name: 'decimal_point_settings')) ? getWebConfig(name: 'decimal_point_settings') : 0)
        <section class="bg-transparent">
            <div class="position-relative">
                @include('web-views.partials._home-top-slider', ['main_banner' => $main_banner])
            </div>
        </section>


        {{-- @include('web-views.partials._category-section-home') --}}

        @if ($featured_products->count() > 0)
            <div class="container py-4 rtl px-0 px-md-3">
                <div class="__inline-62 pt-3">
                    <div class="feature-product-title mt-0 web-text-primary">
                        {{ translate('featured_products') }}
                    </div>
                    <div class="text-end px-3 d-none d-md-block">
                        <a class="text-capitalize view-all-text web-text-primary"
                            href="{{ route('products', ['data_from' => 'featured', 'page' => 1]) }}">
                            {{ translate('view_all') }}
                            <i
                                class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                        </a>
                    </div>
                    <div class="feature-product">
                        <div class="carousel-wrap p-1">
                            <div class="owl-carousel owl-theme" id="featured_products_list">
                                @foreach ($featured_products as $product)
                                    <div>
                                        @include('web-views.partials._feature-product', [
                                            'product' => $product,
                                            'decimal_point_settings' => $decimalPointSettings,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-center pt-2 d-md-none">
                            <a class="text-capitalize view-all-text web-text-primary"
                                href="{{ route('products', ['data_from' => 'featured', 'page' => 1]) }}">
                                {{ translate('view_all') }}
                                <i
                                    class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        @if ($web_config['flash_deals'] && count($web_config['flash_deals']->products) > 0)
            @include('web-views.partials._flash-deal', ['decimal_point_settings' => $decimalPointSettings])
        @endif

        {{-- @if ($web_config['featured_deals'] && count($web_config['featured_deals']) > 0)
            <section class="featured_deal">
                <div class="container">
                    <div class="__featured-deal-wrap bg--light">
                        <div class="d-flex flex-wrap justify-content-between gap-8 mb-3">
                            <div class="w-0 flex-grow-1">
                                <span
                                    class="featured_deal_title font-bold text-dark">{{ translate('featured_deal') }}</span>
                                <br>
                                <span
                                    class="text-left text-nowrap">{{ translate('see_the_latest_deals_and_exciting_new_offers') }}!</span>
                            </div>
                            <div>
                                <a class="text-capitalize view-all-text web-text-primary"
                                    href="{{ route('products', ['data_from' => 'featured_deal']) }}">
                                    {{ translate('view_all') }}
                                    <i
                                        class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                                </a>
                            </div>
                        </div>
                        <div class="owl-carousel owl-theme new-arrivals-product">
                            @foreach ($web_config['featured_deals'] as $key => $product)
                                @include('web-views.partials._product-card-1', [
                                    'product' => $product,
                                    'decimal_point_settings' => $decimalPointSettings,
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif --}}

        @if (isset($main_section_banner))
            <div class="container rtl pt-4 px-0 px-md-3">
                <a href="{{ $main_section_banner->url }}" target="_blank" class="cursor-pointer d-block">
                    <img class="d-block footer_banner_img __inline-63" alt=""
                        src="{{ getValidImage(path: 'storage/app/public/banner/' . $main_section_banner['photo'], type: 'wide-banner') }}">
                </a>
            </div>
        @endif

        @php($businessMode = getWebConfig(name: 'business_mode'))
        @if ($businessMode == 'multi' && count($top_sellers) > 0)
            @include('web-views.partials._top-sellers')
        @endif

        @include('web-views.partials._deal-of-the-day', [
            'decimal_point_settings' => $decimalPointSettings,
        ])

        @if ($footer_banner->count() > 0)
            @foreach ($footer_banner as $key => $banner)
                @if ($key == 0)
                    <div class="container rtl d-sm-none">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <a href="{{ $banner->url }}" class="d-block" target="_blank">
                                    <img class="footer_banner_img __inline-63" alt=""
                                        src="{{ getValidImage(path: 'storage/app/public/banner/' . $banner['photo'], type: 'banner') }}">
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        {{-- <section class="new-arrival-section">

            <div class="container rtl mt-4">
                @if ($latest_products->count() > 0)
                    <div class="section-header">
                        <div class="arrival-title d-block">
                            <div class="text-capitalize">
                                {{ translate('new_arrivals') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="container rtl mb-3 overflow-hidden">
                <div class="py-2">
                    <div class="new_arrival_product">
                        <div class="carousel-wrap">
                            <div class="owl-carousel owl-theme new-arrivals-product">
                                @foreach ($latest_products as $key => $product)
                                    @include('web-views.partials._product-card-2', [
                                        'product' => $product,
                                        'decimal_point_settings' => $decimalPointSettings,
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container rtl px-0 px-md-3">
                <div class="row g-3 mx-max-md-0">

                    @if ($bestSellProduct->count() > 0)
                        @include('web-views.partials._best-selling')
                    @endif

                    @if ($topRated->count() > 0)
                        @include('web-views.partials._top-rated')
                    @endif
                </div>
            </div>
        </section> --}}


        @if ($footer_banner->count() > 0)
            @foreach ($footer_banner as $key => $banner)
                @if ($key == 1)
                    <div class="container rtl pt-4 d-sm-none">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <a href="{{ $banner->url }}" class="d-block" target="_blank">
                                    <img class="footer_banner_img __inline-63" alt=""
                                        src="{{ getValidImage(path: 'storage/app/public/banner/' . $banner['photo'], type: 'banner') }}">
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        @if (count($footer_banner) > 0)
            <div class="container rtl d-md-block d-none">
                <div class="row g-3 mt-3">

                    @if (count($footer_banner) <= 2)
                        @foreach ($footer_banner as $bannerIndex => $banner)
                            <div class="col-md-6">
                                <a href="{{ $banner->url }}" class="d-block" target="_blank">
                                    <img class="footer_banner_img __inline-63" alt=""
                                        src="{{ getValidImage(path: 'storage/app/public/banner/' . $banner['photo'], type: 'banner') }}">
                                </a>
                            </div>
                        @endforeach
                    @else
                        {{-- <?php
                        $footerBannerGroup = $footer_banner->take(count($footer_banner) / 2);
                        $footerBannerGroup2 = $footer_banner->splice(count($footer_banner) / 2);
                        ?> --}}
                        {{-- <div class="col-md-6">
                            <div
                                class="{{ count($footerBannerGroup) > 1 ? 'owl-carousel owl-theme footer-banner-slider' : '' }}">
                                @foreach ($footerBannerGroup as $banner)
                                    <a href="{{ $banner['url'] }}" class="d-block" target="_blank">
                                        <img class="footer_banner_img __inline-63" alt=""
                                            src="{{ getValidImage(path: 'storage/app/public/banner/' . $banner['photo'], type: 'banner') }}">
                                    </a>
                                @endforeach
                            </div>
                        </div> --}}

                        <div class="col-md-12">
                            <div class="owl-carousel owl-theme footer-banner-slider">
                                @foreach ($footer_banner as $banner)
                                    <a href="{{ $banner['url'] }}" class="d-block" target="_blank">
                                        <img class="footer_banner_img __inline-63" alt=""
                                            src="{{ getValidImage(path: 'storage/app/public/banner/' . $banner['photo'], type: 'banner') }}">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <section class="container rtl pt-4 bestSelling-section mb-5">
            @if ($bestSellProduct->count() > 0)
                @include('web-views.partials._best-selling')
            @endif
        </section>

        <!-- Deal of the Day -->
        <!-- Top Left Product -->
        {{-- <div class="products">
            <div class="product">
                <div class="product-card small">
                    <!-- <div class="discount-label">On Sale -10%</div> -->
                    <div class="product-category">CCTV</div>
                    <h3>IP Hikvision DS-2CD123G0E</h3>
                    <div class="rating">⭐⭐⭐⭐⭐ 32 reviews</div>
                    <div class="price">
                        <span class="new-price">$12.52</span>
                        <span class="old-price">$13.89</span>
                    </div>
                    <button class="shop-now">Shop Now</button>
                </div>

                <!-- Main Center Product -->
                <div class="product-card large">
                    <!-- <div class="discount-label center">-20%</div> -->
                    <div class="product-category">Laptop</div>
                    <h3>MacBook Pro 13 inch M2 (2022) - 10GPU/8/512</h3>
                    <p>16.2 inch, 3456 x 2234 Pixels, Apple M1 Pro, 32 GB SSD 512 GB, Apple M1, Multi-touch touchpad,
                        English
                        International Backlit Keyboard</p>
                    <div class="rating">⭐⭐⭐⭐⭐ 120 reviews</div>
                    <div class="price">
                        <span class="new-price">$28.52</span>
                        <span class="old-price">$45.89</span>
                    </div>
                    <div class="countdown">
                        <div class="time">23 Days</div>
                        <div class="time">12 Hours</div>
                        <div class="time">23 Min</div>
                        <div class="time">45 Sec</div>
                    </div>
                    <div class="availability">Available: 380</div>
                    <button class="shop-now">Shop Now</button>
                    <div class="icons">
                        <button class="wishlist"><i class="fa-regular fa-heart"></i></button>
                        <button class="compare">⇄</button>
                        <button class="share"><i class="fa-regular fa-eye"></i></button>
                    </div>
                </div>

                <!-- Top Right Product -->
                <div class="product-card small">
                    <!-- <div class="discount-label">On Sale -20%</div> -->
                    <div class="product-category">Mouse</div>
                    <h3>Microsoft Surface Arc Mouse</h3>
                    <div class="rating">⭐⭐⭐⭐⭐ 32 reviews</div>
                    <div class="price">
                        <span class="new-price">$12.02</span>
                        <span class="old-price">$14.99</span>
                    </div>
                    <button class="shop-now">Shop Now</button>
                </div>

                <!-- Bottom Left Product -->
                <div class="product-card small">
                    <!-- <div class="discount-label">On Sale -30%</div> -->
                    <div class="product-category">Smart Watch</div>
                    <h3>Samsung Galaxy Watch 4 45mm</h3>
                    <div class="rating">⭐⭐⭐⭐⭐ 32 reviews</div>
                    <div class="price">
                        <span class="new-price">$18.52</span>
                        <span class="old-price">$32.00</span>
                    </div>
                    <button class="shop-now">Shop Now</button>
                </div>

                <!-- Bottom Right Product -->
                <div class="product-card small">
                    <!-- <div class="discount-label">On Sale -25%</div> -->
                    <div class="product-category">Game</div>
                    <h3>Sony Playstation VR CHB-2703</h3>
                    <div class="rating">⭐⭐⭐⭐⭐ 32 reviews</div>
                    <div class="price">
                        <span class="new-price">$32.52</span>
                        <span class="old-price">$43.89</span>
                    </div>
                    <button class="shop-now">Shop Now</button>
                </div>
            </div>
        </div> --}}
        <!-- Deal of the Day Ends -->


        {{-- @if ($web_config['brand_setting'] && $brands->count() > 0)
            <section class="container rtl pt-4">

                <div class="section-header">
                    <div class="text-black font-bold __text-22px">
                        <span> {{ translate('brands') }}</span>
                    </div>
                    <div class="__mr-2px">
                        <a class="text-capitalize view-all-text web-text-primary" href="{{ route('brands') }}">
                            {{ translate('view_all') }}
                            <i
                                class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1' }}"></i>
                        </a>
                    </div>
                </div>

                <div class="mt-sm-3 mb-3 brand-slider">
                    <div class="owl-carousel owl-theme p-2 brands-slider">
                        @foreach ($brands as $brand)
                            <div class="text-center">
                                <a href="{{ route('products', ['id' => $brand['id'], 'data_from' => 'brand', 'page' => 1]) }}"
                                    class="__brand-item">
                                    <img alt="{{ $brand->name }}"
                                        src="{{ getValidImage(path: "storage/app/public/brand/$brand->image", type: 'brand') }}">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif --}}

        @if ($home_categories->count() > 0)
            @foreach ($home_categories as $category)
                @include('web-views.partials._category-wise-product', [
                    'decimal_point_settings' => $decimalPointSettings,
                ])
            @endforeach
        @endif

        @php($companyReliability = getWebConfig(name: 'company_reliability'))
        @if ($companyReliability != null)
            @include('web-views.partials._company-reliability')
        @endif
    </div>

    <span id="direction-from-session" data-value="{{ session()->get('direction') }}"></span>
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
@endpush
