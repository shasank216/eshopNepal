@php($overallRating = getOverallRating($product->reviews))

<div class="product">
    <div class="carding position-relative">
        <img alt="{{ $product->name }}"
            src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}">
        @if ($product->discount > 0)
            @if ($product->discount_type == 'percent')
                <div class="ribbon ribbon-sale">On Sale
                    -{{ round($product->discount, !empty($decimalPointSettings) ? $decimalPointSettings : 0) }}%
                </div>
            @elseif($product->discount_type == 'flat')
                <div class="ribbon ribbon-sale">On Sale -{{ webCurrencyConverter(amount: $product->discount) }}</div>
            @endif
        @endif
        <div class="ribbon ribbon-new">New</div>
    </div>

    <div class="text-center">
        <div class="details main-details">
            <p class="bold-subtitle m-0"> {{ $product->brand->name }}</p>

            @if ($overallRating[0] != 0)
                <div class="rating-show justify-content-between">
                    <span class="d-inline-block font-size-sm text-body">
                        @for ($inc = 1; $inc <= 5; $inc++)
                            @if ($inc <= (int) $overallRating[0])
                                <i class="tio-star text-warning"></i>
                            @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                                <i class="tio-star-half text-warning"></i>
                            @else
                                <i class="tio-star-outlined text-warning"></i>
                            @endif
                        @endfor
                        <label class="badge-style review-text-container"> {{ count($product->reviews) }}
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
    </div>

    <!-- Hover Details -->
    <div class="latest_product_hover">
        <div class="product-hover_details d-none">
            <div class="d-flex justify-content-between">
                <div class="carding position-relative">
                    <img alt="{{ $product->name }}"
                        src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}">

                    @if ($product->discount > 0)
                        @if ($product->discount_type == 'percent')
                            <div class="ribbon ribbon-sale">On Sale
                                -{{ round($product->discount, !empty($decimalPointSettings) ? $decimalPointSettings : 0) }}%
                            </div>
                        @elseif($product->discount_type == 'flat')
                            <div class="ribbon ribbon-sale">On Sale
                                -{{ webCurrencyConverter(amount: $product->discount) }}
                            </div>
                        @endif
                    @endif
                    <div class="ribbon ribbon-new">New</div>

                    <div class="actions">
                        <button class="action-btn"><i class="fa fa-heart"></i></button>
                        <a class="action-btn stopPropagation action-product-compare" href="javascript:"
                            data-product-id="{{ $product->id }}">
                            <img src="{{ asset('public/assets/front-end/img/icons/compare.png') }}" alt=""/>
                        </a>
                        <a href="{{ route('product', $product->slug) }}" class="action-btn"><i
                                class="fa fa-eye"></i></a>
                    </div>
                </div>

                <div class="gallery">
                    @foreach (array_slice(json_decode($product->images), 0, 4) as $key => $photo)
                        <figure>
                            <img src="{{ getValidImage(path: 'storage/app/public/product/' . $photo, type: 'product') }}"
                                alt="image" style="width: 100px; height: 75px; object-fit: cover;">
                        </figure>
                    @endforeach
                </div>
            </div>

            <div class="text-center">
                <div class="details">
                    <p class="bold-subtitle"> {{ $product->brand->name }}</p>

                    @if ($overallRating[0] != 0)
                        <div class="rating-show justify-content-between">
                            <span class="d-inline-block font-size-sm text-body">
                                @for ($inc = 1; $inc <= 5; $inc++)
                                    @if ($inc <= (int) $overallRating[0])
                                        <i class="tio-star text-warning"></i>
                                    @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                                        <i class="tio-star-half text-warning"></i>
                                    @else
                                        <i class="tio-star-outlined text-warning"></i>
                                    @endif
                                @endfor
                                <label class="badge-style review-text-container"> {{ count($product->reviews) }}
                                    <span class="review-text">reviews</span>
                                </label>
                            </span>
                        </div>
                    @endif
                    
                    <a href="{{ route('product', $product->slug) }}">
                        <span class="bold-title">{{ Str::limit($product['name'], 23) }}</span>
                    </a>

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

                {{-- <div class="product-tags">
                    @foreach ($product_tags as $product_tag)
                        <div class="product">
                            <div class="product-tags">
                                @foreach ($product_tag->tags as $tag)
                                    <span class="tag">{{ $tag->tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div> --}}

                <div class="product-tags">
                    @foreach ($product_tags as $product_tag)
                        <div class="product">
                            <div class="product-tags">
                                @foreach ($product_tag->tags as $tag)
                                    @if(!empty(trim($tag->tag)))
                                        <span class="tag">{{ $tag->tag }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                

                <div class="product-colors d-flex justify-content-center">

                    @foreach (json_decode($product->colors) as $key => $color)
                        <li style="list-style-type: none;">
                            <label style="background: {{ $color }};"
                                class="color-box"
                                for="{{ $product->id }}-color-{{ str_replace('#', '', $color) }}"
                                data-toggle="tooltip" data-key="{{ str_replace('#', '', $color) }}"
                                data-colorid="preview-box-{{ str_replace('#', '', $color) }}">
                                <span class="outline"></span></label>
                        </li>
                    @endforeach
                </div>
                <form id="add-to-cart-form" class="mb-2">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <div class="d-none">
                        <div>
                            <div
                                class="d-flex justify-content-center align-items-center quantity-box border rounded border-base web-text-primary">
                                <span class="input-group-btn">
                                    <button class="btn btn-number __p-10 web-text-primary" type="button"
                                        data-type="minus" data-field="quantity" disabled="disabled">
                                        -
                                    </button>
                                </span>
                                <input type="hidden" name="quantity"
                                    class="form-control input-number text-center cart-qty-field __inline-29 border-0 "
                                    placeholder="{{ translate('1') }}" value="{{ $product->minimum_order_qty ?? 1 }}"
                                    data-producttype="{{ $product->product_type }}"
                                    min="{{ $product->minimum_order_qty ?? 1 }}"
                                    max="{{ $product['product_type'] == 'physical' ? $product->current_stock : 100 }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-number __p-10 web-text-primary" type="button"
                                        data-producttype="{{ $product->product_type }}" data-type="plus"
                                        data-field="quantity">
                                        +
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="product-generated-variation-code" name="product_variation_code">
                    <input type="hidden" value="" class="in_cart_key form-control w-50" name="key">

                    <div class="__btn-grp d-none d-sm-flex">
                        @if (
                            ($product->added_by == 'seller' &&
                                ($sellerTemporaryClose ||
                                    (isset($product->seller->shop) &&
                                        $product->seller->shop->vacation_status &&
                                        $currentDate >= $sellerVacationStartDate &&
                                        $currentDate <= $sellerVacationEndDate))) ||
                                ($product->added_by == 'admin' &&
                                    ($inHouseTemporaryClose ||
                                        ($inHouseVacationStatus &&
                                            $currentDate >= $inHouseVacationStartDate &&
                                            $currentDate <= $inHouseVacationEndDate))))
                            <button class="btn add-to-cart string-limit" type="button" disabled>
                                {{ translate('add_to_cart') }}
                            </button>
                        @else
                            {{-- <button
                                class="btn add-to-cart product-card-add-cart element-center btn-gap-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }} action-add-to-cart-form"
                                type="button" data-update-text="{{ translate('update_cart') }}"
                                data-add-text="{{ translate('add_to_cart') }}"
                                data-id="{{$product->id}}">
                                <i class="navbar-tool-icon czi-cart text-white me-2"></i>
                                <span class="string-limit text-white">{{ translate('add_to_cart') }}</span>
                            </button> --}}
                            <button type="button" class="btn add-to-cart" data-id="{{$product->id}}">
                                <i class="navbar-tool-icon czi-cart me-2"></i>
                                <span class="string-limit">{{ translate('add_to_cart') }}</span>
                            </button>
                           
                        @endif

                        @if (
                            ($product->added_by == 'seller' &&
                                ($sellerTemporaryClose ||
                                    (isset($product->seller->shop) &&
                                        $product->seller->shop->vacation_status &&
                                        $currentDate >= $sellerVacationStartDate &&
                                        $currentDate <= $sellerVacationEndDate))) ||
                                ($product->added_by == 'admin' &&
                                    ($inHouseTemporaryClose ||
                                        ($inHouseVacationStatus &&
                                            $currentDate >= $inHouseVacationStartDate &&
                                            $currentDate <= $inHouseVacationEndDate))))
                            <div class="alert alert-danger" role="alert">
                                {{ translate('this_shop_is_temporary_closed_or_on_vacation._You_cannot_add_product_to_cart_from_this_shop_for_now') }}
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
