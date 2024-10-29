@extends('layouts.front-end.app')



@section('title', translate($data['data_from']) . ' ' . translate('products'))



@push('css_or_js')
    <meta property="og:image"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo'] }}" />

    <meta property="og:title" content="Products of {{ $web_config['name'] }} " />

    <meta property="og:url" content="{{ env('APP_URL') }}">

    <meta property="og:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">



    <meta property="twitter:card"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo'] }}" />

    <meta property="twitter:title" content="Products of {{ $web_config['name'] }}" />

    <meta property="twitter:url" content="{{ env('APP_URL') }}">

    <meta property="twitter:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">



    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.7.0/nouislider.min.css" rel="stylesheet">





    <style>
        .for-count-value {

            {{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}: 0.6875 rem;

            ;

        }



        .for-count-value {



            {{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}: 0.6875 rem;

        }



        .for-brand-hover:hover {

            color: var(--web-primary);

        }



        .for-hover-label:hover {

            color: var(--web-primary) !important;

        }



        .page-item.active .page-link {

            background-color: var(--web-primary) !important;

        }



        .for-sorting {

            padding- {{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}: 9px;

        }



        .sidepanel {

            {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}: 0;

        }



        .sidepanel .closebtn {

            {{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}: 25 px;

        }



        @media (max-width: 360px) {

            .for-sorting-mobile {

                margin- {{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}: 0% !important;

            }



            .for-mobile {



                margin- {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}: 10% !important;

            }



        }



        @media (max-width: 500px) {

            .for-mobile {



                margin- {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}: 27%;

            }

        }



        .checkbox-wrapper-48 {

            --gray: #636e72;

            --very-light-gray: #eee;

            --light-gray: #9098A9;

            --x-light-gray: #dfe6e9;

            --gradient: linear-gradient(180deg, #1F3C74 0%, #1F3C74 100%);

        }



        .checkbox-wrapper-48 label {

            font-size: 1.35em;

        }



        /* CORE STYLES */

        .checkbox-wrapper-48 input {

            -webkit-appearance: none;

            -moz-appearance: none;

            appearance: none;

            width: 1em;

            height: 1em;

            font: inherit;

            border: 0.1em solid var(--light-gray);

            margin-bottom: -0.125em;
            display: none;
            cursor: pointer;

        }



        /*.checkbox-wrapper-48 input[type=checkbox] {

                                                                        border-radius: 0.25em;

                                                                    }*/



        .checkbox-wrapper-48 input:checked {

            border-color: transparent;

            background: var(--gradient) border-box;

            box-shadow: 0 0 0 0.1em inset #fff;

        }



        .checkbox-wrapper-48 input:not(:checked):hover {

            border-color: transparent;

            background: linear-gradient(#fff, #fff) padding-box, var(--gradient) border-box;

        }



        .categories_text {

            font-size: 16px;

            font-weight: 500;

            line-height: 24px;

            letter-spacing: 0.005em;

            text-align: left;
            cursor: pointer;

        }



        .categories_span {

            color: lightgray;

        }



        .header_categories {

            font-size: 20px;

            font-weight: 600;

            line-height: 24px;

            letter-spacing: 0.0015em;

            text-align: left;

            color: #774EA5;

        }



        .purple-icon {

            color: #774EA5;

        }



        .collapse-button {

            cursor: pointer;

            display: flex;

            align-items: center;

        }



        .collapse-button .fa {

            margin-left: 10px;

        }



        .slider-price-range {

            background-color: #f0f0f0;

            padding: 3px 1rem;

            border-radius: 11px;

            color: #774EA5;

            font-size: 14px;

            font-weight: 500;

            line-height: 20px;

            letter-spacing: 0.0025em;

            text-align: left;

        }



        #priceRange {

            appearance: none;

            /* Standard */

            -webkit-appearance: none;

            /* Safari and Chrome */

            -moz-appearance: none;

            /* Firefox */

            width: 100%;

            height: 8px;

            background: linear-gradient(90deg, #8974F7 0%, #1F3C74 100%);

            border: 1px solid rgba(255, 173, 155, 1);

            border-radius: 5px;

            outline: none;

        }



        #priceRange::-webkit-slider-thumb {

            appearance: none;

            -webkit-appearance: none;

            width: 20px;

            height: 20px;

            background-color: #fff;

            border: 2px solid rgba(255, 173, 155, 1);

            border-radius: 50%;

            cursor: pointer;

        }



        #priceRange::-moz-range-thumb {

            appearance: none;

            -moz-appearance: none;

            width: 20px;

            height: 20px;

            background-color: #fff;

            border: 2px solid rgba(255, 173, 155, 1);

            border-radius: 50%;

            cursor: pointer;

        }



        .custom-checkbox {

            display: none;

            /* Hide default checkbox */

        }



        .custom-checkbox+.form-check-label {

            display: inline-block;

            width: 32px;

            /* Set width and height */

            height: 32px;

            border: none;

            /* Border for square */

            border-radius: 3px;

            /* Optional: rounded corners */

            position: relative;

            cursor: pointer;

        }



        .custom-checkbox:checked+.form-check-label::before {

            content: '';

            position: absolute;

            top: 50%;

            left: 50%;

            width: 12px;

            height: 12px;

            background: white;

            /* Color for check mark */

            border-radius: 2px;

            /* Optional: rounded corners */

            transform: translate(-50%, -50%);

        }



        .custom-checkbox:checked+.form-check-label {

            border-color: transparent;

        }



        .rating_star span img {

            width: 22px;

            margin-bottom: 7px;

        }



        .noUi-target,

        .noUi-connects {

            height: 6px;

        }



        .noUi-horizontal .noUi-handle,

        .noUi-touch-area {

            width: 18.48px;

            height: 18px;

            border-radius: 50%;

        }



        .noUi-touch-area {

            background: #1F3C74;

        }



        .noUi-handle:after,

        .noUi-handle:before {

            background: unset !important;

        }



        .noUi-connect {

            background: linear-gradient(90deg, #8974F7 0%, #1F3C74 100%);

        }



        .noUi-tooltip {

            display: none;

        }



        /* Sorting Products */

        .items-container {

            display: grid;

            gap: 10px;

        }



        /*  */

        .button-group button i {

            /* padding: 10px 20px; */

            margin-right: 10px;

            border: none;

            color: #e0e0e0;

            cursor: pointer;

            border-radius: 5px;

            transition: background-color 0.3s ease;

        }



        .button-group button.active i {

            color: #1F3C74;

        }



        .items-container {

            display: grid;

            gap: 10px;

        }



        /*  */



        .w-20 {

            width: 20px;

        }



        .h-20 {

            height: 20px;

        }



        .sorting-item {

            border-radius: unset !important;

            border: unset !important;

            box-shadow: unset !important;

            background: unset !important;

        }



        /* Sorting Products Ends */
    </style>
@endpush



@section('content')



    @php($decimal_point_settings = getWebConfig(name: 'decimal_point_settings'))



    <div class="container-fluid py-3" dir="{{ Session::get('direction') }}">

        <div class="search-page-header">



            <div class="breadcrumb">

                <a href="{{ url('/') }}">

                    <i class="fa fa-home" aria-hidden="true"></i>

                    Home

                </a>

                <i class="fa fa-angle-right" aria-hidden="true"></i>

                <span>

                    {{ isset($data['brand_name']) ? $data['brand_name'] : '' }}

                </span>

            </div>





            <!-- <div>
                    <h5 class="font-semibold mb-1">{{ translate(str_replace('_', ' ', $data['data_from'])) }} {{ translate('products') }} {{ isset($data['brand_name']) ? '(' . $data['brand_name'] . ')' : '' }}</h5>
                    <div><span class="view-page-item-count">{{ $products->total() }}</span> {{ translate('items_found') }}</div>
                </div> -->



            <div class="d-flex align-items-center gap-3">

                <form id="search-form" class="d-none d-lg-block" action="{{ route('products') }}" method="GET">

                    <input hidden name="data_from" value="{{ $data['data_from'] }}">

                    <div class="sorting-item">

                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                            fill="none">

                            <path d="M11.6667 7.80078L14.1667 5.30078L16.6667 7.80078" stroke="#D9D9D9" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />

                            <path
                                d="M7.91675 4.46875H4.58341C4.3533 4.46875 4.16675 4.6553 4.16675 4.88542V8.21875C4.16675 8.44887 4.3533 8.63542 4.58341 8.63542H7.91675C8.14687 8.63542 8.33341 8.44887 8.33341 8.21875V4.88542C8.33341 4.6553 8.14687 4.46875 7.91675 4.46875Z"
                                stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                            <path
                                d="M7.91675 11.9688H4.58341C4.3533 11.9688 4.16675 12.1553 4.16675 12.3854V15.7188C4.16675 15.9489 4.3533 16.1354 4.58341 16.1354H7.91675C8.14687 16.1354 8.33341 15.9489 8.33341 15.7188V12.3854C8.33341 12.1553 8.14687 11.9688 7.91675 11.9688Z"
                                stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                            <path d="M14.1667 5.30078V15.3008" stroke="#D9D9D9" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />

                        </svg>

                        <label class="for-sorting" for="sorting">

                            <span>{{ translate('sort_by') }}</span>

                        </label>

                        <select class="product-list-filter-on-viewpage">

                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>

                                {{ translate('latest') }}</option>

                            <option value="low-high" {{ request('sort_by') == 'low-high' ? 'selected' : '' }}>

                                {{ translate('low_to_High_Price') }} </option>

                            <option value="high-low" {{ request('sort_by') == 'high-low' ? 'selected' : '' }}>

                                {{ translate('High_to_Low_Price') }}</option>

                            <option value="a-z" {{ request('sort_by') == 'a-z' ? 'selected' : '' }}>

                                {{ translate('A_to_Z_Order') }}</option>

                            <option value="z-a" {{ request('sort_by') == 'z-a' ? 'selected' : '' }}>

                                {{ translate('Z_to_A_Order') }}</option>

                        </select>

                    </div>

                </form>

                <div class="d-lg-none">

                    <div class="filter-show-btn btn btn--primary py-1 px-2 m-0">

                        <i class="tio-filter"></i>

                    </div>

                </div>



                <div class="button-group changeItemPerRow">

                    <button class="btn p-0 mr-2 active" onclick="changeItemsPerRow(1, this)">

                        <i class="fa fa-th-list fa-lg" aria-hidden="true"></i>

                    </button>

                    <button class="btn p-0 mr-2" onclick="changeItemsPerRow(3, this)">

                        <i class="fa fa-th-large fa-lg" aria-hidden="true"></i>

                    </button>

                    <button class="btn p-0 mr-2" onclick="changeItemsPerRow(4, this)">

                        <i class="fa fa-th fa-lg" aria-hidden="true"></i>

                    </button>

                    {{-- <button class="btn p-0" onclick="changeItemsPerRow(4)">

                        <img class="w-20 h-20 p-0" src="{{ asset('public/assets/front-end/img/icons/four-items.png') }}"

                            alt="">

                    </button> --}}

                </div>

            </div>



        </div>



    </div>



    <div class="container-fluid pb-5 mb-2 mb-md-4 rtl __inline-35" dir="{{ Session::get('direction') }}">

        <div class="row">

            <aside
                class="col-lg-3 hidden-xs col-md-3 col-sm-4 SearchParameters __search-sidebar {{ Session::get('direction') === 'rtl' ? 'pl-2' : 'pr-2' }}"
                id="SearchParameters">

                <div class="cz-sidebar __inline-35" id="shop-sidebar">

                    <div class="cz-sidebar-header bg-light">

                        <button class="close ms-auto" type="button" data-dismiss="sidebar" aria-label="Close">

                            <i class="tio-clear"></i>

                        </button>

                    </div>



                    <!-- Categories -->

                    <div class="form-group">

                        <!-- Container for button and icon -->

                        <div class="d-flex justify-content-between align-items-center">

                            <button class="btn" type="button" data-toggle="collapse" data-target="#checkboxCollapse"
                                aria-expanded="true" aria-controls="checkboxCollapse">

                                <h4 class="header_categories">{{ translate('categories') }}</h4>

                            </button>

                            <span id="icon1" class="fa fa-minus collapse-button"></span>

                        </div>



                        <!-- Collapsible content (collapsed by default) -->

                        <div class="collapse show" id="checkboxCollapse">

                            @php($categories = \App\Utils\CategoryManager::get_categories_with_counting())

                            <div class="card card-body border-0 py-0">

                                @foreach ($categories as $category)
                                    <div class="checkbox-wrapper-48">

                                        <label class="d-flex align-items-center">

                                            <input type="radio" class="mr-2 category-checkbox" name="categories"
                                                value="{{ $category['id'] }}"  {{ request('id') == $category['id'] ? 'checked' : '' }}>

                                            <p class="m-0 categories_text">{{ $category['name'] }}

                                                <span class="categories_span">

                                                    {{-- (10) --}}



                                                    ({{ $category->products_count }})
                                                    <!-- Display parent category product count -->



                                                </span>

                                            </p>

                                        </label>

                                    </div>
                                @endforeach

                            </div>

                        </div>

                    </div>

                    <!-- Categories Ends -->



                    <hr>



                    <!-- Budget -->

                    <div class="form-group mb-4">

                        <div class="d-flex justify-content-between align-items-center">

                            <button class="btn" type="button" data-toggle="collapse" data-target="#sliderCollapse"
                                aria-expanded="true" aria-controls="sliderCollapse">

                                <h4 class="header_categories">Budget</h4>

                            </button>

                            <span id="icon2" class="fa fa-minus collapse-button purple-icon"></span>

                        </div>

                        <div class="collapse show" id="sliderCollapse">

                            <div class="card card-body border-0 p-0">

                                <div class="container-fluid slider-container">

                                    <div class="slider-labels d-flex justify-content-between">

                                        <span class="slider-price-range">Min: {{ $defaultCurrencies->symbol }}<span
                                                id="minPrice">10</span></span>

                                        <span class="slider-price-range">Max: {{ $defaultCurrencies->symbol }}<span
                                                id="maxPrice">10000</span></span>

                                    </div>

                                    <div id="priceSlider" class="mt-4"></div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Budget Ends -->



                    <!-- Brand -->

                    {{-- <ul id="lista1" class="__brands-cate-wrap" data-simplebar data-simplebar-auto-hide="false">

                        @foreach (\App\Utils\BrandManager::get_active_brands() as $brand)

                            <div class="brand mt-2 for-brand-hover {{ Session::get('direction') === 'rtl' ? 'mr-2' : '' }}"

                                id="brand">

                                <li class="flex-between __inline-39 get-view-by-onclick"

                                    data-link="{{ route('products', ['id' => $brand['id'], 'data_from' => 'brand', 'page' => 1]) }}">

                                    <div class="text-start">

                                        {{ $brand['name'] }}

                                    </div>

                                    <div class="__brands-cate-badge">

                                        <span>

                                            {{ $brand['brand_products_count'] }}

                                        </span>

                                    </div>

                                </li>

                            </div>

                        @endforeach

                    </ul> --}}



                    <!-- Brand Collapsible Section -->

                    <div class="form-group mt-4">

                        <div class="d-flex justify-content-between align-items-center">

                            <button class="btn" type="button" data-toggle="collapse" data-target="#brandCollapse"
                                aria-expanded="false" aria-controls="brandCollapse">

                                <h4 class="header_categories">Brand</h4>

                            </button>

                            <span id="icon3" class="fa fa-plus collapse-button purple-icon"></span>

                        </div>

                        <div class="collapse show" id="brandCollapse">

                            <div class="card card-body border-0 py-1">

                                <!-- Initial set of brands -->

                                @foreach (\App\Utils\BrandManager::get_active_brands()->take(4) as $brand)
                                    <div class="checkbox-wrapper-48">

                                        <label class="d-flex align-items-center">

                                            <!-- Checkbox for each brand -->

                                            <input type="checkbox" class="mr-2 brand-checkbox"
                                                value="{{ $brand['id'] }}" name="buy_phone">

                                            <p class="m-0 categories_text">

                                                {{ $brand['name'] }}

                                                <span class="categories_span">

                                                    ({{ $brand['brand_products_count'] }})
                                                </span>

                                            </p>

                                        </label>

                                    </div>
                                @endforeach



                                <!-- Show More Starts -->

                                <div class="form-group mt-2">

                                    <div class="d-flex align-items-center">

                                        <a class="show-more-link" style="text-decoration: underline; color: #1F3C74;"
                                            type="button" data-toggle="collapse" data-target="#moreCollapse"
                                            aria-expanded="false" aria-controls="moreCollapse">

                                            Show More

                                        </a>

                                    </div>

                                    <div class="collapse mt-3" id="moreCollapse">

                                        <div class="card card-body border-0 p-0">

                                            <!-- All remaining brands -->

                                            @foreach (\App\Utils\BrandManager::get_active_brands()->skip(4) as $brand)
                                                <div class="checkbox-wrapper-48">

                                                    <label class="d-flex align-items-center">

                                                        <input type="checkbox" class="mr-2 brand-checkbox"
                                                            value="{{ $brand['id'] }}" name="buy_phone">

                                                        <p class="m-0 categories_text">

                                                            {{ $brand['name'] }}

                                                            <span class="categories_span">

                                                                ({{ $brand['brand_products_count'] }})
                                                            </span>

                                                        </p>

                                                    </label>

                                                </div>
                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                                <!-- Show More Ends -->

                            </div>

                        </div>

                    </div>

                    <!-- Brand Collapsible Section Ends -->

                    <hr>

                    <!-- Brand Ends -->



                    <!-- Color Collapsible Section -->

                    <div class="form-group">

                        <div class="d-flex justify-content-between align-items-center">

                            <button class="btn" type="button" data-toggle="collapse" data-target="#colorCollapse"
                                aria-expanded="false" aria-controls="colorCollapse">

                                <h4 class="header_categories">Color</h4>

                            </button>

                            <span id="icon5" class="fa fa-plus collapse-button purple-icon"></span>

                        </div>

                        <div class="collapse show mt-3" id="colorCollapse">

                            <div class="d-flex flex-wrap border-0 p-0">

                                <!-- Initial 10 colors -->

                                @foreach ($colors as $color)
                                    <div class="form-check color-item">

                                        <input class="form-check-input custom-checkbox" type="checkbox"
                                            value="{{ ucfirst($color) }}" id="{{ ucfirst($color) }}"
                                            name="colorFilter">

                                        <label class="form-check-label" for="{{ ucfirst($color) }}"
                                            style="background-color:{{ $color }};">

                                        </label>

                                    </div>
                                @endforeach

                            </div>



                        </div>

                    </div>

                    <!-- Color Collapsible Section Ends -->



                    <hr>



                    <!-- Internal Storage Section -->

                    {{-- <div class="form-group">

                        <div class="d-flex justify-content-between align-items-center">

                            <button class="btn" type="button" data-toggle="collapse" data-target="#internalstorage"

                                aria-expanded="false" aria-controls="internalstorage">

                                <h4 class="header_categories">Internal Storage</h4>

                            </button>

                            <span id="icon6" class="fa fa-plus collapse-button purple-icon"></span>

                        </div>

                        <div class="collapse show " id="internalstorage">

                            <div class="card card-body border-0  py-1">

                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">16 GB

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">32 GB

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">64 GB

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">128 GB

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">256 GB

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">512 GB

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">1 TB

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>

                            </div>

                        </div>

                    </div> --}}

                    <!-- Internal Storage Section Ends -->



                    <hr>



                    <!-- Condition Section -->

                    {{-- <div class="form-group">

                        <div class="d-flex justify-content-between align-items-center">

                            <button class="btn" type="button" data-toggle="collapse" data-target="#condition"

                                aria-expanded="false" aria-controls="condition">

                                <h4 class="header_categories">Condition</h4>

                            </button>

                            <span id="icon7" class="fa fa-plus collapse-button purple-icon"></span>

                        </div>

                        <div class="collapse show " id="condition">

                            <div class="card card-body border-0  py-1">

                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">New

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">Refurbished

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">Used

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="buy_phone">

                                        <p class="m-0 categories_text">Open Box

                                            <span class="categories_span">(10)</span>

                                        </p>

                                    </label>

                                </div>



                            </div>

                        </div>

                    </div> --}}

                    <!-- Condition Section Ends -->



                    <hr>



                    {{-- <!-- Rating Section -->

                    <div class="form-group">

                        <div class="d-flex justify-content-between align-items-center">

                            <button class="btn" type="button" data-toggle="collapse" data-target="#rating"

                                aria-expanded="false" aria-controls="rating">

                                <h4 class="header_categories">Rating</h4>

                            </button>

                            <span id="icon8" class="fa fa-plus collapse-button purple-icon"></span>

                        </div>

                        <div class="collapse show " id="rating">

                            <div class="card card-body border-0 py-1">

                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="rating" value="1">

                                        <div class="rating_star">

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>



                                           

                                        </div>



                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="rating" value="2">

                                        <div class="rating_star">

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                           

                                        </div>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="rating" value="3">

                                        <div class="rating_star">

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                            

                                        </div>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="rating" value="4">

                                        <div class="rating_star">

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/whiteStar.jpg') }}" alt="Star"></span>

                                           

                                        </div>

                                    </label>

                                </div>



                                <div class="checkbox-wrapper-48">

                                    <label class="d-flex align-items-center">

                                        <input type="checkbox" class="mr-2 " name="rating" value="5">

                                        <div class="rating_star">

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                            <span><img src="{{ asset('public/images/yellowStar.jpg') }}" alt="Star"></span>

                                           

                                        </div>

                                    </label>

                                </div>



                            </div>

                        </div>

                    </div>

                    <!-- Rating Section Ends --> --}}



                    <!-- Rating Section -->

                    <div class="form-group">

                        <div class="d-flex justify-content-between align-items-center">

                            <button class="btn" type="button" data-toggle="collapse" data-target="#rating"
                                aria-expanded="false" aria-controls="rating">

                                <h4 class="header_categories">Rating</h4>

                            </button>

                            <span id="icon8" class="fa fa-plus collapse-button purple-icon"></span>

                        </div>

                        <div class="collapse show" id="rating">

                            <div class="card card-body border-0 py-1">

                                <!-- Rating Checkbox List -->

                                @for ($i = 1; $i <= 5; $i++)

                                    <div class="checkbox-wrapper-48">

                                        <label class="d-flex align-items-center">

                                            <input type="checkbox" class="mr-2 rating-filter" name="rating"
                                                value="{{ $i }}">

                                            <div class="rating_star">

                                                @for ($j = 1; $j <= 5; $j++)
                                                    <span>

                                                        <img src="{{ asset($j <= $i ? 'public/images/yellowStar.jpg' : 'public/images/whiteStar.jpg') }}"
                                                            alt="Star">

                                                    </span>
                                                @endfor

                                            </div>

                                        </label>

                                    </div>

                                @endfor

                            </div>

                        </div>

                    </div>

                    <!-- Rating Section Ends -->







                    <!-- Filter -->

                    {{-- <div class="pb-0">

                        <div class="text-center">

                            <div class="__cate-side-title border-bottom">

                                <span class="widget-title font-semibold">{{ translate('filter') }} </span>

                            </div>

                            <div class="__p-25-10 w-100 pt-4">

                                <label class="w-100 opacity-75 text-nowrap for-sorting d-block mb-0 ps-0" for="sorting">

                                    <select class="form-control custom-select" id="searchByFilterValue">

                                        <option selected disabled>{{ translate('choose') }}</option>

                                        <option

                                            value="{{ route('products', ['id' => $data['id'], 'data_from' => 'best-selling', 'page' => 1]) }}"

                                            {{ isset($data['data_from']) != null ? ($data['data_from'] == 'best-selling' ? 'selected' : '') : '' }}>

                                            {{ translate('best_selling_product') }}</option>

                                        <option

                                            value="{{ route('products', ['id' => $data['id'], 'data_from' => 'top-rated', 'page' => 1]) }}"

                                            {{ isset($data['data_from']) != null ? ($data['data_from'] == 'top-rated' ? 'selected' : '') : '' }}>

                                            {{ translate('top_rated') }}</option>

                                        <option

                                            value="{{ route('products', ['id' => $data['id'], 'data_from' => 'most-favorite', 'page' => 1]) }}"

                                            {{ isset($data['data_from']) != null ? ($data['data_from'] == 'most-favorite' ? 'selected' : '') : '' }}>

                                            {{ translate('most_favorite') }}</option>

                                        <option

                                            value="{{ route('products', ['id' => $data['id'], 'data_from' => 'featured_deal', 'page' => 1]) }}"

                                            {{ isset($data['data_from']) != null ? ($data['data_from'] == 'featured_deal' ? 'selected' : '') : '' }}>

                                            {{ translate('featured_deal') }}</option>

                                    </select>

                                </label>

                            </div>



                            <div class="__p-25-10 w-100 pt-0 d-lg-none">

                                <form id="search-form" action="{{ route('products') }}" method="GET">

                                    <input hidden name="data_from" value="{{ $data['data_from'] }}">

                                    <select class="form-control product-list-filter-on-viewpage">

                                        <option value="latest">{{ translate('latest') }}</option>

                                        <option value="low-high">{{ translate('low_to_High_Price') }} </option>

                                        <option value="high-low">{{ translate('High_to_Low_Price') }}</option>

                                        <option value="a-z">{{ translate('A_to_Z_Order') }}</option>

                                        <option value="z-a">{{ translate('Z_to_A_Order') }}</option>

                                    </select>

                                </form>

                            </div>

                        </div>

                    </div> --}}



                    {{-- <div>

                        <div class="text-center">

                            <div class="__cate-side-title pt-0">

                                <span class="widget-title font-semibold">{{ translate('price') }} </span>

                            </div>



                            <div class="d-flex justify-content-between align-items-center __cate-side-price">

                                <div class="__w-35p">

                                    <input

                                        class="bg-white cz-filter-search form-control form-control-sm appended-form-control"

                                        type="number" value="0" min="0" max="1000000" id="min_price"

                                        placeholder="{{ translate('min') }}">



                                </div>

                                <div class="__w-10p">

                                    <p class="m-0">{{ translate('to') }}</p>

                                </div>

                                <div class="__w-35p">

                                    <input value="100" min="100" max="1000000"

                                        class="bg-white cz-filter-search form-control form-control-sm appended-form-control"

                                        type="number" id="max_price" placeholder="{{ translate('max') }}">



                                </div>



                                <div class="d-flex justify-content-center align-items-center __number-filter-btn">



                                    <a class="action-search-products-by-price">

                                        <i

                                            class="__inline-37 czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}"></i>

                                    </a>



                                </div>

                            </div>

                        </div>

                    </div> --}}



                    {{-- @if ($web_config['brand_setting'])

                        <div>

                            <div class="text-center">

                                <div class="__cate-side-title">

                                    <span class="widget-title font-semibold">{{ translate('brands') }}</span>

                                </div>

                                <div class="__cate-side-price pb-3">

                                    <div class="input-group-overlay input-group-sm">

                                        <input placeholder="{{ translate('search_by_brands') }}"

                                            class="__inline-38 cz-filter-search form-control form-control-sm appended-form-control"

                                            type="text" id="search-brand">

                                        <div class="input-group-append-overlay">

                                            <span class="input-group-text">

                                                <i class="czi-search"></i>

                                            </span>

                                        </div>

                                    </div>

                                </div>

                                <ul id="lista1" class="__brands-cate-wrap" data-simplebar

                                    data-simplebar-auto-hide="false">

                                    @foreach (\App\Utils\BrandManager::get_active_brands() as $brand)

                                        <div class="brand mt-2 for-brand-hover {{ Session::get('direction') === 'rtl' ? 'mr-2' : '' }}"

                                            id="brand">

                                            <li class="flex-between __inline-39 get-view-by-onclick"

                                                data-link="{{ route('products', ['id' => $brand['id'], 'data_from' => 'brand', 'page' => 1]) }}">

                                                <div class="text-start">

                                                    {{ $brand['name'] }}

                                                </div>

                                                <div class="__brands-cate-badge">

                                                    <span>

                                                        {{ $brand['brand_products_count'] }}

                                                    </span>

                                                </div>

                                            </li>

                                        </div>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    @endif --}}



                    {{-- <div class="mt-3 __cate-side-arrordion">

                        <div>

                            <div class="text-center __cate-side-title">

                                <span class="widget-title font-semibold">{{ translate('categories') }}</span>

                            </div>

                            @php($categories = \App\Utils\CategoryManager::parents())

                            <div class="accordion mt-n1 __cate-side-price" id="shop-categories">

                                @foreach ($categories as $category)

                                    <div class="menu--caret-accordion">

                                        <div class="card-header flex-between">

                                            <div>

                                                <label class="for-hover-label cursor-pointer get-view-by-onclick"

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

                                        <div class="card-body p-0 ms-2 d--none" id="collapse-{{ $category['id'] }}">

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

                    </div> --}}

                </div>



            </aside>



            <section class="col-lg-9">



                {{-- <div class="top-right-img">

                    <img src="{{ asset('public/assets/front-end/img/media/top_right.png') }}" width="100%"

                        alt="">

                </div> --}}

                {{-- <div class="row" id="ajax-products"> --}}

                <div class="items-container d-grid" id="ajax-products">





                    @include('web-views.products._ajax-products', [
                        'products' => $products,
                    
                        'decimal_point_settings' => $decimal_point_settings,
                    ])

                </div>

            </section>



        </div>

    </div>



    <span id="products-search-data-backup" data-url="{{ url('/products') }}" data-id="{{ $data['id'] }}"
        data-name="{{ $data['name'] }}" data-from="{{ $data['data_from'] }}" data-sort="{{ $data['sort_by'] }}"
        data-min-price="{{ $data['min_price'] }}" data-max-price="{{ $data['max_price'] }}"
        data-message="{{ translate('items_found') }}"></span>

       <!-- Compare Limit Message -->

    <div id="compare-limit-message" style="display:none;">

        <span id="compare-message-text">You have already selected 4 products.</span>

        <button class="btn" id="clear-compare-items">Clear Compare Items</button>

    </div>



    <!-- Compare Count Display -->

    <div class="compare-count" style="display:none;">

        <a href="{{ url('compare/product') }}">

            Compare <span id="compare-product-count">0</span>

        </a>

   </div>

@endsection



@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/product-view.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.7.0/nouislider.min.js"></script>



    <script>
        $(document).ready(function() {

            // Toggle icons for the first collapsible section

            $('#checkboxCollapse').on('show.bs.collapse', function() {

                $('#icon1').removeClass('fa-plus').addClass('fa-minus');

            }).on('hide.bs.collapse', function() {

                $('#icon1').removeClass('fa-minus').addClass('fa-plus');

            });



            $('#icon1').on('click', function() {

                $('#checkboxCollapse').collapse('toggle');

            });



            // Toggle icons for the second collapsible section (Budget Slider)

            $('#sliderCollapse').on('show.bs.collapse', function() {

                $('#icon2').removeClass('fa-plus').addClass('fa-minus');

            }).on('hide.bs.collapse', function() {

                $('#icon2').removeClass('fa-minus').addClass('fa-plus');

            });



            $('#icon2').on('click', function() {

                $('#sliderCollapse').collapse('toggle');

            });



            // Toggle icons for the third collapsible section (Brand Selection)

            $('#brandCollapse').on('show.bs.collapse', function() {

                $('#icon3').removeClass('fa-plus').addClass('fa-minus');

            }).on('hide.bs.collapse', function() {

                $('#icon3').removeClass('fa-minus').addClass('fa-plus');

            });



            $('#icon3').on('click', function() {

                $('#brandCollapse').collapse('toggle');

            });



            // Toggle icons for the fourth collapsible section (Show More)

            $('#moreCollapse').on('show.bs.collapse', function() {

                $('#icon4').removeClass('fa-plus').addClass('fa-minus');

            }).on('hide.bs.collapse', function() {

                $('#icon4').removeClass('fa-minus').addClass('fa-plus');

            });



            $('#icon4').on('click', function() {

                $('#moreCollapse').collapse('toggle');

            });



            // Toggle icons for the fifth collapsible section (Show More)

            $('#colorCollapse').on('show.bs.collapse', function() {

                $('#icon5').removeClass('fa-plus').addClass('fa-minus');

            }).on('hide.bs.collapse', function() {

                $('#icon5').removeClass('fa-minus').addClass('fa-plus');

            });



            $('#icon5').on('click', function() {

                $('#colorCollapse').collapse('toggle');

            });



            // Toggle icons for the fifth collapsible section (Show More)

            $('#internalstorage').on('show.bs.collapse', function() {

                $('#icon6').removeClass('fa-plus').addClass('fa-minus');

            }).on('hide.bs.collapse', function() {

                $('#icon6').removeClass('fa-minus').addClass('fa-plus');

            });



            $('#icon6').on('click', function() {

                $('#internalstorage').collapse('toggle');

            });



            // Toggle icons for the sixth collapsible section (Show More)

            $('#condition').on('show.bs.collapse', function() {

                $('#icon7').removeClass('fa-plus').addClass('fa-minus');

            }).on('hide.bs.collapse', function() {

                $('#icon7').removeClass('fa-minus').addClass('fa-plus');

            });



            $('#icon7').on('click', function() {

                $('#condition').collapse('toggle');

            });



            // Toggle icons for the seventh collapsible section (Show More)

            $('#rating').on('show.bs.collapse', function() {

                $('#icon8').removeClass('fa-plus').addClass('fa-minus');

            }).on('hide.bs.collapse', function() {

                $('#icon8').removeClass('fa-minus').addClass('fa-plus');

            });



            $('#icon8').on('click', function() {

                $('#rating').collapse('toggle');

            });



            // Update price range value dynamically

            // $('#priceRange').on('input', function() {

            //     $('#priceValue').text(`$${$(this).val()}`);

            // });



            // Budget

            // var priceSlider = document.getElementById('priceSlider');



            // noUiSlider.create(priceSlider, {

            //     start: [10, 10000], // Initial values for handles

            //     connect: true,

            //     range: {

            //         'min': 10,

            //         'max': 10000

            //     },

            //     step: 1,

            //     tooltips: [true, true],

            //     format: {

            //         to: function(value) {

            //             return Math.round(value);

            //         },

            //         from: function(value) {

            //             return Number(value);

            //         }

            //     }

            // });



            // priceSlider.noUiSlider.on('update', function(values, handle) {

            //     if (handle === 0) {

            //         document.getElementById('minPrice').innerText = values[0];

            //     } else {

            //         document.getElementById('maxPrice').innerText = values[1];

            //     }

            // });



            // Budget Ends

        });



        // Sorting items

        function changeItemsPerRow(numItems, element) {

            const container = document.querySelector('.items-container');

            container.style.gridTemplateColumns = `repeat(${numItems}, 1fr)`;



            // Ensure all items inside the product list get the correct class

            const items = document.querySelectorAll('.product-single-hover .muntiple-designs');

            items.forEach(item => {

                item.classList.remove('design-1', 'design-2');

                if (numItems === 1) {

                    item.classList.add('design-1');

                } else {

                    item.classList.add('design-2');

                }

            });



            // Remove the 'active' class from all buttons

            const buttons = document.querySelectorAll('.button-group button');

            buttons.forEach(button => button.classList.remove('active'));



            // Add the 'active' class to the clicked button

            element.classList.add('active');

        }

        // Sorting items Ends
    </script>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <script>
        $(document).ready(function() {

            $('.category-checkbox').on('change', function() {

                // Collect all selected category IDs

                let selectedCategories = [];



                $('.category-checkbox:checked').each(function() {

                    selectedCategories.push($(this).val());

                });



                // Make AJAX request to filter products

                $.ajax({

                    url: "{{ route('products.filter') }}", // Ensure this route is correct

                    type: 'GET',

                    data: {

                        categories: selectedCategories

                    },

                    success: function(response) {

                        // Replace the content of the items-container with the response HTML

                        $('#ajax-products').html(response.data);

                    },

                    error: function(xhr, status, error) {

                        // console.error('Error filtering products:', xhr.responseText);

                        // // Display a user-friendly error message

                        // alert('An error occurred while filtering products. Please try again.');

                    }

                });

            });



        });
    </script>

    {{-- brand --}}

    <script>
        $(document).ready(function() {

            // Event listener for any change in checkboxes

            $('.brand-checkbox').on('change', function() {

                // Initialize the array for selected brands

                let selectedBrand = [];



                // Collect all selected brand IDs

                $('.brand-checkbox:checked').each(function() {

                    selectedBrand.push($(this).val());

                });





                // Debugging: Display the selectedBrand array to verify the IDs

                console.log('Selected Brand IDs: ', selectedBrand); // Check the console log for the IDs



                // Make AJAX request to filter products based on selected brands

                $.ajax({

                    url: "{{ route('brand.filter') }}", // Ensure this route is correctly defined

                    type: 'GET',

                    data: {

                        brands: selectedBrand // Send the selected brand IDs to the server

                    },

                    success: function(response) {

                        // Update the products display with the filtered products

                        $('#ajax-products').html(response.data);

                    },

                    error: function(xhr, status, error) {

                        // console.error('Error filtering products:', xhr.responseText);

                        // // Notify the user of an error

                        // alert('An error occurred while filtering products. Please try again.');

                    }

                });

            });

        });
    </script>



    {{-- product filter by  price range --}}

    <script>
        // Initialize the noUiSlider

        var priceSlider = document.getElementById('priceSlider');



        noUiSlider.create(priceSlider, {

            start: [10, 10000],

            connect: true,

            range: {

                'min': 0,

                'max': 10000

            },

            step: 1,

            tooltips: [true, true],

            format: {

                to: function(value) {

                    return Math.round(value); // Round to the nearest whole number

                },

                from: function(value) {

                    return Number(value); // Convert string to number

                }

            }

        });



        // Update the min and max values displayed

        priceSlider.noUiSlider.on('update', function(values, handle) {

            document.getElementById('minPrice').textContent = values[0];

            document.getElementById('maxPrice').textContent = values[1];

        });



        // Trigger filtering when the slider values change

        priceSlider.noUiSlider.on('change', function(values, handle) {

            filterProducts(values[0], values[1]);

        });



        // Function to filter products

        function filterProducts(minPrice, maxPrice) {

            $.ajax({

                url: '{{ route('price.filter') }}', // Define this route in your routes/web.php

                method: 'GET',

                data: {

                    min_price: minPrice,

                    max_price: maxPrice

                },

                success: function(response) {

                    // Update the products display with the filtered products

                    $('#ajax-products').html(response.data);
                    // console.log(response);

                },
                error: function(xhr, status, error) {

                    console.error('Error filtering products:', xhr.responseText);

                }

            });

        }



        //New Ajax for filter

        // function filterProducts(filterParams) {

        //     $.ajax({

        //         url: '/filter-products', // Change to your actual filtering URL

        //         method: 'GET',

        //         data: filterParams,

        //         success: function (response) {

        //             // Replace the product list with the filtered products

        //             $('#product-list-container').html(response);



        //             // Reinitialize functionality after filtering

        //             reinitializeFunctionalities();

        //         },

        //         error: function () {

        //             console.log('Error filtering products.');

        //         }

        //     });

        // }



        // function reinitializeAddToCart() {

        //     $('.action-add-to-cart-form').on('submit', function (e) {

        //         e.preventDefault();

        //         let form_id = $(this).attr('id');

        //         addToCart(form_id);

        //     });

        // }



        // function reinitializeWishlist() {

        //     $('.product-action-add-wishlist').on('click', function () {

        //         let id = $(this).data('product-id');

        //         addWishlist(id);

        //     });

        // }



        // function reinitializeCompare() {

        //     $('.action-product-compare').on('click', function () {

        //         let id = $(this).data('product-id');

        //         addToCompare(id);

        //     });

        // }



        // $(document).on('click', '.action-product-quick-view', function () {

        //     let productId = $(this).data('product-id');

        //     productQuickView(productId);

        // });



        // function renderCompareFunction() {

        //     $('.action-product-compare').off('click').on('click', function () {

        //         const productId = $(this).data('product-id');

        //         const index = selectedProducts.indexOf(productId);



        //         if (index === -1) {

        //             if (selectedProducts.length < maxItems) {

        //                 selectedProducts.push(productId);

        //             } else {

        //                 $('#compare-limit-message').show();

        //                 $('#compare-message-text').text('You have already selected 4 products.');



        //                 clearTimeout(messageTimeout);

        //                 messageTimeout = setTimeout(() => {

        //                     $('#compare-message-text').text('');

        //                 }, 10000);

        //             }

        //         } else {

        //             selectedProducts.splice(index, 1);

        //         }



        //         updateCompareCount();

        //         saveSelectedProducts();

        //     });

        // }



        function reinitializeFunctionalities() {

            // Clear previous event handlers to avoid duplication

            $(document).off('click', '.action-add-to-cart-form-re-init');

            $(document).off('click', '.product-action-add-wishlist');

            $(document).off('click', '.action-product-compare');

            $(document).off('click', '.action-product-quick-view');



            // Attach add-to-cart event to dynamically loaded elements

            $(document).on('click', '.action-add-to-cart-form-re-init', function(e) {

                e.preventDefault();

                // Correct way to get the product id from data-product-id attribute
                let productId = $(this).data('product-id');

                // Alert the correct product ID
                //    alert("hello");

                addToCart(productId);

            });

            // bibek added
            function addToCart(productId) {
                // alert(productId);


                // Set up the AJAX request
                $.ajax({
                    url: "{{ route('cart.add') }}", // Add to cart route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Pass CSRF token
                        id: productId, // Pass the product ID
                        quantity: 1 // You can change the quantity if needed
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            // console.log(response.cart);
                            toastr.success(response.message);
                            // alert('Product added to cart successfully!'); // You can replace with Toast message or a cart update
                            // You can update your cart UI here, for example, update cart counter, etc.
                            // Reload the page after 1 second
                            setTimeout(function() {
                                location.reload();
                            }, 1000); // 1000ms = 1 second delay before reload

                        } else {
                            // alert('Failed to add product to cart!');
                            toastr.error('Something went wrong, please try again.');
                        }
                    },
                    error: function(response) {
                        // alert('Error occurred while adding to cart!');
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }








            // Attach wishlist event to dynamically loaded elements

            $(document).on('click', '.product-action-add-wishlist', function() {

                let id = $(this).data('product-id');

                addWishlist(id);

            });



            // Attach compare event to dynamically loaded elements

            // $(document).on('click', '.action-product-compare', function () {

            //     let id = $(this).data('product-id');


            //     handleCompareClick(id); // Reuse the comparison handling logic

            // });









            // Attach quick view event to dynamically loaded elements

            $(document).on('click', '.action-product-quick-view', function() {

                let productId = $(this).data('product-id');

                productQuickView(productId);

            });

        }



        // Call this function initially after page load if needed

        $(document).ready(function() {

            reinitializeFunctionalities();

        });
    </script>

    <script>
        $(document).ready(function() {

            // Function to handle checkbox changes

            $('.rating-filter').change(function() {

                // Get all checked checkboxes

                var selectedRatings = [];



                $('.rating-filter:checked').each(function() {

                    selectedRatings.push($(this).val());

                });



                // Make AJAX request to filter data

                $.ajax({

                    url: "{{ route('filter.ratings') }}", // Your route to handle the request

                    method: "GET",

                    data: {

                        ratings: selectedRatings

                    },

                    success: function(response) {

                        // Update the products display with the filtered products

                        $('#ajax-products').html(response.data);

                        // console.log(response);



                    },

                    error: function(xhr, status, error) {

                        // console.error('Error filtering products:', xhr.responseText);

                        // // Notify the user of an error

                        // alert('An error occurred while filtering products. Please try again.');

                    }

                });

            });

        });
    </script>





    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const showMoreBtn = document.getElementById('showMoreBtn');

            const moreColors = document.getElementById('moreColors');



            showMoreBtn.addEventListener('click', function() {

                if (moreColors.style.display === 'none') {

                    moreColors.style.display = 'flex'; // or 'block'

                    showMoreBtn.textContent = 'Show Less';

                } else {

                    moreColors.style.display = 'none';

                    showMoreBtn.textContent = 'Show More';

                }

            });

        });
    </script>



    <script>
        $(document).ready(function() {

            function getSelectedColors() {

                var selectedColors = [];

                $('.form-check-input:checked').each(function() {

                    selectedColors.push($(this).val());

                });

                return selectedColors;

            }



            function updateProducts() {

                var selectedColors = getSelectedColors();



                // Debugging: Uncomment to see selected colors in alert

                // alert(selectedColors);



                $.ajax({

                    url: "{{ route('filter.ColorWise') }}",

                    method: 'get',

                    data: {

                        colors: selectedColors,

                        _token: $('meta[name="csrf-token"]').attr('content')

                    },

                    success: function(response) {

                        // Update the products display with the filtered products

                        $('#ajax-products').html(response.data);

                        // console.log(response);



                    },

                    error: function(xhr, status, error) {

                        // console.error('Error filtering products:', xhr.responseText);

                        // // Notify the user of an error

                        // alert('An error occurred while filtering products. Please try again.');

                    }

                });

            }



            $('.form-check-input').on('change', function() {

                updateProducts();

            });

        });
    </script>
@endpush
