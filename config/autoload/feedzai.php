<?php

return [
    'pulse' => [
        'token' => env('FEEDZAI_PULSE_AUTHORIZATION_BEARER_TOKEN'),
        'delay_update_transaction' => env('FEEDZAI_PULSE_DELAY_UPDATE_TRANSACTION', 10),
        'url' => env('FEEDZAI_PULSE_URL'),
    ],
    'jwt' => [
        'private_key_base64_encoded' => env('FEEDZAI_JWT_PRIVATE_KEY_BASE64_ENCODED'),
        'public_key_base64_encoded' => env('FEEDZAI_JWT_PUBLIC_KEY_BASE64_ENCODED'),
        'token_ttl_minutes' => env('FEEDZAI_JWT_TOKEN_TTL_MINUTES'),
        'audience' => env('APP_URL'),
        'issuer' => env('APP_URL'),
        'subject' => 'picpay',
        'key_id' => env('FEEDZAI_JWT_KEY_ID'),
    ],
];