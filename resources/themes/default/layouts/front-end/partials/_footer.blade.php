<div class="__inline-9 rtl">

    <div class="background_class page-footer font-small mdb-color rtl" style="background-color: #1f3c74;">

        <div class="container">
            <div class="row mt-5 align-items-center subscribe-container">
                <div class="col-md-4 col-sm-6 mt-1">
                    <div class="signup">
                        <h6><i class="fa fa-envelope-o" aria-hidden="true"></i> Sign Up For Newsletter</h6>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mt-2 shopping-first">
                    <div class="shopping">
                        <h6>Shopping First For Coupon $25 Receive And...</h6>
                    </div>
                </div>
                <div class="col-md-4 colsm-12 footer-padding-bottom offset-max-sm--1 pb-3 pt-3 pb-sm-0 __inline-9">
                    <div class="text-nowrap mb-3 position-relative">
                        <form action="{{ route('subscription') }}" method="post">
                            @csrf
                            <input type="email" name="subscription_email" class="form-control subscribe-border text-align-direction p-12px" placeholder="{{ translate('your_Email_Address')}}" required>
                            <button class="subscribe-button" type="submit">
                                {{ translate('subscribe')}}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="page-footer font-small mdb-color rtl">
        <div class="pt-4 custom-light-primary-color-20">
            <div class="container text-center __pb-13px px-0 pb-0">

                <div class="row mt-3">
                    <div class="col-lg-3 col-md-5 footer-web-logo text-center text-md-start px-5">
                        <p class="m-0 footer-brand_desc text-def">
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod
                            tincidunt ut laoreet dolore magna aliquam erat volutpat.
                        </p>
                        <div class="max-sm-100 justify-content-start d-flex flex-wrap mt-md-3 mt-0 mb-md-3 text-align-direction footer-social_media">
                            @if ($web_config['social_media'])
                                @foreach ($web_config['social_media'] as $item)
                                    <span class="social-media ">
                                        @if ($item->name == 'twitter')
                                            <a class="social-btn text-white sb-light sb-{{ $item->name }} me-2 mb-2 d-flex justify-content-center align-items-center"
                                                target="_blank" href="{{ $item->link }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="16"
                                                    height="16" viewBox="0 0 24 24">
                                                    <g opacity=".3">
                                                        <polygon fill="#fff" fill-rule="evenodd"
                                                            points="16.002,19 6.208,5 8.255,5 18.035,19"
                                                            clip-rule="evenodd">
                                                        </polygon>
                                                        <polygon points="8.776,4 4.288,4 15.481,20 19.953,20 8.776,4">
                                                        </polygon>
                                                    </g>
                                                    <polygon fill-rule="evenodd"
                                                        points="10.13,12.36 11.32,14.04 5.38,21 2.74,21"
                                                        clip-rule="evenodd">
                                                    </polygon>
                                                    <polygon fill-rule="evenodd"
                                                        points="20.74,3 13.78,11.16 12.6,9.47 18.14,3"
                                                        clip-rule="evenodd">
                                                    </polygon>
                                                    <path
                                                        d="M8.255,5l9.779,14h-2.032L6.208,5H8.255 M9.298,3h-6.93l12.593,18h6.91L9.298,3L9.298,3z"
                                                        fill="currentColor">
                                                    </path>
                                                </svg>
                                            </a>
                                        @else
                                            <a class="social-btn text-white sb-light sb-{{ $item->name }} me-2 mb-2"
                                                target="_blank" href="{{ $item->link }}">
                                                <i class="{{ $item->icon }}" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    {{-- <div class="col-lg-3 col-md-5 footer-web-logo text-center text-md-start p-5">
                        <h6 class="text-uppercase font-semi-bold footer-header text-def footer-head">
                            Shortcut Links
                        </h6>
                        <ul class="widget-list __pb-10px">
                            <li class="widget-list-item">
                                <a class="widget-list-link" href="{{ url('/') }}">
                                    Home
                                </a>
                            </li>
                            <li class="widget-list-item">
                                <a class="widget-list-link"
                                    href="{{ route('products') }}">
                                    Shop
                                </a>
                            </li>
                            <li class="widget-list-item">
                                @if ($web_config['guest_checkout_status'] || auth('customer')->check())
                                    <a class="widget-list-link"
                                        href="{{ route('shop-cart') }}">
                                        Cart
                                    </a>
                                @else
                                    <a class="widget-list-link"
                                        href="{{ route('customer.auth.login') }}">
                                        Cart
                                    </a>
                                @endif
                            </li>
                            <li class="widget-list-item">
                                <a class="widget-list-link"
                                    href="#">
                                    Blogs
                                </a>
                            </li>
                            <li class="widget-list-item">
                                @if (auth('customer')->check())
                                    <a class="widget-list-link"
                                        href="{{route('user-account')}}">
                                        My Account
                                    </a>
                                @else
                                    <a class="widget-list-link"
                                        href="{{ route('customer.auth.login') }}">
                                        Sign in
                                    </a>
                                @endif
                            </li>

                        </ul>
                    </div> --}}

                    <div class="col-lg-9 col-md-7">
                        <div class="row footer-company-content_container">
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 footer-padding-bottom text-start">
                                <h6
                                    class="text-uppercase font-semi-bold footer-header text-def footer-head">
                                    Products</h6>
                                <ul class="widget-list __pb-10px">
                                    @php( $flash_deals = \App\Models\FlashDeal::where(['status' => 1, 'deal_type' => 'flash_deal'])->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('end_date', '>=', date('Y-m-d'))->first() )
                                    @if (isset($flash_deals))
                                        <li class="widget-list-item">
                                            <a class="widget-list-link"
                                                href="{{ route('flash-deals', [$flash_deals['id']]) }}">
                                                {{ translate('flash_deal') }}
                                            </a>
                                        </li>
                                    @endif
                                    <li class="widget-list-item">
                                        <a class="widget-list-link"
                                            href="{{ route('products', ['data_from' => 'featured', 'page' => 1]) }}">
                                            {{ translate('featured_products') }}
                                        </a>
                                    </li>
                                    <li class="widget-list-item">
                                        <a class="widget-list-link"
                                            href="{{ route('products', ['data_from' => 'latest', 'page' => 1]) }}">
                                            {{ translate('latest_products') }}
                                        </a>
                                    </li>
                                    <li class="widget-list-item">
                                        <a class="widget-list-link"
                                            href="{{ route('products', ['data_from' => 'best-selling', 'page' => 1]) }}">
                                            {{ translate('best_selling_product') }}
                                        </a>
                                    </li>
                                    <li class="widget-list-item">
                                        <a class="widget-list-link"
                                            href="{{ route('products', ['data_from' => 'top-rated', 'page' => 1]) }}">
                                            {{ translate('top_rated_product') }}
                                        </a>
                                    </li>

                                </ul>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 footer-padding-bottom text-start">
                                <h6
                                    class="text-uppercase font-semi-bold footer-header text-def footer-head">
                                    Our Company</h6>
                                @php($refund_policy = getWebConfig(name: 'refund-policy'))
                                @php($return_policy = getWebConfig(name: 'return-policy'))
                                @php($cancellation_policy = getWebConfig(name: 'cancellation-policy'))
                                @if (auth('customer')->check())
                                    <ul class="widget-list __pb-10px">
                                        <li class="widget-list-item">
                                            <a class="widget-list-link" href="{{ route('privacy-policy') }}">
                                                {{ translate('privacy_policy') }}
                                            </a>
                                        </li>

                                        <li class="widget-list-item">
                                            <a class="widget-list-link"
                                                href="{{ route('terms') }}">{{ translate('terms_&_conditions') }}
                                            </a>
                                        </li>

                                        @if (isset($refund_policy['status']) && $refund_policy['status'] == 1)
                                            <li class="widget-list-item">
                                                <a class="widget-list-link" href="{{ route('refund-policy') }}">
                                                    {{ translate('refund_policy') }}
                                                </a>
                                            </li>
                                        @endif

                                        @if (isset($return_policy['status']) && $return_policy['status'] == 1)
                                            <li class="widget-list-item">
                                                <a class="widget-list-link" href="{{ route('return-policy') }}">
                                                    {{ translate('return_policy') }}
                                                </a>
                                            </li>
                                        @endif

                                        @if (isset($cancellation_policy['status']) && $cancellation_policy['status'] == 1)
                                            <li class="widget-list-item">
                                                <a class="widget-list-link" href="{{ route('cancellation-policy') }}">
                                                    {{ translate('cancellation_policy') }}
                                                </a>
                                            </li>
                                        @endif

                                    </ul>
                                @else
                                    <ul class="widget-list __pb-10px">
                                        <li class="widget-list-item">
                                            <a class="widget-list-link"
                                                href="{{ route('customer.auth.login') }}">{{ translate('profile_info') }}</a>
                                        </li>
                                      
                                        <li class="widget-list-item">
                                            <a class="widget-list-link"
                                                href="{{ route('customer.auth.login') }}">{{ translate('wish_list') }}</a>
                                        </li>

                                        <li class="widget-list-item">
                                            <a class="widget-list-link"
                                                href="{{ route('track-order.index') }}">{{ translate('track_order') }}</a>
                                        </li>

                                        @if (isset($refund_policy['status']) && $refund_policy['status'] == 1)
                                            <li class="widget-list-item">
                                                <a class="widget-list-link"
                                                    href="{{ route('refund-policy') }}">{{ translate('refund_policy') }}</a>
                                            </li>
                                        @endif

                                        @if (isset($return_policy['status']) && $return_policy['status'] == 1)
                                            <li class="widget-list-item">
                                                <a class="widget-list-link"
                                                    href="{{ route('return-policy') }}">{{ translate('return_policy') }}</a>
                                            </li>
                                        @endif

                                        @if (isset($cancellation_policy['status']) && $cancellation_policy['status'] == 1)
                                            <li class="widget-list-item">
                                                <a class="widget-list-link"
                                                    href="{{ route('cancellation-policy') }}">{{ translate('cancellation_policy') }}</a>
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 footer-padding-bottom text-start">
                                <h6
                                    class="text-uppercase font-semi-bold footer-header text-def footer-head">
                                    Download App</h6>
                                <p class="m-0 footer-offer text-def">
                                    Save $3 with app & new user only
                                </p>
                                <ul class="widget-list __pb-10px">

                                    @if ($web_config['android']['status'])
                                        <li class="widget-list-item">
                                            <a class="widget-list-link" href="{{ $web_config['android']['link'] }}">
                                                <img src="{{ theme_asset(path: 'public/assets/front-end/png/google_app.png') }}"
                                                    alt="">
                                            </a>
                                        </li>
                                    @endif
                                    @if ($web_config['ios']['status'])
                                        <li class="widget-list-item">
                                            <a class="widget-list-link" href="{{ $web_config['ios']['link'] }}">
                                                <img src="{{ theme_asset(path: 'public/assets/front-end/png/apple_app.png') }}"
                                                    alt="">
                                            </a>
                                        </li>
                                    @endif

                                </ul>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 footer-padding-bottom text-start">
                                <h6 class="text-uppercase font-semi-bold footer-header text-def footer-head">
                                    Store Infomation
                                </h6>
                                    <ul class="widget-list __pb-10px">
                                        <li class="widget-list-item mb-0">
                                            <a class="widget-list-link d-flex align-items-center">
                                                <i class="fa fa-map-marker me-2 mt-2 mb-2 fa-lg"></i>
                                                {{ getWebConfig(name: 'shop_address') }}
                                            </a>
                                        </li>

                                        <li class="widget-list-item mb-0">
                                            <a class="widget-list-link d-flex align-items-center" href="{{ 'tel:' . $web_config['phone']->value }}">
                                                <i class="fa fa-phone  me-2 mt-2 mb-2 fa-lg"></i>
                                                {{ getWebConfig(name: 'company_phone') }}
                                            </a>
                                        </li>

                                        <li class="widget-list-item mb-0">
                                            <a class="widget-list-link d-flex align-items-center" href="{{ 'mailto:' . getWebConfig(name: 'company_email') }}"
                                                style="text-transform: none;">
                                                <i class="fa fa-envelope  me-2 mt-2 mb-2 fa-lg"></i>
                                                {{ getWebConfig(name: 'company_email') }}
                                            </a>
                                        </li>
                                    </ul>

                            </div>
                            {{-- <div class="col-sm-5 footer-padding-bottom offset-max-sm--1 pb-3 pb-sm-0">
                                <div class="mb-2">
                                    <h6 class="text-uppercase mobile-fs-12 font-semi-bold footer-header text-center text-sm-start">{{ translate('newsletter')}}</h6>
                                    <div class="text-center text-sm-start mobile-fs-12">{{ translate('subscribe_to_our_new_channel_to_get_latest_updates')}}</div>
                                </div>
                                <div class="text-nowrap mb-4 position-relative">
                                    <form action="{{ route('subscription') }}" method="post">
                                        @csrf
                                        <input type="email" name="subscription_email"
                                               class="form-control subscribe-border text-align-direction p-12px"
                                               placeholder="{{ translate('your_Email_Address')}}" required>
                                        <button class="subscribe-button" type="submit">
                                            {{ translate('subscribe')}}
                                        </button>
                                    </form>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    
                <ul class="widget-list widget-list-bottom-footer  __pb-10px">
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{ url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="widget-list-item">
                        <a class="widget-list-link"
                            href="{{ route('products') }}">
                            Shop
                        </a>
                    </li>
                    <li class="widget-list-item">
                        @if ($web_config['guest_checkout_status'] || auth('customer')->check())
                            <a class="widget-list-link"
                                href="{{ route('shop-cart') }}">
                                Cart
                            </a>
                        @else
                            <a class="widget-list-link"
                                href="{{ route('customer.auth.login') }}">
                                Cart
                            </a>
                        @endif
                    </li>
                    <li class="widget-list-item">
                        <a class="widget-list-link"
                            href="#">
                            Blogs
                        </a>
                    </li>
                    <li class="widget-list-item">
                        @if (auth('customer')->check())
                            <a class="widget-list-link"
                                href="{{route('user-account')}}">
                                My Account
                            </a>
                        @else
                            <a class="widget-list-link"
                                href="{{ route('customer.auth.login') }}">
                                Sign in
                            </a>
                        @endif
                    </li>

                </ul>
                    
                    <div class="hr"></div>

                    <div class="footer-additional w-100">
                        <div class="footer-additional-content">
                            <div class="foot-bottom-head text-def">
                                Information:
                            </div>
                            <div class="foot-bottom-content text-def">
                                4G Mobiles / Smartphones / Samsung Mobiles / Micromax Mobiles
                            </div>
                        </div>
                        <div class="footer-additional-content">
                            <div class="foot-bottom-head text-def">
                                Categories:
                            </div>
                            <div class="foot-bottom-content text-def">
                                @foreach($categories as $key => $category)
                                    <a href="{{route('products',['id'=> $category['id'],'data_from'=>'category','page'=>1])}}" class="text-def">
                                        {{ $category->name }} /
                                    </a>    
                                @endforeach
                            </div>
                        </div>
                        <div class="footer-additional-content">
                            <div class="foot-bottom-head text-def">
                                Products:
                            </div>
                            <div class="foot-bottom-content text-def">
                                {{-- @foreach($latest_products as $key => $product)
                                <a href="{{route('product',$product->slug)}}" class="text-def">
                                    {{ $product->name }} /
                                </a>
                                @endforeach --}}
                            </div>
                        </div>
                    </div>
                    
                    <div class="hr"></div>

                    <div class="footer-bottom w-100">
                        <div class="footer-bottom-content">
                            <div class="foot-bottom-payment">
                                <img src="{{asset('public/assets/front-end/img/paypal.png')}}">
                            </div>
                            <div class="foot-bottom-payment">
                                <img src="{{asset('public/assets/front-end/img/visa.png')}}">
                            </div>
                            <div class="foot-bottom-payment">
                                <img src="{{asset('public/assets/front-end/img/mastercard.png')}}">
                            </div>
                            <div class="foot-bottom-payment">
                                <img src="{{asset('public/assets/front-end/img/amax.png')}}">
                            </div>
                        </div>

                        <div class="footer-additional-content">
                            <div class="foot-bottom-copyright text-def">
                                {{ $web_config['copyright_text']->value }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @php($cookie = $web_config['cookie_setting'] ? json_decode($web_config['cookie_setting']['value'], true) : null)
        @if ($cookie && $cookie['status'] == 1)
            <section id="cookie-section"></section>
        @endif
    </footer>
</div>
