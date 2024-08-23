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


<div class="product">
    <div class="carding position-relative p-4 mx-auto">
        <img class="h-100" alt="{{ $product->name }}"
            src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}">
        @if ($product->discount > 0)
            @if ($product->discount_type == 'percent')
                <div class="ribbon ribbon-sale">On Sale
                    -{{ round($product->discount, !empty($decimalPointSettings) ? $decimalPointSettings : 0) }}%</div>
            @elseif($product->discount_type == 'flat')
                <div class="ribbon ribbon-sale">On Sale -{{ webCurrencyConverter(amount: $product->discount) }}</div>
            @endif
        @else
        @endif
        <div class="ribbon ribbon-new">New</div>

        {{-- <div class="d-flex justify-content-between timer mt-4">
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
        </div> --}}
        {{-- @dd($product); --}}
    </div>

    <div class="text-center">
        <div class="details main-details">

            <p class="title">{{ $product->brand_id }}</p>
            {{-- @if ($overallRating[0] != 0)
                @for ($inc = 1; $inc <= 5; $inc++)
                    @if ($inc <= (int) $overallRating[0])
                        <div class="rating">
                            <i class="tio-star text-warning">
                                ( {{ count($product->reviews) }} )</i>
                        </div>
                    @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                        <div class="rating">
                            <i class="tio-star-half text-warning"></i>
                            ( {{ count($product->reviews) }} )
                        </div>
                    @else
                        <div class="rating">
                            <i class="tio-star-outlined text-warning"></i>
                            ( {{ count($product->reviews) }} )
                        </div>
                    @endif
                @endfor
            @endif --}}

            <span class="bold-subtitle">
                {{ Str::limit($product['name'], 23) }}
            </span>
            <p style="color: red; font-weight: 600; font-size: 20px;">
                @if ($product->discount > 0)
                    {{ webCurrencyConverter(
                        amount: $product->unit_price - getProductDiscount(product: $product, price: $product->unit_price),
                    ) }}
                @endif
                <del style="margin-left: 10px; color: #000;  font-weight: 500; font-size: 16px;">
                    {{ webCurrencyConverter(amount: $product->unit_price) }}
                </del>
            </p>
        </div>
    </div>

    <!-- Hover Wala -->
    <div class="product-hover_details d-none">
        <div class="d-flex justify-content-between">
            <div class="carding position-relative">
                <img class="h-100" alt="{{ $product->name }}"
                    src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}">

                @if ($product->discount > 0)
                    @if ($product->discount_type == 'percent')
                        <div class="ribbon ribbon-sale">On Sale
                            -{{ round($product->discount, !empty($decimalPointSettings) ? $decimalPointSettings : 0) }}%
                        </div>
                    @elseif($product->discount_type == 'flat')
                        <div class="ribbon ribbon-sale">On Sale -{{ webCurrencyConverter(amount: $product->discount) }}
                        </div>
                    @endif
                @else
                @endif
                <div class="ribbon ribbon-new">New</div>

                <div class="actions">
                    <button class="action-btn"><i class="fa fa-heart"></i></button>
                    {{-- <button class="action-btn"><i class="fa fa-sync-alt"></i></button> --}}
                    <a href="{{route('product',$product->slug)}}" class="action-btn"><i class="fa fa-eye"></i></a>
                </div>


                {{-- <div class="d-flex justify-content-between timer mt-4">
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
                </div> --}}

            </div>

            <div class="">
                <div class="gallery">
                    @foreach ($product as $products)
                    <figure><img src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/'.$product['thumbnail'], type: 'product') }}" alt="image"></figure>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="text-center">
            <div class="details ">
                <p class="title">
                    {{ $product->brand_id }}
                </p>
                {{-- @if ($overallRating[0] != 0)
                    <div class="rating">
                        @for ($inc = 1; $inc <= 5; $inc++)
                            @if ($inc <= (int) $overallRating[0])
                                <i class="tio-star text-warning">
                                    ( {{ count($product->reviews) }} )</i>
                            @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                                <i class="tio-star-half text-warning"></i>
                                ( {{ count($product->reviews) }} )
                            @else
                                <i class="tio-star-outlined text-warning"></i>
                                ( {{ count($product->reviews) }} )
                            @endif
                        @endfor
                    </div>
                @endif --}}
                <span class="bold-subtitle">{{ Str::limit($product['name'], 23) }}</span>
                <p style="color: red; font-weight: 600; font-size: 20px;">
                    @if ($product->discount > 0)
                        {{ webCurrencyConverter(
                            amount: $product->unit_price - getProductDiscount(product: $product, price: $product->unit_price),
                        ) }}
                    @endif
                    <del style="margin-left: 10px; color: #000;  font-weight: 500; font-size: 16px;">
                        {{ webCurrencyConverter(amount: $product->unit_price) }}
                    </del>
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
