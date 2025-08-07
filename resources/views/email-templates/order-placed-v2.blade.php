<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ translate('Order Placed') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Roboto', sans-serif; 
            -ms-text-size-adjust: 100%; 
            -webkit-text-size-adjust: 100%; 
            padding: 0; 
            margin: 0; 
            color: #333333;
            line-height: 1.6;
        }
        .d-flex { display: flex; }
        .align-items-center { align-items: center; }
        .m-auto { margin: auto; }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .credit-section { 
            padding: 20px 0; 
            width: 650px; 
            margin: 30px auto; 
            border-top: 1px solid #e0e0e0;
        }
        .order-action-btn { 
            background-color: #ffffff; 
            width: 90%; 
            margin: 30px auto; 
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .order-main-table { 
            width: 650px; 
            background-color: white; 
            margin: 50px auto 0; 
            padding: 40px; 
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .order-main-sub-table { 
            background-color: rgb(255, 255, 255); 
            width: 90%; 
            margin: 0 auto 20px; 
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .color-green { color: #28a745; }
        .color-primary { color: #007bff; }
        .color-danger { color: #dc3545; }
        .table-header-items { 
            background-color: #f5f5f5; 
            padding: 12px 8px;
            font-weight: 500;
            border-radius: 4px;
        }
        .table-header-items th { 
            padding: 12px 8px; 
            font-weight: 500;
        }
        .calculation-section { 
            width: 46%; 
            margin-left: 41%; 
            display: inline;
        }
        .m-10px { margin: 10px; }
        .width-100 { width: 100%; }
        .width-50 { width: 50%; }
        .width-50px { width: 50px; }
        .h-50px { height: 50px; }
        .pt-20px { padding-top: 20px; }
        .pb-2 { padding-bottom: 20px; }
        .mt-1 { margin-top: 10px; }
        .p-1 { padding: 8px; }
        .p-2 { padding: 16px; }
        .p-3 { padding: 12px 24px; }
        .radius-5 { border-radius: 5px; }
        .border-0 { border: none; }
        .btn { 
            display: inline-block;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        .text-capitalize { text-transform: capitalize; }
        .border-bottom { border-bottom: 1px solid #e0e0e0; }
        .bg-primary { background-color: #f8f9fa; }
        .ps-1 { padding-left: 4px; }
        .ps-3 { padding-left: 16px; }
        .me-1 { margin-right: 4px; }
        .social-icon {
            transition: transform 0.3s ease;
        }
        .social-icon:hover {
            transform: translateY(-3px);
        }
        .company-logo {
            max-height: 60px;
            width: auto;
        }
        .shop-logo {
            max-height: 40px;
            width: auto;
            border-radius: 4px;
            margin-right: 10px;
        }
        .track-order-btn {
            display: inline-block;
            margin: 20px 0;
            font-weight: 500;
        }
        .product-name {
            font-weight: 500;
            color: #333;
        }
        .total-row {
            font-weight: 600;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
<?php

use App\Models\Order;
use App\Models\Seller;
use App\Models\Shop;
use App\User;

$companyPhone = getWebConfig(name: 'company_phone');
$companyEmail = getWebConfig(name: 'company_email');
$companyName = getWebConfig(name: 'company_name');
$companyLogo = getWebConfig(name: 'company_web_logo');
$order = Order::find($id);

if ($order->seller_is == 'seller') {
    $seller = Seller::find($order->seller_id);
    $shop = Shop::find($seller->id);
}

if ($order->is_guest) {
    $userPhone = $order['shipping_address_data'] ? $order['shipping_address_data']->phone : $order['billing_address_data']->phone;
} else {
    $userPhone = User::find($order->customer_id)->phone;
}
?>

<div class="order-main-table">
    <table class="order-main-sub-table">
        <tbody>
        <tr>
            <td>
                <h2 style="margin: 0 0 10px 0; font-weight: 500;">{{ translate('thanks_for_the_order') }}</h2>
                <h3 class="color-green" style="margin: 0; font-weight: 500;">{{ translate('Your_order_ID') }}: {{$id}}</h3>
            </td>
            <td>
                <div class="text-end me-1">
                    <img src="{{dynamicStorage(path: 'storage/app/public/company/'.$companyLogo) }}" class="company-logo" alt="{{ $companyName }}"/>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="order-action-btn">
        <table class="width-100">
            <tbody>
            <tr class="width-100">
                <td class="width-50 mt-1">
                    <div class="text-start mt-1">
                        <strong style="font-size: 16px;">{{ translate('vendor_details') }}</strong>
                        <br>
                        @if ($order->seller_is == 'seller')
                            <div class="d-flex align-items-center mt-1">
                                <img src="{{dynamicStorage(path: 'storage/app/public/shop/'.$shop->image) }}" 
                                     class="shop-logo" alt="{{ $shop->name }}"/>
                                <span class="ps-1">{{$shop->name}}</span>
                            </div>
                        @else
                            <div class="d-flex align-items-center mt-1">
                                <span>{{ translate('inhouse_products') }}</span>
                            </div>
                        @endif
                    </div>
                </td>
                <td class="width-50">
                    <div class="text-end mt-1">
                        <strong style="font-size: 16px;">{{ translate('payment_details') }}</strong>
                        <br>
                        <div class="mt-1">
                            <span>{{ str_replace('_',' ',$order->payment_method) }}</span><br>
                            <span style="color: {{$order->payment_status=='paid'?'#28a745':'#dc3545'}}; font-weight: 500;">
                              {{$order->payment_status}}
                            </span><br>
                            <span style="color: #6c757d;">
                              {{ date('d M Y H:i',strtotime($order['created_at'])) }}
                            </span>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <?php
    $subtotal = 0;
    $total = 0;
    $subTotal = 0;
    $totalTax = 0;
    $totalShippingCost = 0;
    $totalDiscountOnProduct = 0;
    $extraDiscount = 0;
    ?>
    
    <div class="order-action-btn">
        <div class="p-2">
            <table class="width-100" style="border-collapse: collapse;">
                <thead>
                    <tr class="table-header-items">
                        <th class="text-start">{{ translate('SL') }}</th>
                        <th class="text-start">{{ translate('Ordered_Items') }}</th>
                        <th class="text-end">{{ translate('Unit_price') }}</th>
                        <th class="text-center">{{ translate('QTY') }}</th>
                        <th class="text-end">{{ translate('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($order->details as $key=>$details)
                        <?php $subtotal = ($details['price']) * $details->qty; ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">

                        <td class="p-1">{{$key+1}}</td>
                        <td class="p-1">
                            <span class="product-name">
                                {{$details['product']?Str::limit($details['product']->name,55):''}}
                            </span>
                            <br>
                            @if ($details['variant']!=null)
                                <span style="color: #6c757d; font-size: 0.9em;">
                                    {{ translate('variation') }}: {{$details['variant']}}
                                </span>
                            @endif
                        </td>
                        <td class="p-1 text-end">{{ webCurrencyConverter(amount: $details['price']) }}</td>
                        <td class="p-1 text-center">{{ $details->qty }}</td>
                        <td class="p-1 text-end">{{ webCurrencyConverter(amount: $subtotal) }}</td>
                    </tr>
                        <?php
                        $subTotal += $details['price'] * $details['qty'];
                        $totalTax += $details['tax'];
                        $totalShippingCost += $details->shipping ? $details->shipping->cost : 0;
                        $totalDiscountOnProduct += $details['discount'];
                        $total += $subtotal;
                        ?>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <?php
    if ($order['extra_discount_type'] == 'percent') {
        $extraDiscount = ($subTotal / 100) * $order['extra_discount'];
    } else {
        $extraDiscount = $order['extra_discount'];
    }
    $shipping = $order['shipping_cost'];
    ?>

    <div class="order-action-btn">
        <table class="width-100">
            <tr>
                <th></th>
                <td class="text-end">
                    <table class="text-capitalize calculation-section" style="border-collapse: collapse; width: 100%;">
                        <tbody>
                        <tr>
                            <th class="pb-2 text-start">{{ translate('sub_total') }}:</th>
                            <td class="pb-2 text-end">{{ webCurrencyConverter(amount: $subTotal) }}</td>
                        </tr>
                        <tr>
                            <td class="pb-2 text-start">{{ translate('tax') }}:</td>
                            <td class="pb-2 text-end">{{ webCurrencyConverter(amount: $totalTax) }}</td>
                        </tr>
                        @if($order->order_type == 'default_type')
                            <tr>
                                <td class="pb-2 text-start">{{ translate('shipping') }}:</td>
                                <td class="pb-2 text-end">{{ webCurrencyConverter(amount: $shipping) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="pb-2 text-start">{{ translate('coupon_discount') }}:</td>
                            <td class="pb-2 text-end">
                                - {{ webCurrencyConverter(amount: $order->discount_amount) }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="pb-2 text-start">{{ translate('discount_on_product') }}:</td>
                            <td class="pb-2 text-end">
                                - {{ webCurrencyConverter(amount: $totalDiscountOnProduct) }}
                            </td>
                        </tr>
                        @if ($order->order_type != 'default_type')
                            <tr class="border-bottom pb-2">
                                <th class="pb-2 text-start">{{ translate('extra_discount') }}:</th>
                                <td class="pb-2 text-end">
                                    - {{ webCurrencyConverter(amount: $extraDiscount) }}
                                </td>
                            </tr>
                        @endif
                        <tr class="total-row" style="background-color: #f8f9fa; padding: 12px 0;">
                            <th class="pb-2 text-start">{{ translate('total') }}:</th>
                            <td class="pb-2 text-end" style="padding-right: 16px;">
                                {{ webCurrencyConverter(amount: $order->order_amount) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="order-action-btn" style="text-align: center;">
        <p style="margin-bottom: 20px;">{{ translate('You_can_track_your_order_by_clicking_the_below_button') }}</p>
        <a href="{{ route('track-order.result', ['order_id'=>$order->id, 'phone_number'=>$userPhone]) }}"
           class="track-order-btn btn btn-primary radius-5">
            {{ translate('track_your_order') }}
        </a>
    </div>
</div>

<div class="credit-section">
    <table class="m-auto width-100">
        <tbody>
        <tr>
            <th class="text-start">
                <h1 style="font-size: 20px; margin: 0 0 10px 0; color: #333;">
                    {{ $companyName }}
                </h1>
            </th>
        </tr>
        <tr>
            <th class="text-start">
                <div style="margin-bottom: 5px;"><strong>{{ translate('phone') }}:</strong> {{ $companyPhone }}</div>
                <div style="margin-bottom: 5px;"><strong>{{ translate('website') }}:</strong> <a href="{{ url('/') }}" style="color: #007bff; text-decoration: none;">{{ url('/') }}</a></div>
                <div style="margin-bottom: 5px;"><strong>{{ translate('email') }}:</strong> <a href="mailto:{{ $companyEmail }}" style="color: #007bff; text-decoration: none;">{{ $companyEmail }}</a></div>
            </th>
        </tr>
        <tr>
            @php($socialMedia = \App\Models\SocialMedia::where('active_status', 1)->get())
            @if(isset($socialMedia))
                <th class="text-start pt-20px">
                    <div class="width-100 d-flex">
                        @foreach ($socialMedia as $item)
                            <div style="margin-right: 15px;">
                                <a href="{{$item->link}}" target="_blank">
                                    <img src="{{dynamicAsset(path: 'public/assets/back-end/img/'.$item->name.'.png') }}" alt="{{ $item->name }}"
                                         class="social-icon h-50px width-50px">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </th>
            @endif
        </tr>
        </tbody>
    </table>
</div>

</body>
</html>