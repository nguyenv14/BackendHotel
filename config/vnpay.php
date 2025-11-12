<?php

return [
    'vnp_payment_url' => env('VNPAY_PAYMENT_URL'),
    'tmn_code' => env('VNPAY_TMN_CODE'),
    'hash_secret' => env('VNPAY_HASH_SECRET'),
    'version' => env('VNPAY_VERSION', '2.1.0'),
    'command' => env('VNPAY_COMMAND', 'pay'),
    'curr_code' => env('VNPAY_CURR_CODE', 'VND'),
    'locale' => env('VNPAY_LOCALE', 'vn'),
    'return_url' => env('VNPAY_RETURN_URL'),
    'ipn_url' => env('VNPAY_IPN_URL'),
    'order_type' => env('VNPAY_ORDER_TYPE', 'other'),
    'expired_minutes' => (int) env('VNPAY_EXPIRED_MINUTES', 15),
];
