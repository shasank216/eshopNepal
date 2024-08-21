<style>
    .foot_products h5{
        font-weight: 600;
    }

    .widget-list-link {
        font-size: 16px;
        font-weight: 500;
        line-height: 24px;
        letter-spacing: 0.005em;
    }

    .location-store {
        font-size: 16px;
    font-weight: 500;
    line-height: 24px;
    letter-spacing: 0.005em;
    color: #5C5C5C;
    }

    .bottom-footer {
        font-size: 16px;
    font-weight: 600;
    line-height: 24px;
    letter-spacing: 0.005em;
    }

    .info .spans span {
        font-size: 16px;
    font-weight: 500;
    line-height: 24px;
    letter-spacing: 0.005em;
    }
</style>

<div class="background_class page-footer font-small mdb-color rtl" style="background-color: #1f3c74;">

    <div class="container">
        <div class="row mt-5 align-items-center">
            <div class="col-lg-4 mt-1">
                <div class="signup">
                    <h6><i class="fa fa-envelope-o" aria-hidden="true"></i> Sign Up For Newsletter</h6>
                </div>
            </div>
            <div class="col-lg-4 mt-2">
                <div class="shopping">
                    <h6>Shopping First For Coupon $25 Receive And...</h6>
                </div>
            </div>
            <div class="col-sm-4 footer-padding-bottom offset-max-sm--1 pb-3 pt-3 pb-sm-0 __inline-9">
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

