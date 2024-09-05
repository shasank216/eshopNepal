@php($overallRating = getOverallRating($product->reviews))

{{-- <div class="muntiple-designs"> --}}
    <div class="product-single-hover style--card">
        <div class="overflow-hidden position-relative">

            <div class="d-flex muntiple-designs design-1">
                <div class="search-product-image">
                    <div class=" inline_product clickable d-flex justify-content-center">
                        @if ($product->discount > 0)
                            <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13">
                                <span class="direction-ltr d-block">
                                    @if ($product->discount_type == 'percent')
                                        -{{ round($product->discount, !empty($decimal_point_settings) ? $decimal_point_settings : 0) }}%
                                    @elseif($product->discount_type == 'flat')
                                        -{{ webCurrencyConverter(amount: $product->discount) }}
                                    @endif
                                </span>
                            </span>
                        @else
                            <div class="d-flex justify-content-end">
                                <span class="for-discount-value-null"></span>
                            </div>
                        @endif
                        <div class="p-10px pb-0">
                            <a href="{{ route('product', $product->slug) }}" class="w-100">
                                <img alt=""
                                    src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}">
                            </a>
                        </div>


                        <div class="quick-view">
                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                data-product-id="{{ $product->id }}">
                                <i class="czi-eye align-middle"></i>
                            </a>
                        </div>
                        @if ($product->product_type == 'physical' && $product->current_stock <= 0)
                            <span class="out_fo_stock">{{ translate('out_of_stock') }}</span>
                        @endif
                    </div>
                </div>
                <div class="search-product-details">
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
                                {!! $product->details !!}
                            </p>
                        </div>

                        <div>
                            @if (!empty($product->colors) && count(json_decode($product->colors)) > 0)
                                <div class="flex-start align-items-center">
                                    <div class="w-100">
                                        <ul class="list-inline checkbox-color product-search-page mb-0 flex-start ps-0">
                                            @foreach (json_decode($product->colors) as $key => $color)
                                                <li>
                                                    {{-- <input type="radio"
                                                        id="{{ $product->id }}-color-{{ str_replace('#', '', $color) }}"
                                                        name="color" value="{{ $color }}"
                                                        @if ($key == 0) checked @endif> --}}
                                                    <label style="background: {{ $color }};"
                                                        class="focus-preview-image-by-color shadow-border"
                                                        for="{{ $product->id }}-color-{{ str_replace('#', '', $color) }}"
                                                        data-toggle="tooltip"
                                                        data-key="{{ str_replace('#', '', $color) }}"
                                                        data-colorid="preview-box-{{ str_replace('#', '', $color) }}">
                                                        <span class="outline"></span></label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="justify-content-between ">
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

                        <div class="__btn-grp search-page-buttons mt-2 mb-3 d-sm-flex">
                            {{-- @if (($product->added_by == 'seller' && ($sellerTemporaryClose || (isset($product->seller->shop) && $product->seller->shop->vacation_status && $currentDate >= $sellerVacationStartDate && $currentDate <= $sellerVacationEndDate))) || ($product->added_by == 'admin' && ($inHouseTemporaryClose || ($inHouseVacationStatus && $currentDate >= $inHouseVacationStartDate && $currentDate <= $inHouseVacationEndDate))))
                                <button class="btn btn-secondary" type="button" disabled>
                                    {{ translate('buy_now') }}
                                </button>
                                <button class="btn btn--primary string-limit" type="button" disabled>
                                    {{ translate('add_to_cart') }}
                                </button>
                            @else --}}
                            <button
                                class="btn text-white element-center btn-gap-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }} action-buy-now-this-product"
                                type="button">
                                <span class="string-limit">{{ translate('buy_now') }}</span>
                            </button>
                            <button
                                class="btn element-center btn-gap-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }} action-add-to-cart-form"
                                type="button" data-update-text="{{ translate('update_cart') }}"
                                data-add-text="{{ translate('add_to_cart') }}">
                                <span class="string-limit">{{ translate('add_to_cart') }}</span>
                            </button>
                            {{-- @endif --}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- </div> --}}
