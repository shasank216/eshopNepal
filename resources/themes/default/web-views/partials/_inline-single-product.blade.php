@php($overallRating = getOverallRating($product->reviews))



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

       
    </div>

    <div class="text-center">
        <div class="details main-details">

            <p class="title">{{ $product->brand_id }}</p>
           

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
                <p class="title">
                    {{ $product->brand_id }}
                </p>
              
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
