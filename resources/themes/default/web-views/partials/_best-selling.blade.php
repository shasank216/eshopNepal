<div class="col-lg-12 px-max-md-0">
    <div class="h-100">
        <div class="card-body p-0">
            <div class="row d-flex justify-content-between align-items-center mb-3 product-head-border">
                {{-- <div> --}}
                {{-- <img class="size-30" src="{{theme_asset(path: "public/assets/front-end/png/best-sellings.png")}}"
                         alt=""> --}}
                <div class="text-black home-title">
                    <span> {{ translate('best_sellings') }}</span>
                </div>


                {{-- <span class="font-bold pl-1">{{ translate('best_sellings')}}</span> --}}
                {{-- </div> --}}
                <div>
                    <a class="text-capitalize view-all-text web-text-primary"
                        href="{{ route('products', ['data_from' => 'best-selling', 'page' => 1]) }}">{{ translate('view_all') }}
                        <i
                            class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1' }}"></i>
                    </a>
                </div>
            </div>
            <!--<div class="row g-3">-->
            <div class="best-selling-grid g-3">
            {{-- <div class="owl-theme owl-carousel best-selling-slider"> --}}
                @php
                    $productsFound = false;

                @endphp

                @foreach ($bestSellProduct as $key => $bestSell)

                    @if ($bestSell->product != null)
                        @if ($bestSell->product && $key < 6)
                            @php
                                $productsFound = true;
                            @endphp
                            <!--<div class="product col-lg-3 col-md-4 col-sm-6 col-12">-->
                            <div class="product grid-cards mb-3">
                                <div class="position-relative ">
                                    <a class="__best-selling" href="{{ route('product', $bestSell->product->slug) }}">
                                        <img class=""
                                            src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $bestSell->product['thumbnail'], type: 'product') }}"
                                            alt="{{ translate('product') }}" />
                                    </a>
                                    @if ($product->discount > 0)
                                        @if ($product->discount_type == 'percent')
                                            <div class="ribbon ribbon-sale">On Sale
                                                -{{ round($product->discount, !empty($decimalPointSettings) ? $decimalPointSettings : 0) }}%
                                            </div>
                                        @elseif($product->discount_type == 'flat')
                                            <div class="ribbon ribbon-sale">On Sale
                                                -{{ webCurrencyConverter(amount: $product->discount) }}</div>
                                        @endif
                                    @endif
                                    <div class="ribbon ribbon-new">New</div>

                                    <div class="products">
                                        <div class="text-center">
                                            <div class="details main-details">
                                                <p class="bold-subtitle m-0"> {{ $bestSell->product->brand->name }}</p>



                                                <span
                                                    class="bold-title">{{ Str::limit($bestSell->product['name'], 100) }}
                                                </span>

                                                @if ($product->discount > 0)
                                                    <p class="product-price m-0">
                                                        {{ webCurrencyConverter(amount: $bestSell->product->unit_price) }}
                                                        <del class="product-discount-price">
                                                            {{ webCurrencyConverter(
                                                                amount: $bestSell->product->unit_price -
                                                                    getProductDiscount(product: $bestSell->product, price: $bestSell->product->unit_price),
                                                            ) }}
                                                        </del>
                                                    </p>
                                                @else
                                                    <p class="product-without-discount m-0">
                                                        {{ webCurrencyConverter(amount: $bestSell->product->unit_price) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Hover Wala -->
                                        <div class="bestselling-hover-responsive">
                                            <div class="product-hover_details hover-detail d-none w-100">
                                                <div class="d-flex justify-content-between">
                                                    <div class="carding position-relative">

                                                        <a class="__best-selling w-100"
                                                            href="{{ route('product', $bestSell->product->slug) }}">
                                                            <img class=""
                                                                src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $bestSell->product['thumbnail'], type: 'product') }}"
                                                                alt="{{ translate('product') }}" />
                                                        </a>


                                                        @if ($bestSell->product->discount > 0)
                                                            @if ($bestSell->product->discount_type == 'percent')
                                                                <div class="ribbon ribbon-sale">On Sale
                                                                    -{{ round($bestSell->product->discount) }}%</div>
                                                            @elseif($bestSell->product->discount_type == 'flat')
                                                                <div class="ribbon ribbon-sale">On Sale
                                                                    -{{ webCurrencyConverter(amount: $bestSell->product->discount) }}
                                                                </div>
                                                            @endif
                                                        @endif

                                                        <div class="ribbon ribbon-new">New</div>

                                                        <div class="actions">
                                                            {{-- <button class="action-btn"><i
                                                                    class="fa fa-heart"></i></button> --}}
                                                            <button type="button"
                                                                data-product-id="{{ $product['id'] }}"
                                                                class="action-btn __text-18px w-100  product-action-add-wishlist">
                                                                <i class="fa {{ $wishlistStatus == 1 ? 'fa-heart' : 'fa-heart-o' }} wishlist_icon_{{ $product['id'] }} web-text-primary"
                                                                    aria-hidden="true"></i>

                                                                {{-- <span class="fs-14 text-muted align-bottom countWishlist-{{ $product['id'] }}">{{ $countWishlist }}</span> --}}
                                                            </button>

                                                            {{-- <div class="compare-container">
                                                                <a class="action-btn stopPropagation action-product-compare"
                                                                    href="javascript:"
                                                                    data-product-id="{{ $product->id }}">
                                                                    <img src="{{ asset('public/assets/front-end/img/icons/compare.png') }}"
                                                                        alt="Compare" />
                                                                </a>


                                                            </div> --}}
                                                            <div class="compare-container">
                                                                {{-- <a class="action-btn stopPropagation action-product-compare"
                                                                    href="javascript:"
                                                                    data-product-id="{{ $bestSell->product->id }}">
                                                                    <img src="{{ asset('public/assets/front-end/img/icons/compare.png') }}" alt="Compare" />
                                                                </a> --}}

                                                                <a class="action-btn stopPropagation action-product-compare"
                                                                    href="javascript:"
                                                                    data-product-id="{{ $bestSell->product->id }}">
                                                                    <img src="{{ asset('public/assets/front-end/img/icons/compare.png') }}"
                                                                        alt="Compare" />
                                                                </a>
                                                            </div>
                                                            <button class="action-btn"><i
                                                                    class="fa fa-eye"></i></button>
                                                        </div>

                                                    </div>

                                                    {{-- <div class="">
                                                        <div class="gallery">
                                                            @foreach (array_slice(json_decode($bestSell->product->images), 0, 4) as $key => $photo)
                                                                <figure>
                                                                    <img src="{{ getValidImage(path: 'storage/app/public/product/' . $photo, type: 'product') }}"
                                                                        alt="image"
                                                                        style="width: 100px; height: 75px; object-fit: cover;">
                                                                </figure>
                                                            @endforeach
                                                        </div>
                                                    </div> --}}
                                                </div>

                                                <div class="text-center">
                                                    <div class="details ">
                                                        <p class="bold-subtitle"> {{ $bestSell->product->brand->name }}
                                                        </p>

                                                        <span class="bold-title">
                                                            {{ Str::limit($bestSell->product['name'], 23) }}
                                                        </span>

                                                        @if ($product->discount > 0)
                                                            <p class="product-price m-0">
                                                                {{ webCurrencyConverter(amount: $bestSell->product->unit_price) }}
                                                                <del class="product-discount-price">
                                                                    {{ webCurrencyConverter(
                                                                        amount: $bestSell->product->unit_price -
                                                                            getProductDiscount(product: $bestSell->product, price: $bestSell->product->unit_price),
                                                                    ) }}
                                                                </del>
                                                            </p>
                                                        @else
                                                            <p class="product-without-discount m-0">
                                                                {{ webCurrencyConverter(amount: $bestSell->product->unit_price) }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <div class="product-tags px-3">
                                                        <div class="product">
                                                            <div class="product-tags">
                                                                @foreach ($bestSell->product->tags as $tag)
                                                                    <span class="tag">{{ $tag->tag }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="product-colors d-flex justify-content-center">


                                                        @foreach (json_decode($bestSell->product->colors) as $key => $color)
                                                            <li style="list-style-type: none; padding :0;margin: 0;">
                                                                {{-- <input type="radio"
                                                        id="{{ $product->id }}-color-{{ str_replace('#', '', $color) }}"
                                                        name="color" value="{{ $color }}"
                                                        @if ($key == 0) checked @endif> --}}
                                                                <label style="background: {{ $color }};"
                                                                    class="color-box"
                                                                    for="{{ $bestSell->product->id }}-color-{{ str_replace('#', '', $color) }}"
                                                                    data-toggle="tooltip"
                                                                    data-key="{{ str_replace('#', '', $color) }}"
                                                                    data-colorid="preview-box-{{ str_replace('#', '', $color) }}">
                                                                    <span class="outline"></span></label>
                                                            </li>
                                                        @endforeach
                                                    </div>

                                                    {{-- <button class="add-to-cart"><span class="btn-text"><i
                                                                class="fa-solid fa-cart-shopping"></i> Add
                                                            To
                                                            Cart</span></button> --}}

                                                    <!-- Compare Checkbox Below Product -->
                                                    {{-- <div class="compare-checkbox mt-2" style="margin-left: 15px;">
                                                            <input type="checkbox" id="compare-{{ $bestSell->product->id }}" class="compare-checkbox-input"
                                                                value="{{ $bestSell->product->id }}" data-product-id="{{ $bestSell->product->id }}">
                                                            <label for="compare-{{ $bestSell->product->id }}">{{ translate('Add to Compare') }}</label>
                                                        </div> --}}
                                                    <form id="add-to-cart-form" class="mb-2 px-1">
                                                        @csrf
                                                        <input type="hidden" name="id"
                                                            value="{{ $bestSell->product->id }}">
                                                        <div>
                                                            <div class="d-none">
                                                                <div
                                                                    class="d-flex justify-content-center align-items-center quantity-box border rounded border-base web-text-primary">
                                                                    <span class="input-group-btn">
                                                                        <button
                                                                            class="btn btn-number __p-10 web-text-primary"
                                                                            type="button" data-type="minus"
                                                                            data-field="quantity" disabled="disabled">
                                                                            -
                                                                        </button>
                                                                    </span>
                                                                    <input type="text" name="quantity"
                                                                        class="form-control input-number text-center cart-qty-field __inline-29 border-0 "
                                                                        placeholder="{{ translate('1') }}"
                                                                        value="{{ $product->minimum_order_qty ?? 1 }}"
                                                                        data-producttype="{{ $product->product_type }}"
                                                                        min="{{ $product->minimum_order_qty ?? 1 }}"
                                                                        max="{{ $product['product_type'] == 'physical' ? $product->current_stock : 100 }}">
                                                                    <span class="input-group-btn">
                                                                        <button
                                                                            class="btn btn-number __p-10 web-text-primary"
                                                                            type="button"
                                                                            data-producttype="{{ $product->product_type }}"
                                                                            data-type="plus" data-field="quantity">
                                                                            +
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" class="product-generated-variation-code"
                                                            name="product_variation_code">
                                                        <input type="hidden" value=""
                                                            class="in_cart_key form-control w-50" name="key">

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
                                                                <button class="btn add-to-cart string-limit"
                                                                    type="button" disabled>
                                                                    {{ translate('add_to_cart') }}
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn add-to-cart w-100"
                                                                    data-id="{{ $bestSell->product->id }}">
                                                                    <i class="navbar-tool-icon czi-cart me-2"></i>
                                                                    <span
                                                                        class="string-limit">{{ translate('add_to_cart') }}</span>
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
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach

                @if (!$productsFound)
                    <div class="row align-items-center justify-content-center bg-white w-100 p-4">
                        <p>No Products Found.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
