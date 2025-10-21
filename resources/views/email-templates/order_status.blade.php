<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ translate('Order Status Update') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Roboto', sans-serif; 
            margin: 0; padding: 0; 
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h2 { margin: 0 0 10px 0; font-weight: 500; }
        .status {
            font-size: 1.2em;
            font-weight: 600;
            color: #007bff;
            margin: 10px 0;
        }
        .thank-you {
            font-size: 1.1em;
            color: #28a745;
            font-weight: 600;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 0.9em;
            color: #6c757d;
        }
        .social-icons img {
            width: 28px;
            height: 28px;
            margin: 0 6px;
            transition: transform 0.3s ease;
        }
        .social-icons img:hover { transform: translateY(-3px); }
    </style>
</head>
<body>
@php
    $companyPhone = getWebConfig('company_phone');
    $companyEmail = getWebConfig('company_email');
    $companyName = getWebConfig('company_name');
    $companyLogo = getWebConfig('company_web_logo');
@endphp

<div class="container">
    <div style="text-align: center;">
        <img src="{{ dynamicStorage('storage/app/public/company/'.$companyLogo) }}" alt="{{ $companyName }}" style="max-height: 60px; margin-bottom: 20px;">
        <h2>{{ translate('Order Update') }}</h2>
        <p>{{ translate('Your order ID') }} <strong>#{{ $order->id }}</strong> {{ translate('is now') }}:</p>
        <p class="status text-capitalize">{{ $status }}</p>

        @if(strtolower($status) == 'delivered')
            <p class="thank-you">{{ translate('Thank you for shopping with us!') }}</p>
            <p>{{ translate('We hope you enjoy your purchase. If you need any support, feel free to contact us.') }}</p>
        @endif

        <a href="{{ route('track-order.result', ['order_id'=>$order->id, 'phone_number'=>$order->is_guest ? $order['shipping_address_data']->phone : $order->customer->phone]) }}" 
           class="btn">
            {{ translate('Track Your Order') }}
        </a>
    </div>

    <div class="footer">
        <p><strong>{{ $companyName }}</strong></p>
        <p>{{ translate('Phone') }}: {{ $companyPhone }} | {{ translate('Email') }}: <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></p>
        <p><a href="{{ url('/') }}">{{ url('/') }}</a></p>
        <div class="social-icons">
            @php($socialMedia = \App\Models\SocialMedia::where('active_status', 1)->get())
            @foreach ($socialMedia as $item)
                <a href="{{ $item->link }}" target="_blank">
                    <img src="{{ dynamicAsset('public/assets/back-end/img/'.$item->name.'.png') }}" alt="{{ $item->name }}">
                </a>
            @endforeach
        </div>
    </div>
</div>

</body>
</html>
