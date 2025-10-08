@php($overallRating = getOverallRating($product->reviews))
@push('css_or_js')
    <style>
        .btn-grp-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-grp-container .btn {
            flex: 1;
        }

        .ribbon-sale {
            background-color:
                color-mix(in srgb, #FF3B3B, #fff 20%);
            display: flex;
            justify-content: start;
            align-items: center;
            width: 130px;
            font-size: 11px;
            font-weight: 400;
            padding-left: 1rem;
            height: 20px;
        }

        .ribbon {
            position: absolute;
            padding: 5px 10px 5px 15px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            z-index: 3;
            top: 20px;
            left: -13px;
            clip-path: polygon(100% 0, 85% 50%, 100% 100%, 10% 100%, 10% 0%);
            box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        }

        .ribbon-sale {
            background-color:
                color-mix(in srgb, #FF3B3B, #fff 20%);
            display: flex;
            justify-content: start;
            align-items: center;
            width: 130px;
            font-size: 11px;
            font-weight: 400;
            padding-left: 1rem;
            height: 20px;
        }

        .ribbon {
            position: absolute;
            padding: 5px 10px 5px 15px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            z-index: 3;
            top: 20px;
            left: -13px;
            clip-path: polygon(100% 0, 85% 50%, 100% 100%, 10% 100%, 10% 0%);
            box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        }

        .ribbon-sale {
            background-color:
                color-mix(in srgb, #FF3B3B, #fff 20%);
            display: flex;
            justify-content: start;
            align-items: center;
            width: 130px;
            font-size: 11px;
            font-weight: 400;
            padding-left: 1rem;
            height: 20px;
        }

        .ribbon {
            position: absolute;
            padding: 5px 10px 5px 15px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            z-index: 3;
            top: 20px;
            left: -13px;
            clip-path: polygon(100% 0, 85% 50%, 100% 100%, 10% 100%, 10% 0%);
            box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        }
    </style>
@endpush
<div class="product-single-hover style--card">
    <div class="overflow-hidden position-relative">
        <div class="d-flex muntiple-designs design-1">
            <div class="search-product-image">
                <div class="inline_product clickable d-flex justify-content-center">
                    @if ($product->discount > 0)
                        {{-- <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13"> --}}
                        <span class="ribbon ribbon-sale">
                            {{-- <span class="direction-ltr d-block pl-2"> --}}
                            On Sale
                            @if ($product->discount_type == 'percent')
                                {{ round($product->discount, !empty($decimal_point_settings) ? $decimal_point_settings : 0) }}%
                            @elseif($product->discount_type == 'flat')
                                {{ webCurrencyConverter(amount: $product->discount) }}
                            @endif
                            {{-- </span> --}}
                        </span>
                    @else
                        <div class="d-flex justify-content-end">
                            <span class="for-discount-value-null"></span>
                        </div>
                    @endif
                    <div class="p-10px pb-0">
                        <a href="{{ route('product', $product->slug) }}"
                            class="w-100 d-flex align-items-center justify-content-center">
                            <img alt=""
                                src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}">
                        </a>
                    </div>
                    <div class="quick-view" id="product-list-container">
                        <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:void(0)"
                            data-product-id="{{ $product->id }}">
                            <i class="czi-eye align-middle web-text-primary"></i>
                        </a>
                        <a class="btn-circle stopPropagation action-product-compare" href="javascript:void(0)"
                            data-product-id="{{ $product->id }}" data-category-id="{{ $product->category_id }}">
                            <i class="czi-compare align-middle web-text-primary"></i>
                            <!-- Ensure the category ID is included here -->
                            {{-- <img src="{{ asset('public/assets/front-end/img/icons/compare.png') }}" alt="Compare" /> --}}
                        </a>
                        <a class="btn-circle stopPropagation product-action-add-wishlist" href="javascript:void(0)"
                            data-product-id="{{ $product->id }}">
                            <i class="fa fa-heart-o wishlist_icon_12 web-text-primary" aria-hidden="true"></i>
                            {{-- <i class="fa {{ $wishlistStatus == 1 ? 'fa-heart' : 'fa-heart-o' }} wishlist_icon_{{ $product['id'] }} web-text-primary"
                            aria-hidden="true"></i>  --}}
                        </a>
                    </div>
                    @if ($product->product_type == 'physical' && $product->current_stock <= 0)
                        <span class="out_fo_stock">{{ translate('out_of_stock') }}</span>
                    @endif
                </div>
            </div>
            <div class="search-product-details d-flex align-items-center">
                <div class="single-product-details">
                    <div>
                        <p class="m-0 brand-name">
                            {{ $product->brand->name }}
                        </p>
                    </div>
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
                    <div class="product-item_container">
                        <a href="{{ route('product', $product->slug) }}">
                            {{ Str::limit($product['name'], 23) }}
                        </a>
                        <p class="m-0">
                            {{ Str::limit(strip_tags($product['details']), 150) }}
                            {{-- {!! $product->details !!} --}}
                        </p>
                    </div>
                    <div>
                        @if (!empty($product->colors) && count(json_decode($product->colors)) > 0)
                            <div class="flex-start align-items-center">
                                <div class="w-100">
                                    <ul class="list-inline checkbox-color product-search-page mb-0 flex-start ps-0">
                                        @foreach (json_decode($product->colors) as $key => $color)
                                            <li>
                                                <label style="background: {{ $color }};"
                                                    class="focus-preview-image-by-color shadow-border"
                                                    for="{{ $product->id }}-color-{{ str_replace('#', '', $color) }}"
                                                    data-toggle="tooltip" data-key="{{ str_replace('#', '', $color) }}"
                                                    data-colorid="preview-box-{{ str_replace('#', '', $color) }}">
                                                    <span class="outline"></span></label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="justify-content-between mt-1">
                        <div class="product-price d-flex flex-wrap align-items-center gap-8">
                            <span class="text-accent text-dark">
                                {{ webCurrencyConverter(
                                    amount: $product->unit_price - getProductDiscount(product: $product, price: $product->unit_price),
                                ) }}
                                @if ($product->discount > 0)
                                    <del class="category-single-product-price">
                                        {{ webCurrencyConverter(amount: $product->unit_price) }}
                                    </del>
                                    <br>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="__btn-grp search-page-buttons mt-2 mb-3">
                        <form id="add-to-cart-form-{{ $product->id }}" class="mb-2">
                            @csrf
                            <input type="hidden" name="id" value="{{ $product->id }}">
                            <input type="hidden" class="product-generated-variation-code"
                                name="product_variation_code">
                            <input type="hidden" value="" class="in_cart_key form-control w-50" name="key">
                            <input type="hidden" name="quantity" value="1">
                            <!-- <div class="mt-2 mb-0 d-none d-sm-flex" style="display: flex; gap: 10px; align-items: center;"> -->
                            <div class="mt-2 mb-0 d-none d-sm-flex align-items-center gap-1">
                                <!--  <div class="__btn-grp mt-2 mb-3 d-none d-sm-flex">  original  -->
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
                                    <button class="btn btn-secondary" type="button" disabled>
                                        {{ translate('buy_now') }}
                                    </button>
                                    <button class="btn add-to-cart string-limit" type="button" disabled>
                                        {{ translate('add_to_cart') }}
                                    </button>
                                @else
                                    @if (auth('customer')->check())
                                        <button
                                            class="btn btn-secondary element-center btn-gap-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }} action-buy-now-this-product"
                                            data-product-id="{{ $product->id }}" type="button">
                                            <span class="string-limit">{{ translate('buy_now') }}</span>
                                        </button>
                                        {{-- <button
                                            class="btn btn-secondary element-center btn-gap-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}"
                                            type="button" onclick="addToCart('add-to-cart-form', true)">
                                            <span class="string-limit">{{ translate('buy_now') }}</span>
                                        </button> --}}
                                        <button type="button" class="btn action-add-to-cart-form-re-init add-to-cart"
                                            onclick="addToCart('add-to-cart-form-{{ $product->id }}', false)"
                                            data-product-id="{{ $product->id }}">
                                            <i class="navbar-tool-icon czi-cart me-2"></i>
                                            <span class="string-limit">{{ translate('add_to_cart') }}</span>
                                        </button>
                                    @else
                                        <a href="{{ route('customer.auth.login') }}"
                                            class="btn btn-secondary element-center btn-gap-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }} "
                                            type="button">
                                            <span class="string-limit">{{ translate('buy_now') }}</span>
                                        </a>
                                        <a href="{{ route('customer.auth.login') }}"
                                            class="btn action-add-to-cart-form-re add-to-cart"
                                            data-product-id="{{ $product->id }}" type="button"
                                            data-update-text="{{ translate('update_cart') }}">
                                            <span class="string-limit">{{ translate('add_to_cart') }}</span>
                                        </a>
                                    @endif
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
    </div>
    <!-- Compare Checkbox Below Product -->
    {{-- <div class="compare-checkbox mt-2" style="margin-left: 15px;">
        <input type="checkbox" id="compare-{{ $product->id }}" class="compare-checkbox-input"
            value="{{ $product->id }}" data-product-id="{{ $product->id }}">
        <label for="compare-{{ $product->id }}">{{ translate('Add to Compare') }}</label>
    </div> --}}
</div>
