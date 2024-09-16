@extends('layouts.front-end.app')

@section('title', 'Product Compare')

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/product-details.css') }}" />
@endpush

@section('content')
    <!-- Bread Crumb -->
    <div class="braed-crumbs">
        <div class="container-fluid">
            <span>
                <a href="{{ url('/') }}" class="home">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    Home
                </a>
            </span>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
            <span class="text-capitalize product">Product Compare</span>
        </div>
    </div>
    <!-- Bread Crumb Ends -->

    <section class="compare-section">
        <div class="product-compare-section container">

            <div>
                <div class="product-compare-table">
                    <div class="compare-product_item">
                        <img src="https://rukminim2.flixcart.com/image/184/184/xif0q/mobile/n/l/u/-original-imah2fjd7wfd9ksh.jpeg?q=90"
                            alt="">
                        <div class="product-compart-title my-2">
                            <a href="#">
                                Motorola G85 5G (Olive Green)
                            </a>
                        </div>
                        <div class="compare-product-price d-flex flex-wrap align-items-center gap-8 my-2">
                            <span class="text-accent text-dark">
                                Rs.190.00
                                <del class="compare-discount-product-price">
                                    Rs.200.00
                                </del>
                            </span>
                        </div>

                    </div>
                    <div class="product-compare-description">
                        <div class="product-compare-rating my-3">
                            <div class="compare-stars">
                                4.4
                                <i class="fa fa-star text-white" aria-hidden="true"></i>
                            </div>
                            <div class="compare-review-count">
                                <a href="#" class="">1,200+ Reviews</a>
                            </div>
                        </div>

                        <div class="product-compare-specification my-3">
                            <span>
                                8 GB RAM | 128 GB ROM
                                16.94 cm (6.67 inch) Full HD+ Display
                                50MP + 8MP | 32MP Front Camera
                                5000 mAh Battery
                                6s Gen 3 Processor
                                Warranty: 1 Year on Handset and 6 Months on Accessories
                                Returns: 7 Days Replacement Policy
                            </span>
                        </div>

                        <div class="product-compare-variants my-2">
                            <p class="varients-title m-0">
                                Color
                            </p>
                            <p class="varients-content m-0">
                                Cobalt Blue, Olive Green, Urban Grey
                            </p>
                        </div>

                        <div class="product-compare-variants my-2">
                            <p class="varients-title m-0">
                                Color
                            </p>
                            <p class="varients-content m-0">
                                Cobalt Blue, Olive Green, Urban Grey
                            </p>
                        </div>

                        <div class="product-compare-variants my-2">
                            <p class="varients-title m-0">
                                Color
                            </p>
                            <p class="varients-content m-0">
                                Cobalt Blue, Olive Green, Urban Grey
                            </p>
                        </div>

                        <div class="compare-buynow my-3">
                            <button class="btn" type="button" data-update-text="Update cart"
                                data-add-text="Add to cart">
                                <i class="czi-cart text-white me-2"></i>
                                <span class="string-limit text-white">Add to cart</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="product-compare-table">
                    <div class="compare-product_item">
                        <img src="https://rukminim2.flixcart.com/image/184/184/xif0q/mobile/n/l/u/-original-imah2fjd7wfd9ksh.jpeg?q=90"
                            alt="">
                        <div class="product-compart-title">
                            <a href="#">
                                vivo T3 Lite 5G (Majestic Black, 128 GB)
                            </a>
                        </div>
                        <div class="compare-product-price d-flex flex-wrap align-items-center gap-8">
                            <span class="text-accent text-dark">
                                Rs.190.00
                                <del class="compare-discount-product-price">
                                    Rs.200.00
                                </del>
                            </span>
                        </div>

                    </div>
                    <div class="product-compare-description">
                        <div class="product-compare-rating my-3">
                            <div class="compare-stars">
                                4.4
                                <i class="fa fa-star text-white" aria-hidden="true"></i>
                            </div>
                            <div class="compare-review-count">
                                <a href="#" class="">1,200+ Reviews</a>
                            </div>
                        </div>

                        <div class="product-compare-specification my-3">
                            <span>
                                8 GB RAM | 128 GB ROM
                                16.94 cm (6.67 inch) Full HD+ Display
                                50MP + 8MP | 32MP Front Camera
                                5000 mAh Battery
                                6s Gen 3 Processor
                                Warranty: 1 Year on Handset and 6 Months on Accessories
                                Returns: 7 Days Replacement Policy
                            </span>
                        </div>

                        <div class="product-compare-variants my-2">
                            <p class="varients-title m-0">
                                Color
                            </p>
                            <p class="varients-content m-0">
                                Cobalt Blue, Olive Green, Urban Grey
                            </p>
                        </div>

                        <div class="product-compare-variants my-2">
                            <p class="varients-title m-0">
                                Color
                            </p>
                            <p class="varients-content m-0">
                                Cobalt Blue, Olive Green, Urban Grey
                            </p>
                        </div>

                        <div class="product-compare-variants my-2">
                            <p class="varients-title m-0">
                                Color
                            </p>
                            <p class="varients-content m-0">
                                Cobalt Blue, Olive Green, Urban Grey
                            </p>
                        </div>

                        <div class="compare-buynow my-3">
                            <button class="btn" type="button" data-update-text="Update cart"
                                data-add-text="Add to cart">
                                <i class="czi-cart text-white me-2"></i>
                                <span class="string-limit text-white">Add to cart</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>  

            <div>
                <div class="product-compare-table">
                    <div class="compare-product_item">
                        <img src="https://rukminim2.flixcart.com/image/184/184/xif0q/mobile/n/l/u/-original-imah2fjd7wfd9ksh.jpeg?q=90"
                            alt="">
                    </div>
                </div>
            </div>

            <div>
                <div class="product-compare-table">
                    <div class="compare-product_item">
                        <img src="https://rukminim2.flixcart.com/image/184/184/xif0q/mobile/n/l/u/-original-imah2fjd7wfd9ksh.jpeg?q=90"
                            alt="">
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('script')
@endpush
