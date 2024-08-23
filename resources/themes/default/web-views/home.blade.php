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

            .dealOfTheDayProducts {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .dealOfTheDay {
                display: grid;
                grid-template-columns: 1fr 2fr 1fr;
                grid-template-rows: 1fr 1fr;
                grid-gap: 20px;
                width: 100%;
                max-width: 1200px;

            }

            .product {


                /* ----------- Added  ---------- */
                position: relative;

                /* max-width: 290px; */
                /* ----------- Added  ---------- */
            }

            .product-card {
                background-color: #f5f5f5;
                padding: 20px;
                /* border-radius: 8px; */
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                position: relative;
                text-align: center;

                border: none;
                /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); */
                transition: transform 0.2s ease-in-out;
            }

            .product-card.large {
                grid-column: 2 / 3;
                grid-row: 1 / 3;
                text-align: left;
                position: relative;
                overflow: hidden;
            }

            .discount-label {
                position: absolute;
                top: 10px;
                left: 10px;
                background-color: color-mix(in srgb, #FF3B3B, #fff 20%);
                color: #fff;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 12px;
            }

            .discount-label.center {
                top: 5%;
                left: 31px;
                transform: translate(-50%, -50%) rotate(-45deg);
                padding: 42px 82px;
                font-size: 16px;
            }

            .product-category {
                color: #727272;
                font-size: 16px;
                font-weight: 400;
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

            .custom-card {
                width: 100%;
                height: 300px;
                border: none;
                position: relative;
                background-color: #D9D9D9;

                margin-bottom: 0;
            }

            .custom-long-card {
                width: 100%;
                height: 300px;
                border: none;
                position: relative;
                background-color: #D9D9D9;

                margin-bottom: 0;
            }

            .category-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                font-size: 14px;
                font-weight: 500;
                border-radius: 15px;
                background-color: #FFFFFF;
                color: #000;
            }

            .card-body {
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                height: 100%;
            }

            /* .card-title {
                margin-bottom: 0;
                font-size: 26px;
                font-weight: 600;
                color: #FFFFFF;
            } */

            /* ========== */
            .card-title {
                font-size: 26px;
                font-weight: 600;
                color: #FFFFFF;
                white-space: nowrap;
                /* Prevents text from wrapping to the next line */
                overflow: hidden;
                /* Hides any overflow text */
                text-overflow: ellipsis;
                /* Adds ellipsis (...) if text overflows */
                margin-bottom: 0;
            }

            /* ========== */


            .card-subtitle {
                font-size: 14px;
                font-weight: 400;
                color: #FFFFFF;
            }
        </style>

        
<style>
    .add-to-cart {
        width: 100%;
        padding: 10px;
        background-color: #4759ff;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .add-to-cart:hover {
        background-color: #3c4ed0;
    }

    .actions {
        position: absolute;
        top: 10px;
        right: 10px;
        /* 40px */
        display: flex;
        flex-direction: column;
    }

    .action-btn {
        margin-bottom: 5px;
        width: 40px;
        height: 40px;
        background: white;
        border: none;
        padding: 10px;
        border-radius: 50px 50px;
        cursor: pointer;
        font-size: 18px;
    }

    .arrow {
        font-size: 20px;
        color: #727272;
        cursor: pointer;
        padding: 0 20px;
    }

    .arrow-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .availability {
        margin: 10px 0;
        font-size: 16px;
        font-weight: 500;
        color: #727272;
    }

    .bg-blue {
        background-color: #74A4D0;
    }

    .bg-orange {
        background-color: #E79142;
    }

    .bg-purple {
        background-color: #CB58E0;
    }

    .box {
        box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
        margin-right: 5px;
        border-radius: 5px;
        font-weight: 500;
        font-size: 16px;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        height: 100%;
    }

    .card-title {
        font-size: 26px;
        font-weight: 600;
        color: #FFFFFF;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0;
    }

    .card-subtitle {
        font-size: 14px;
        font-weight: 400;
        color: #FFFFFF;
    }

    .carding {
        width: 247px;
        height: 300px;
        background-color: color-mix(in srgb, #606BBF, #fff 90%);
        border-radius: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        border: none;
        position: relative;
        cursor: pointer;
    }

    .product-hover_details .carding {
        /* top: -25px;
        left: -10px; */
        width: 246px;

    }

    .category-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 15px;
        background-color: #FFFFFF;
        color: #000;
    }

    .color-box {
        width: 30px;
        height: 30px;
    }

    .color-box-small {
        display: flex;
    }

    .color-box1 {
        background-color: gray;
    }

    .color-box2 {
        background-color: rgb(109, 30, 30);
    }

    .color-box3 {
        background-color: rgb(116, 79, 79);
    }

    .color-box4 {
        background-color: rgb(133, 141, 24);
    }

    .color-box5 {
        background-color: rgb(194, 102, 163);
    }

    .countdown {
        display: flex;
        justify-content: space-between;
        margin: 10px 0;
    }

    .custom-card {
        width: 100%;
        height: 300px;
        border: none;
        position: relative;
        background-color: #D9D9D9;
        margin-bottom: 0;
    }

    .custom-long-card {
        width: 100%;
        height: 300px;
        border: none;
        position: relative;
        background-color: #D9D9D9;
        margin-bottom: 0;
    }

    .dealOfTheDay {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr;
        grid-template-rows: 1fr 1fr;
        grid-gap: 20px;
        width: 100%;
        max-width: 1200px;
    }

    .dealOfTheDayProducts {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .discount-label {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: color-mix(in srgb, #FF3B3B, #fff 20%);
        color: #fff;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
    }

    .discount-label.center {
        top: 5%;
        left: 31px;
        transform: translate(-50%, -50%) rotate(-45deg);
        padding: 42px 82px;
        font-size: 16px;
    }

    .fourth-box {
        background-color: #FFB4B4;
        border: none;
    }

    .gallery {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .gallery figure {
        border-radius: 10px;
        border: 1px solid color-mix(in srgb, #606BBF, #fff 90%);
        background-color: color-mix(in srgb, #606BBF, #fff 90%);
        width: 100%;

        /* height: 63px */
        height: 59.3px;
        cursor: pointer;
    }

    .gallery figure img {
        width: 100%;
        display: block;
        border-radius: inherit;
    }

    .product-hover_details {
        position: absolute;
        top: -16px;
        left: -16px;
        padding: 1rem;

        border-radius: 15px;
        z-index: 4;
        width: 370px;
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        background-color: #fff;
        cursor: pointer;
    }



    .image-placeholder {
        height: 200px;
        background-color: #f0f0f5;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .icons {
        margin-top: 15px;
        margin-right: 10px;
    }

    .icons button {
        background: white;
        padding: 10px;
        border-radius: 50px 50px;
        border: none;
        cursor: pointer;
        font-size: 18px;
    }

    .main-details {
        display: block;
    }

    .new-badge {
        top: 10px;
        left: 10px;
        z-index: 1;
        font-size: 0.8rem;
        padding: 5px 10px;
    }

    .new-price {
        color: color-mix(in srgb, #FF3B3B, #fff 20%);
        font-weight: 600;
        font-size: 20px;
    }

    .old-price {
        font-weight: 500;
        font-size: 16px;
        color: color-mix(in srgb, #1F3C74, #000000 50%);
        text-decoration: line-through;
        margin-left: 10px;
    }

    .p {
        font-weight: 600;
        font-size: 20px;
    }

    .promo-card {
        padding: 10px 20px;
        margin-bottom: 20px;
    }

    .product {
        position: relative;
    }

    .product-card {
        background-color: #f5f5f5;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        text-align: center;
        border: none;
        transition: transform 0.2s ease-in-out;
    }

    .product-card.large {
        grid-column: 2 / 3;
        grid-row: 1 / 3;
        text-align: left;
        position: relative;
        overflow: hidden;
    }

    .product-card:hover {
        transform: scale(1.05);
    }

    .product-category {
        color: #727272;
        font-size: 16px;
        font-weight: 400;
        margin-bottom: 5px;
    }

    .product-colors {
        display: flex;
        gap: 5px;
        margin-bottom: 15px;
    }

    .product-tags {
        display: flex;
        gap: 5px;
        justify-content: center;
        margin-bottom: 10px;
    }

    /* Range */

    .progress {
        background-color: #f8f9fa;
        border-radius: 50px;
        height: 15px;
        overflow: hidden;
    }

    .progress-bar {
        background-color: #1f3c74;
        border-radius: 50px;
        height: 100%;
        width: 80%;
    }

    /* Range */

    .ribbon {
        position: absolute;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: bold;
        color: white;
        z-index: 3;
        top: 20px;
        left: -13px;
        clip-path: polygon(100% 0, 85% 50%, 100% 100%, 10% 100%, 10% 0%);
        box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
    }

    .ribbon-new {
        top: 60px;
        left: -6px;
        width: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #1F3C74;
        font-size: 14px;
        font-weight: 500;
        z-index: 3;
    }

    .ribbon-sale {
        background-color: color-mix(in srgb, #FF3B3B, #fff 20%);
        display: flex;
        justify-content: center;
        align-items: center;
        width: 130px;
    }

    .row>.col-md-3,
    .row>.col-md-6 {
        padding-right: 10px;
        padding-left: 10px;
    }

    .shop-now {
        background-color: #1F3C74;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 15px;
        cursor: pointer;
        margin-top: 15px;
        font-weight: 800;
        font-size: 16px;
    }

    .shop-now:hover {
        background-color: #0056b3;
    }

    .slider {
        padding: 5px;
        border: 1px solid;
    }

    .span.bold-subtitle {
        font-size: 20px;
        font-weight: 600;
    }

    .tag {
        background-color: #e0e0e0;
        border-radius: 5px;
        padding: 5px;
        font-size: 12px;
        color: #555555;
        box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset;
    }

    .timer {
        width: 250px;
        position: absolute;
        bottom: 12px;
        padding: 8px 8px 0 8px;
        left: 50%;
        transform: translateX(-50%);
    }

    .time {
        background-color: #ffffff;
        text-align: center;
        padding: 10px 5px;
        border-radius: 4px;
        margin: 0 5px;
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    }

    .time-box {
        background-color: #ffffff;
        border-radius: 5px;
        width: 22%;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
    }

    .number {
        font-size: 16px;
        font-weight: 500;
        color: #606BBF;
        color: color-mix(in srgb, #606BBF, #000000 50%);
    }

    .label {
        font-size: 16px;
        font-weight: 500;
        color: #727272;
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


        <!-- Deal of the day -->
        <!-- Top Left Product -->
        {{-- <section class="container rtl pt-4 bestSelling-section mb-5">
            <nav class="navbar ">
                <p style="font-size: 34px; font-weight: 600;">Deal Of The Day</p>
                <span class="form-inline">
                    <!-- <p style="float: right; letter-spacing: 5px;"><i class="fa-solid fa-less-than"></i></p>
                    <p style="float: right;"><i class="fa-solid fa-greater-than"></i></p> -->
                    <div class="arrow-container">
                        <div class="arrow"><i class="fa-solid fa-chevron-left"></i></div>
                        <div class="separator"></div>
                        <div class="arrow"><i class="fa-solid fa-chevron-right"></i></div>
                    </div>
                </span>
            </nav>
            <hr>
            <div class="dealOfTheDayProducts mt-5">
                <!-- <div class="product"> -->
                <div class="dealOfTheDay">
                    <div class="product-card">
                        <div class="row">
                            <div class="discount-label">On Sale -10%</div>
        
                            <div class="col-md-4">
                                <!-- <img src="fjg.jpg" alt="testing"> -->
                            </div>
        
                            <div class="col-md-8">
                                <div class="small">
                                    <div class="product-category">CCTV</div>
                                    <h3 style="color: #1F3C74;">IP Hikvision DS-2CD123G0E</h3>
                                    <div class="rating">⭐⭐⭐⭐⭐ 32 reviews</div>
                                    <div class="price">
                                        <span class="new-price">$12.52</span>
                                        <span class="old-price">$13.89</span>
                                    </div>
                                    <button class="shop-now">Shop Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <!-- Main Center Product -->
                    <div class="product-card large">
        
                        <div class="row">
                            <div class="discount-label center">-20%</div>
        
                            <div class="col-md-5">
                                <!-- <img src="" alt="Hello"> -->
                            </div>
        
                            <div class="col-md-7">
                                <div class="product-category">Laptop</div>
                                <h3 style="font-weight: 600; font-size: 20px; color: #1F3C74;">MacBook Pro 13 inch M2 (2022) -
                                    10GPU/8/512</h3>
                                <div class="rating">⭐⭐⭐⭐⭐ 120 reviews</div>
                                <p style="font-weight: 400; font-size: 16px; color: #727272;">16.2 inch, 3456 x 2234 Pixels,
                                    Apple M1 Pro, 32 GB SSD 512 GB, Apple M1, Multi-touch
                                    touchpad,
                                    English
                                    International Backlit Keyboard
                                </p>
        
                                <span class="color-box-small">
                                    <div class="color-box first-box mx-1"></div>
                                    <div class="color-box second-box mx-1"></div>
                                    <div class="color-box third-box mx-1"></div>
                                    <div class="color-box fourth-box mx-1"></div>
                                </span>
        
                                <div class="price">
                                    <span class="new-price">$28.52</span>
                                    <span class="old-price">$45.89</span>
                                </div>
        
                                <div class="countdown">
                                    <div class="time">
                                        <span class="number">23</span>
                                        <span class="label">Days</span>
                                    </div>
                                    <div class="time">
                                        <span class="number">12</span>
                                        <span class="label">Hours</span>
                                    </div>
                                    <div class="time">
                                        <span class="number">23</span>
                                        <span class="label">Mins</span>
                                    </div>
                                    <div class="time">
                                        <span class="number">43</span>
                                        <span class="label">Secs</span>
                                    </div>
                                </div>
        
                                <div class="availability">Available: <span class="price">380</span></div>
        
                                <!-- Slider -->
                                <div class="slider-container">
                                    <div class="range">
                                        <input type="range" name="" id="range1">
                                    </div>
                                </div>
                                <!-- Slider -->
        
                                <button class="shop-now">Shop Now</button>
        
                                <div class="icons">
                                    <button class="wishlist"><i class="fa fa-heart"></i></button>
                                    <button class="compare"><i class="fa fa-exchange" aria-hidden="true"></i></button>
                                    <button class="share"><i class="fa fa-eye"></i></button>
                                </div>
        
        
                            </div>
                        </div>
                    </div>
        
                    <!-- Top Right Product -->
                    <div class="product-card">
                        <div class="row">
                            <div class="discount-label">On Sale -10%</div>
        
                            <div class="col-md-4">
                                <!-- <img src="fjg.jpg" alt="testing"> -->
                            </div>
        
                            <div class="col-md-8">
                                <div class="small">
                                    <div class="product-category">CCTV</div>
                                    <h3 style="color: #1F3C74;">IP Hikvision DS-2CD123G0E</h3>
                                    <div class="rating">⭐⭐⭐⭐⭐ 32 reviews</div>
                                    <div class="price">
                                        <span class="new-price">$12.52</span>
                                        <span class="old-price">$13.89</span>
                                    </div>
                                    <button class="shop-now">Shop Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <!-- Bottom Left Product -->
                    <div class="product-card">
                        <div class="row">
                            <div class="discount-label">On Sale -10%</div>
        
                            <div class="col-md-4">
                                <!-- <img src="fjg.jpg" alt="testing"> -->
                            </div>
        
                            <div class="col-md-8">
                                <div class="small">
                                    <div class="product-category">CCTV</div>
                                    <h3 style="color: #1F3C74;">IP Hikvision DS-2CD123G0E</h3>
                                    <div class="rating">⭐⭐⭐⭐⭐ 32 reviews</div>
                                    <div class="price">
                                        <span class="new-price">$12.52</span>
                                        <span class="old-price">$13.89</span>
                                    </div>
                                    <button class="shop-now">Shop Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
        
        
                    <!-- Bottom Right Product -->
                    <div class="product-card">
                        <div class="row">
                            <div class="discount-label">On Sale -10%</div>
        
                            <div class="col-md-4">
                                <!-- <img src="fjg.jpg" alt="testing"> -->
                            </div>
        
                            <div class="col-md-8">
                                <div class="small">
                                    <div class="product-category">CCTV</div>
                                    <h3 style="color: #1F3C74;">IP Hikvision DS-2CD123G0E</h3>
                                    <div class="rating">⭐⭐⭐⭐⭐ 32 reviews</div>
                                    <div class="price">
                                        <span class="new-price">$12.52</span>
                                        <span class="old-price">$13.89</span>
                                    </div>
                                    <button class="shop-now">Shop Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
        
                </div>
            </div>
        </section> --}}
        <!-- Deal of the Day Ends -->

        {{-- Advertise section --}}

        @if (count($footer_banner) > 0)
            <div class="container rtl d-md-block d-none mt-3">
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

        {{-- Advertise section end --}}


        {{-- search by category --}}
        <section class="container">
            @php($decimal_point_settings = getWebConfig(name: 'decimal_point_settings'))

            <div class="container pt-5 pb-5 mb-2 mb-md-4 rtl __inline-35" dir="{{ Session::get('direction') }}">
                <div class="row">
                    <aside
                        class="col-lg-3 hidden-xs col-md-3 col-sm-4 SearchParameters __search-sidebar {{ Session::get('direction') === 'rtl' ? 'pl-2' : 'pr-2' }}"
                        id="SearchParameters">
                        <div class="cz-sidebars __inline-35 bg-white" id="shop-sidebar">
                            <div class="cz-sidebar-header bg-light">
                                <button class="close ms-auto" type="button" data-dismiss="sidebar" aria-label="Close">
                                    <i class="tio-clear"></i>
                                </button>
                            </div>

                            <div class="mt-3 __cate-side-arrordion">
                                <div>
                                    <div class="home text-center __cate-side-title">
                                        <span
                                            class="widget-title font-semibold">{{ translate('shops by categories') }}</span>
                                    </div>
                                    @php($categories = \App\Utils\CategoryManager::parents())
                                    <div class="accordion mt-n1 __cate-side-price" id="shop-categories">
                                        @foreach ($categories as $category)
                                            <div class="menu--caret-accordion">
                                                <div class="card-header flex-between">
                                                    <div>
                                                        <img alt="{{ $category->name }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/category/' . $category->icon, type: 'category') }}"
                                                            width="64" height="64"
                                                            style="height: 20px; width: 20px;">
                                                        <label
                                                            class="for-hover-label cursor-pointer get-view-by-onclick home-category"
                                                            data-link="{{ route('products', ['id' => $category['id'], 'data_from' => 'category', 'page' => 1]) }}">
                                                            {{ $category['name'] }}
                                                        </label>
                                                    </div>
                                                    <div class="px-2 cursor-pointer menu--caret">
                                                        <strong class="pull-right for-brand-hover">
                                                            @if ($category->childes->count() > 0)
                                                                <i class="tio-next-ui fs-13"></i>
                                                            @endif
                                                        </strong>
                                                    </div>
                                                </div>
                                                <div class="card-body p-0 ms-2 d--none"
                                                    id="collapse-{{ $category['id'] }}">
                                                    @foreach ($category->childes as $child)
                                                        <div class="menu--caret-accordion">
                                                            <div class="for-hover-label card-header flex-between">
                                                                <div>
                                                                    <label class="cursor-pointer get-view-by-onclick"
                                                                        data-link="{{ route('products', ['id' => $child['id'], 'data_from' => 'category', 'page' => 1]) }}">
                                                                        {{ $child['name'] }}
                                                                    </label>
                                                                </div>
                                                                <div class="px-2 cursor-pointer menu--caret">
                                                                    <strong class="pull-right">
                                                                        @if ($child->childes->count() > 0)
                                                                            <i class="tio-next-ui fs-13"></i>
                                                                        @endif
                                                                    </strong>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-0 ms-2 d--none"
                                                                id="collapse-{{ $child['id'] }}">
                                                                @foreach ($child->childes as $ch)
                                                                    <div class="card-header">
                                                                        <label
                                                                            class="for-hover-label d-block cursor-pointer text-left get-view-by-onclick"
                                                                            data-link="{{ route('products', ['id' => $ch['id'], 'data_from' => 'category', 'page' => 1]) }}">
                                                                            {{ $ch['name'] }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </aside>

                    <section class="col-lg-9">
                        <div class="row" id="ajax-products">
                            @if (count($products) > 0)

                                @php($decimal_point_settings = getWebConfig(name: 'decimal_point_settings'))
                                @foreach ($products as $product)
                                    @if (!empty($product['product_id']))
                                        @php($product = $product->product)
                                    @endif
                                    <div
                                        class=" {{ Request::is('products*') ? 'col-lg-4 col-md-4 col-sm-4 col-6' : 'col-lg-4 col-md-4 col-sm-4 col-6' }} {{ Request::is('shopView*') ? 'col-lg-3 col-md-4 col-sm-4 col-6' : '' }} p-2">
                                        @if (!empty($product))
                                            @include('web-views.partials._filter-single-product', [
                                                'product' => $product,
                                                'decimal_point_settings' => $decimal_point_settings,
                                            ])
                                        @endif
                                    </div>
                                @endforeach

                                <div class="col-12">
                                    <nav class="d-flex justify-content-between pt-2" aria-label="Page navigation"
                                        id="paginator-ajax">
                                        {!! $products->links() !!}
                                    </nav>
                                </div>
                            @else
                                <div class="d-flex justify-content-center align-items-center w-100 py-5">
                                    <div>
                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/media/product.svg') }}"
                                            class="img-fluid" alt="">
                                        <h6 class="text-muted">{{ translate('no_product_found') }}</h6>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>

            {{-- <span id="products-search-data-backup"
            data-url="{{ url('/products') }}"
            data-id="{{ $data['id'] }}"
            data-name="{{ $data['name'] }}"
            data-from="{{ $data['data_from'] }}"
            data-sort="{{ $data['sort_by'] }}"
            data-min-price="{{ $data['min_price'] }}"
            data-max-price="{{ $data['max_price'] }}"
            data-message="{{ translate('items_found') }}"
            ></span> --}}
        </section>

        <section class="container">
            <!-- Latest News start -->

            <nav class="navbar">
                <p style="font-size: 34px; font-weight: 600;">Latest News</p>
                <span class="form-inline ml-auto">
                    <p style="margin-bottom: 0;"><u><a href="#"
                                style="color: #000; font-size: 16px; font-weight: 600;">See All
                                <i class="fa fa-arrow-right"></i></a></u></p>
                </span>
            </nav>
            <hr>

            <div class="container" style="margin-top: 40px;">
                <div class="row">
                    <!-- First Row -->
                    <div class="col-md-3 mb-4">
                        <div class="card custom-card" style="border-radius: 0;">
                            <div class="card-body">
                                <span class="badge badge-light category-badge">Fashion</span>
                                <div class="card-text mt-auto">
                                    <h5 class="card-title" style="line-height: 44px;">Discover new Trends</h5>
                                    <p class="card-subtitle">by Alpha on August 8, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card custom-card" style="border-radius: 0;">
                            <div class="card-body">
                                <span class="badge badge-light category-badge">Gadgets</span>
                                <div class="card-text mt-auto">
                                    <h5 class="card-title" style="line-height: 44px;">Apple Wireless Headphones</h5>
                                    <p class="card-subtitle">by Alpha on August 8, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card custom-long-card" style="border-radius: 0;">
                            <div class="card-body">
                                <span class="badge badge-light category-badge">Smart Watch</span>
                                <div class="card-text mt-auto">
                                    <h5 class="card-title" style="line-height: 44px;">Apple Watch Series 6</h5>
                                    <p class="card-subtitle">by Alpha on August 8, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Second Row -->
                    <div class="col-md-6 mb-4">
                        <div class="card custom-long-card" style="border-radius: 0;">
                            <div class="card-body">
                                <span class="badge badge-light category-badge">Game</span>
                                <div class="card-text mt-auto">
                                    <h5 class="card-title" style="line-height: 44px;">What's interesting about VR games?
                                    </h5>
                                    <p class="card-subtitle">by Alpha on August 8, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card custom-card" style="border-radius: 0;">
                            <div class="card-body">
                                <span class="badge badge-light category-badge">Camera</span>
                                <div class="card-text mt-auto">
                                    <h5 class="card-title" style="line-height: 44px;">The peak quality of the viewfinder
                                    </h5>
                                    <p class="card-subtitle">by Alpha on August 8, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card custom-card" style="border-radius: 0;">
                            <div class="card-body">
                                <span class="badge badge-light category-badge">Laptop</span>
                                <div class="card-text mt-auto">
                                    <h5 class="card-title" style="line-height: 44px;">What's good about new macbook?</h5>
                                    <p class="card-subtitle">by Alpha on August 8, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest News ends -->
        </section>


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

    
<script>
    $('.product').each(function() {
        var $this = $(this);
        var hoverDetail = $this.find('.product-hover_details');

        $this.hover(function() {
            hoverDetail.toggleClass('d-none');
        });
    });
</script>
@endpush
