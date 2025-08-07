{{--<div class="top-footer-section">
    <div class="d-flex justify-content-between" style="background:#1F3C74;">
    <div class="d-flex align-items-center">
        <span><i class="fa fa-envelope text-white" style="font-size: 1.4rem;color:white;"></i></span>
        <h5 class="m-0 ms-2" style="
font-size: 26px;
font-weight: 600;
line-height: 36px;
text-align: left;
">Sign Up For Newsletter</h5>
    </div>
    <div>
        <p>Shopping First For Coupon $25 Receive And...</p>
    </div>
        <div class="text-nowrap mb-4 position-relative">
            <form action="{{ route('subscription') }}" method="post" class="d-flex">
@csrf
<input type="email" name="subscription_email" class="form-control subscribe-border text-align-direction p-12px" placeholder="{{ translate('your_Email_Address')}}" required>
<button class="subscribe-button" type="submit">
    {{ translate('subscribe')}}
</button>
</form>
</div>
</div>
</div>
</div>--}}

<div class="__inline-9 rtl">
    <div class="d-flex justify-content-center text-center custom-light-primary-color text-md-start mt-3 p-4">
        <div class="col-md-3 d-flex justify-content-center">
            <div>
                <a href="{{route('about-us')}}">
                    <div class="text-center">
                        <img class="size-60" src="{{theme_asset(path: "public/assets/front-end/png/about-company.png")}}" alt="">
                    </div>
                    <div class="text-center">
                        <p class="m-0">
                            {{ translate('about_Company')}}
                        </p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-3 d-flex justify-content-center">
            <div>
                <a href="{{route('contacts')}}">
                    <div class="text-center">
                        <img class="size-60" src="{{theme_asset(path: "public/assets/front-end/png/contact-us.png")}}" alt="">
                    </div>
                    <div class="text-center">
                        <p class="m-0">
                            {{ translate('contact_Us')}}
                        </p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-3 d-flex justify-content-center">
            <div>
                <a href="{{route('helpTopic')}}">
                    <div class="text-center">
                        <img class="size-60" src="{{theme_asset(path: "public/assets/front-end/png/faq.png")}}" alt="">
                    </div>
                    <div class="text-center">
                        <p class="m-0">
                            {{ translate('FAQ')}}
                        </p>
                    </div>
                </a>
            </div>
        </div>

    </div>

    <footer class="page-footer font-small mdb-color rtl">
        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-4">
                    <div class="foot_text">
                        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
                    </div>
                    <div class="social" style="gap: 15px;">
                        <a href="#"><i class="fa fa-facebook-official fa-lg" style="color: #2874f0;" aria-hidden="true"></i></a>
                        <a href="#"><i class="fa fa-youtube-play fa-lg" style="color: red;" aria-hidden="true"></i></a>
                        <a href="#"><i class="fa fa-instagram fa-lg" style="color: orangered;" aria-hidden="true"></i></a>
                        <a href="#"><i class="fa fa-twitter fa-lg" style="color: #2874f0;" aria-hidden="true"></i></a>
                        <a href="#"><i class="fa fa-pinterest-square fa-lg" style="color: red;" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="foot_products">
                        <h5>Products</h5>
                    </div>
                    <ul class="list">
                        <li><a href="#">Prices Drop</a></li>
                        <li><a href="#">New Products</a></li>
                        <li><a href="#">Best Sellers</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Sitemap</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <div class="foot_products">
                        <h5>Our Company</h5>
                    </div>
                    <ul class="list">
                        <li><a href="#">Delivery</a></li>
                        <li><a href="#">Legal Notice</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Secure Payment</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <div class="foot_products">
                        <h5>Download App</h5>
                    </div>
                    <ul class="list">
                        <li><a href="#">Save $3 with app &<br> new user only</a></li>
                        <a href="#"><img src="image/GP.jpg"></a>
                        <a href="#"><img src="image/ios.jpg" style="margin-top: 10px;"></a>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <div class="foot_products">
                        <h5>Download App</h5>
                    </div>
                    <ul class="list">
                        <p><i class="fa fa-map-marker" aria-hidden="true"></i> EshopNepal, Kathmando</p>
                        <p><i class="fa fa-phone" aria-hidden="true"></i> +001 476 814</p>
                        <a href="#"><i class="fa fa-envelope-o" aria-hidden="true"></i> eshop@gmail.com</a>

                    </ul>
                </div>
            </div>
            <hr>
            <div class="info d-flex" style="gap: 15px;">
                <h5>Information:</h5>
                <div class="spans mt-1">
                    <span>4G Mobiles / Smartphones / Samsung Mobiles / Micromax Mobiles</span>
                </div>
            </div>
            <div class="info d-flex mt-2" style="gap: 15px;">
                <h5>Categoies:</h5>
                <div class="spans mt-1">
                    <span>Soft Mobile Covers / Pinted Back Covers / Ambrane P</span>
                </div>
            </div>
            <div class="info d-flex mt-2" style="gap: 15px;">
                <h5>Products:</h5>
                <div class="spans mt-1">
                    <span>8 GB Memory Cards / 22 GB Memory Cards / 16 GB Memory Cards</span>
                </div>
            </div>
            <hr>

            <div class="footer_flex_content d-flex justify-content-between">
                <div class="footer_end d-flex" style="gap: 15px;">
                    <div class="paypal">
                        <img src="image/paypal.png">
                    </div>
                    <div class="visa">
                        <img src="image/visa.png">
                    </div>
                    <div class="mastercard">
                        <img src="image/mastercard.png">
                    </div>
                    <div class="amax">
                        <img src="image/amax.png">
                    </div>
                </div>

                <div class="footer_text">
                    <p><i class="fa fa-copyright fa-lg" style="color: black;" aria-hidden="true"></i> 2024 Demo Store. All Rights Reserved. Designed by EshopNepal</p>
                </div>

            </div>
        </div>

        @php($cookie = $web_config['cookie_setting'] ? json_decode($web_config['cookie_setting']['value'], true):null)
        @if($cookie && $cookie['status']==1)
        <section id="cookie-section"></section>
        @endif
    </footer>
</div>
