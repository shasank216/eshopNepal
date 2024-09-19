@php($overallRating = getOverallRating($product->reviews))

<style>
    .btn-grp-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn-grp-container .btn {
        flex: 1;
    }
</style>

<div class="product-single-hover style--card">
    <div class="overflow-hidden position-relative">
        <div class="d-flex muntiple-designs design-1">
            <div class="search-product-image">
                <div class="inline_product clickable d-flex justify-content-center">
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
                            <i class="czi-eye align-middle web-text-primary"></i>
                            
                        </a>
                       

                        {{-- <a class="btn-circle stopPropagation action-product-compare" href="javascript:"
                            data-product-id="{{ $product->id }}">
                            <img src="{{ asset('public/assets/front-end/img/icons/compare.png') }}" alt="" />
                        </a> --}}

                        {{-- <a class="btn-circle stopPropagation action-product-compare" href="javascript:" data-product-id="{{ $product->id }}">
                            <img src="{{ asset('public/assets/front-end/img/icons/compare.png') }}" alt="" />
                        </a> --}}
                        <!-- Example product entry -->
                        <a class="btn-circle stopPropagation action-product-compare" href="javascript:" data-product-id="{{ $product->id }}">
                            <img src="{{ asset('public/assets/front-end/img/icons/compare.png') }}" alt="Compare" />
                        </a>

                        

                        <a class="btn-circle stopPropagation product-action-add-wishlist" href="javascript:"
                            data-product-id="{{ $product->id }}">
                           
                            {{-- <i class="fa fa-heart-o wishlist_icon_12 web-text-primary" aria-hidden="true"></i> --}}
                            <i class="fa {{ $wishlistStatus == 1 ? 'fa-heart' : 'fa-heart-o' }} wishlist_icon_{{ $product['id'] }} web-text-primary"
                            aria-hidden="true"></i> 
                        </a>

                        {{-- <button type="button" data-product-id="{{ $product['id'] }}"
                        class="action-btn __text-18px   product-action-add-wishlist">
                         <i class="fa {{ $wishlistStatus == 1 ? 'fa-heart' : 'fa-heart-o' }} wishlist_icon_{{ $product['id'] }} web-text-primary"
                            aria-hidden="true"></i> 
                            
                        <span class="fs-14 text-muted align-bottom countWishlist-{{ $product['id'] }}">{{ $countWishlist }}</span>
                    </button> --}}
                        
                        
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

                    <div class="justify-content-between">
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
                        <form class="add-to-cart-form btn-grp-container">
                            <!-- Hidden input field for product ID -->
                            <input type="hidden" name="id" value="{{ $product->id }}">

                            <!-- Buy Now Button -->
                            <button class="btn text-white element-center action-buy-now-this-product" type="button"
                                data-product-id="{{ $product->id }}">
                                <span class="string-limit">{{ translate('buy_now') }}</span>
                            </button>

                            <!-- Add to Cart Button -->
                            <button type="submit" class="btn add-to-cart">
                                <i class="navbar-tool-icon czi-cart me-2"></i>
                                <span class="string-limit">{{ translate('add_to_cart') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- Compare Checkbox Below Product -->
    <div class="compare-checkbox mt-2" style="margin-left: 15px;">
        <input type="checkbox" id="compare-{{ $product->id }}" class="compare-checkbox-input"
            value="{{ $product->id }}" data-product-id="{{ $product->id }}">
        <label for="compare-{{ $product->id }}">{{ translate('Add to Compare') }}</label>
    </div> --}}

</div>