<footer class="pt-5" style="background: #e9ebf1;">
    <div class="container">
        <div class="row mt-5">
            <div class="col-lg-4">
                <div class="foot_text">
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
                </div>
                <div class="social d-flex">
                    @if($web_config['social_media'])
                    @foreach ($web_config['social_media'] as $item)
                    <span class="social-media ">
                        @if ($item->name == "twitter")
                        <a class="social-btn text-white sb-light sb-{{$item->name}} me-2 mb-2 d-flex justify-content-center align-items-center" target="_blank" href="{{$item->link}}">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="16" height="16" viewBox="0 0 24 24">
                                <g opacity=".3">
                                    <polygon fill="#fff" fill-rule="evenodd" points="16.002,19 6.208,5 8.255,5 18.035,19" clip-rule="evenodd">
                                    </polygon>
                                    <polygon points="8.776,4 4.288,4 15.481,20 19.953,20 8.776,4">
                                    </polygon>
                                </g>
                                <polygon fill-rule="evenodd" points="10.13,12.36 11.32,14.04 5.38,21 2.74,21" clip-rule="evenodd">
                                </polygon>
                                <polygon fill-rule="evenodd" points="20.74,3 13.78,11.16 12.6,9.47 18.14,3" clip-rule="evenodd">
                                </polygon>
                                <path d="M8.255,5l9.779,14h-2.032L6.208,5H8.255 M9.298,3h-6.93l12.593,18h6.91L9.298,3L9.298,3z" fill="currentColor">
                                </path>
                            </svg>
                        </a>
                        @else
                        <a class="social-btn text-white sb-light sb-{{$item->name}} me-2 mb-2" target="_blank" href="{{$item->link}}">
                            <i class="{{$item->icon}}" aria-hidden="true"></i>
                        </a>
                        @endif
                    </span>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="col-lg-2">
                <div class="foot_products">
                    <h5>Products</h5>
                </div>
                <ul class="widget-list __pb-10px">
                    @php($flash_deals=\App\Models\FlashDeal::where(['status'=>1,'deal_type'=>'flash_deal'])->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date','>=',date('Y-m-d'))->first())
                        @if(isset($flash_deals))
                        <li class="widget-list-item">
                            <a class="widget-list-link" href="{{route('flash-deals',[$flash_deals['id']])}}">
                                {{ translate('flash_deal')}}
                            </a>
                        </li>
                        @endif
                        <li class="widget-list-item">
                            <a class="widget-list-link" href="{{route('products',['data_from'=>'featured','page'=>1])}}">
                                {{ translate('featured_products')}}
                            </a>
                        </li>
                        <li class="widget-list-item">
                            <a class="widget-list-link" href="{{route('products',['data_from'=>'latest','page'=>1])}}">
                                {{ translate('latest_products')}}
                            </a>
                        </li>
                        <li class="widget-list-item">
                            <a class="widget-list-link" href="{{route('products',['data_from'=>'best-selling','page'=>1])}}">
                                {{ translate('best_selling_product')}}
                            </a>
                        </li>
                        <li class="widget-list-item">
                            <a class="widget-list-link" href="{{route('products',['data_from'=>'top-rated','page'=>1])}}">
                                {{ translate('top_rated_product')}}
                            </a>
                        </li>

                </ul>
            </div>
            <div class="col-lg-2">
                <div class="foot_products">
                    <h5>Our Company</h5>
                </div>
                {{-- <h6 class="text-uppercase mobile-fs-12 font-semi-bold footer-header">{{ translate('account_&_shipping_info')}}</h6> --}}
                @php($refund_policy = getWebConfig(name: 'refund-policy'))
                @php($return_policy = getWebConfig(name: 'return-policy'))
                @php($cancellation_policy = getWebConfig(name: 'cancellation-policy'))
                @if(auth('customer')->check())
                <ul class="widget-list __pb-10px">
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('user-account')}}">
                            {{ translate('profile_info')}}
                        </a>
                    </li>

                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('track-order.index')}}">
                            {{ translate('track_order')}}
                        </a>
                    </li>

                    @if(isset($refund_policy['status']) && $refund_policy['status'] == 1)
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('refund-policy')}}">
                            {{ translate('refund_policy')}}
                        </a>
                    </li>
                    @endif

                    @if(isset($return_policy['status']) && $return_policy['status'] == 1)
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('return-policy')}}">
                            {{ translate('return_policy')}}
                        </a>
                    </li>
                    @endif

                    @if(isset($cancellation_policy['status']) && $cancellation_policy['status'] == 1)
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('cancellation-policy')}}">
                            {{ translate('cancellation_policy')}}
                        </a>
                    </li>
                    @endif

                </ul>
                @else
                <ul class="widget-list __pb-10px">
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('customer.auth.login')}}">{{ translate('profile_info')}}</a>
                    </li>
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('customer.auth.login')}}">{{ translate('wish_list')}}</a>
                    </li>

                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('track-order.index')}}">{{ translate('track_order')}}</a>
                    </li>

                    @if(isset($refund_policy['status']) && $refund_policy['status'] == 1)
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('refund-policy')}}">{{ translate('refund_policy')}}</a>
                    </li>
                    @endif

                    @if(isset($return_policy['status']) && $return_policy['status'] == 1)
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('return-policy')}}">{{ translate('return_policy')}}</a>
                    </li>
                    @endif

                    @if(isset($cancellation_policy['status']) && $cancellation_policy['status'] == 1)
                    <li class="widget-list-item">
                        <a class="widget-list-link" href="{{route('cancellation-policy')}}">{{ translate('cancellation_policy')}}</a>
                    </li>
                    @endif
                </ul>
                @endif
            </div>
            <div class="col-lg-2">
                @if($web_config['ios']['status'] || $web_config['android']['status'])
                <div class="download-section">
                    <h6 style="    font-size: 20px;" class="text-uppercase font-weight-bold align-items-center">
                        Download App
                    </h6>
                </div>
                @endif
                <div class="download-section mt-4">
                    <p>
                        Save $3 with app &<br> new user only
                    </p>
                </div>
                <div class="store-contents d-flex justify-content-center flex-column pr-lg-4">
                    @if($web_config['ios']['status'])
                    <div class="me-2 mb-2">
                        <a class="" href="{{ $web_config['ios']['link'] }}" role="button">
                            <img class="w-100" src="{{theme_asset(path: "public/assets/front-end/png/apple_app.png")}}" alt="">
                        </a>
                    </div>
                    @endif

                    @if($web_config['android']['status'])
                    <div class="me-2 mb-2">
                        <a href="{{ $web_config['android']['link'] }}" role="button">
                            <img class="w-100" src="{{theme_asset(path: "public/assets/front-end/png/google_app.png")}}" alt="">
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-2">
                <div class="foot_products">
                    <h5 style="font-size: 20px;">Store Infomation</h5>
                </div>
                {{-- <ul class="list p-0"> --}}
                    <p class="location-store"><i class="fa fa-map-marker" aria-hidden="true"></i> {{ getWebConfig(name: 'shop_address')}}</p>
                    <a class="widget-list-link location-store" href="{{ 'tel:'.$web_config['phone']->value }}">
                        <span class="">
                            <i class="fa fa-phone  me-2 mt-2 mb-2"></i>
                            <span class="direction-ltr">
                                {{getWebConfig(name: 'company_phone')}}
                            </span>
                        </span>
                    </a>
                    <a class="widget-list-link location-store"
                        href="{{ 'mailto:'.getWebConfig(name: 'company_email') }}">
                        <span><i class="fa fa-envelope  me-2 mt-2 mb-2"></i> {{getWebConfig(name: 'company_email')}} </span>
                    </a>

                {{-- </ul> --}}
            </div>
        </div>
        <hr>
        <div class="info d-flex mt-3" style="gap: 15px;">
            <h5 class="m-0 bottom-footer">Information:</h5>
            <div class="spans">
                <span>4G Mobiles / Smartphones / Samsung Mobiles / Micromax Mobiles</span>
            </div>
        </div>
        <div class="info d-flex mt-2" style="gap: 15px;">
            <h5 class="m-0 bottom-footer">Categoies:</h5>
            <div class="spans">
                <span>Soft Mobile Covers / Pinted Back Covers / Ambrane P</span>
            </div>
        </div>
        <div class="info d-flex mt-2 pb-3" style="gap: 15px;">
            <h5 class="m-0 bottom-footer">Products:</h5>
            <div class="spans">
                <span>8 GB Memory Cards / 22 GB Memory Cards / 16 GB Memory Cards</span>
            </div>
        </div>
        <hr>

        <div class="footer_flex_content d-flex justify-content-between mt-3 pb-3">
            <div class="footer_end d-flex" style="gap: 15px;">
                <div class="paypal">
                    <img src="{{asset('public/assets/front-end/img/paypal.png')}}">
                </div>
                <div class="visa">
                    <img src="{{asset('public/assets/front-end/img/visa.png')}}">
                </div>
                <div class="mastercard">
                    <img src="{{asset('public/assets/front-end/img/mastercard.png')}}">
                </div>
                <div class="amax">
                    <img src="{{asset('public/assets/front-end/img/amax.png')}}">
                </div>
            </div>

            <div class="footer_text">
                <p><i class="fa fa-copyright fa-lg" style="color: black;" aria-hidden="true"></i> {{ $web_config['copyright_text']->value }}</p>
            </div>

        </div>
    </div>
</footer>
