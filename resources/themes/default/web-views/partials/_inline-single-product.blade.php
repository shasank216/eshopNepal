@php($overallRating = getOverallRating($product->reviews))

{{-- <div class="product-single-hover style--card">
    <div class="overflow-hidden position-relative">
        <div class=" inline_product clickable d-flex justify-content-center">
            @if ($product->discount > 0)
                <div class="d-flex">
                    <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                        <span class="direction-ltr d-block">
                            @if ($product->discount_type == 'percent')
                                -{{ round($product->discount,(!empty($decimalPointSettings) ? $decimalPointSettings: 0))}}%
                            @elseif($product->discount_type =='flat')
                                -{{ webCurrencyConverter(amount: $product->discount) }}
                            @endif
                        </span>
                    </span>
                </div>
            @else
                <div class="d-flex justify-content-end">
                    <span class="for-discount-value-null"></span>
                </div>
            @endif
            <div class="p-10px pb-0">
                <a href="{{route('product',$product->slug)}}" class="w-100">
                    <img alt=""
                         src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/'.$product['thumbnail'], type: 'product') }}">
                </a>
            </div>

            <div class="quick-view">
                <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:" data-product-id="{{ $product->id }}">
                    <i class="czi-eye align-middle"></i>
                </a>
            </div>
            @if ($product->product_type == 'physical' && $product->current_stock <= 0)
                <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
            @endif
        </div>
        <div class="single-product-details">
            <div class="text-center brand-name">
                <!-- <a href="{{route('product',$product->slug)}}"> -->
                    {{ $product->brand->name }}
                <!-- </a> -->
            </div>
            @if ($overallRating[0] != 0)
                <div class="rating-show justify-content-between text-center">
                    <span class="d-inline-block font-size-sm text-body">
                        @for ($inc = 1; $inc <= 5; $inc++)
                            @if ($inc <= (int) $overallRating[0])
                                <i class="tio-star text-warning"></i>
                            @elseif ($overallRating[0] != 0 && $inc <= (int)$overallRating[0] + 1.1 && $overallRating[0] > ((int)$overallRating[0]))
                                <i class="tio-star-half text-warning"></i>
                            @else
                                <i class="tio-star-outlined text-warning"></i>
                            @endif
                        @endfor
                        <label class="badge-style">( {{ count($product->reviews) }} )</label>
                    </span>
                </div>
            @endif
            <div class="text-center">
                <a class="product-name" href="{{route('product',$product->slug)}}">
                    {{ Str::limit($product['name'], 23) }}
                </a>
            </div>
            <div class="justify-content-between text-center">
                <div class="product-price text-center d-flex flex-wrap justify-content-center align-items-center gap-8">
                    @if ($product->discount > 0)
                        <del class="category-single-product-price">
                            {{ webCurrencyConverter(amount: $product->unit_price) }}
                        </del>
                    @endif
                    <span class="text-accent text-dark">
                        {{ webCurrencyConverter(amount:
                            $product->unit_price-(getProductDiscount(product: $product, price: $product->unit_price))
                        ) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div> --}}

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

    .hover-detail .carding {
        /* top: -25px;
    left: -10px; */
        width: 246px;

        /* === chatgpt added this ==== */
        /* === chatgpt added this ==== */
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

    .hover-detail {
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

<div class="product">
    <div class="carding position-relative p-4 mx-auto">
        <div class="ribbon ribbon-sale">On Sale -10%</div>
        <div class="ribbon ribbon-new">New</div>

        <div class="d-flex justify-content-between timer mt-4">
            <div class="time-box text-center">
                <span class="number d-block">30</span>
                <span class="label d-block">Days</span>
            </div>
            <div class="time-box text-center">
                <span class="number d-block">12</span>
                <span class="label d-block">Hours</span>
            </div>
            <div class="time-box text-center">
                <span class="number d-block">45</span>
                <span class="label d-block">Min</span>
            </div>
            <div class="time-box text-center">
                <span class="number d-block">50</span>
                <span class="label d-block">Sec</span>
            </div>
        </div>
    </div>

    <div class="text-center">
        <div class="details main-details">

            <p class="title">Sony</p>
            <div class="rating">⭐⭐⭐⭐⭐ 162 <a href="#">reviews</a></div>
            <span class="bold-subtitle">Camera Canon EOS 2000D Kit EF-S18-55mm F3.5-5.6 III</span>
            <p style="color: red; font-weight: 600; font-size: 20px;">$9.122 <del
                    style="margin-left: 10px; color: #000;  font-weight: 500; font-size: 16px;">$1.325</del>
            </p>
        </div>
    </div>

    <!-- Hover Wala -->
    <div class="hover-detail d-none">
        <div class="d-flex justify-content-between">
            <div class="carding position-relative">

                <div class="ribbon ribbon-sale">On Sale -10%</div>
                <div class="ribbon ribbon-new">New</div>

                <div class="actions">
                    <button class="action-btn"><i class="fa fa-heart"></i></button>
                    <button class="action-btn"><i class="fa fa-sync-alt"></i></button>
                    <button class="action-btn"><i class="fa fa-eye"></i></button>
                </div>


                <div class="d-flex justify-content-between timer mt-4">
                    <div class="time-box text-center">
                        <span class="number d-block">30</span>
                        <span class="label d-block">Days</span>
                    </div>
                    <div class="time-box text-center">
                        <span class="number d-block">12</span>
                        <span class="label d-block">Hours</span>
                    </div>
                    <div class="time-box text-center">
                        <span class="number d-block">45</span>
                        <span class="label d-block">Min</span>
                    </div>
                    <div class="time-box text-center">
                        <span class="number d-block">50</span>
                        <span class="label d-block">Sec</span>
                    </div>
                </div>

            </div>

            <div class="">
                <div class="gallery">
                    <figure><img src="" alt="image"></figure>
                    <figure><img src="" alt="image"></figure>
                    <figure><img src="" alt="image"></figure>
                    <figure><img src="" alt="image"></figure>
                </div>
            </div>
        </div>

        <div class="text-center">
            <div class="details ">
                <p class="title">Sony</p>
                <div class="rating">⭐⭐⭐⭐⭐ 162 <a href="#">reviews</a></div>
                <span class="bold-subtitle">Camera Canon EOS 2000D Kit EF-S18-55mm F3.5-5.6 III</span>
                <p style="color: red; font-weight: 600; font-size: 20px;">$9.122 <del
                        style="margin-left: 10px; color: #000;  font-weight: 500; font-size: 16px;">$1.325</del>
                </p>
            </div>

            <div class="product-tags">
                <span class="tag">USB 3.0</span>
                <span class="tag">Portable</span>
                <span class="tag">360 Reality Audio</span>
            </div>

            <div class="product-colors d-flex justify-content-center">
                <span class="color-box" style="background-color: #ccc;"></span>
                <span class="color-box" style="background-color: #a3a3a3;"></span>
                <span class="color-box" style="background-color: #ff0000;"></span>
                <span class="color-box" style="background-color: #ffcc00;"></span>
                <span class="color-box" style="background-color: #f5a9bc;"></span>
            </div>

            <button class="add-to-cart"><span class="btn-text"><i class="fa-solid fa-cart-shopping"></i> Add
                    To
                    Cart</span></button>

        </div>

    </div>

</div>


