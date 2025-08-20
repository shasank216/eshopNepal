<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ translate('Order Status Updated') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f8f9fa; padding:0; margin:0; color:#333; }
        .order-main-table { width:650px; margin:50px auto; background:#fff; padding:40px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
        .status { font-weight: 600; font-size:1.2em; color: #007bff; }
    </style>
</head>
<body>
<div class="order-main-table">
    <h2>{{ translate('Your Order Status Has Been Updated') }}</h2>
    <h3>{{ translate('Order ID') }}: {{ $id }}</h3>
    <p>{{ translate('Current Status') }}: <span class="status">{{ $status }}</span></p>

    <div style="margin-top: 30px; text-align:center;">
        <a href="{{ route('track-order.result', ['order_id'=>$id, 'phone_number'=>auth()->user()?->phone ?? '']) }}"
           style="display:inline-block; background:#007bff; color:#fff; padding:10px 20px; border-radius:5px; text-decoration:none;">
            {{ translate('Track Your Order') }}
        </a>
    </div>
</div>
</body>
</html>
