<?php

return [
    'currency' => env('PAYMENT_CURRENCY', 'USD'),

    'hiring_fees' => [
        'entry' => 50.00,
        'mid' => 100.00,
        'senior' => 200.00,
        'lead' => 300.00,
        'default' => 100.00,
    ],
];
