@php($overallRating = getOverallRating($product->reviews))

<div class="product">
    <div class="carding position-relative p-4">
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
            <p class="bold-subtitle"> {{ $product->brand->name }}</p>

            <span class="bold-subtitle">
                {{ Str::limit($product['name'], 23) }}
            </span>
            <p class="m-0" style="color: red; font-weight: 600; font-size: 20px;">
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

                <div class="product-tags px-3">
                    @foreach ($product_tags as $product_tag)
                        <div class="product">
                            <div class="product-tags">
                                @foreach ($product_tag->tags as $tag)
                                    <span class="tag">{{ $tag->tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="product-colors d-flex justify-content-center">
                    {{-- <span class="color-box" style="background-color: #ccc;"></span>
                    <span class="color-box" style="background-color: #a3a3a3;"></span>
                    <span class="color-box" style="background-color: #ff0000;"></span>
                    <span class="color-box" style="background-color: #ffcc00;"></span>
                    <span class="color-box" style="background-color: #f5a9bc;"></span> --}}

                    @foreach (json_decode($product->colors) as $key => $color)
                        <li style="list-style-type: none;">
                            {{-- <input type="radio"
                            id="{{ $product->id }}-color-{{ str_replace('#', '', $color) }}"
                            name="color" value="{{ $color }}"
                            @if ($key == 0) checked @endif> --}}
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
                    {{-- <div class="d-flex justify-content-center align-items-center">
                        <div>
                            <div
                                class="d-flex justify-content-center align-items-center quantity-box border rounded border-base web-text-primary">
                                <span class="input-group-btn">
                                    <button class="btn btn-number __p-10 web-text-primary" type="button"
                                        data-type="minus" data-field="quantity" disabled="disabled">
                                        -
                                    </button>
                                </span>
                                <input type="text" name="quantity"
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
                    </div> --}}
                    <input type="hidden" class="product-generated-variation-code" name="product_variation_code">
                    <input type="hidden" value="" class="in_cart_key form-control w-50" name="key">

                    <div class="__btn-grp mt-2 mb-3 d-none d-sm-flex">
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
                            <button
                                class="btn add-to-cart element-center btn-gap-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }} action-add-to-cart-form"
                                type="button" data-update-text="{{ translate('update_cart') }}"
                                data-add-text="{{ translate('add_to_cart') }}">
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
