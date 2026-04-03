<?php

declare(strict_types=1);

return [
    'default' => env('PAYMENT_DRIVER', 'stub'),

    'webhook_secret' => env('PAYMENT_WEBHOOK_SECRET', 'stub-secret'),

    'drivers' => [
        'stub' => [],
    ],
];
