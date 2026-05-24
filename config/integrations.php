<?php

return [
    'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    'twilio_sid' => env('TWILIO_ACCOUNT_SID'),
    'twilio_token' => env('TWILIO_AUTH_TOKEN'),
    'twilio_phone' => env('TWILIO_PHONE_NUMBER'),
    'fcm_api_key' => env('FCM_API_KEY'),
    'elasticsearch_hosts' => [
        env('ELASTICSEARCH_HOST', 'localhost:9200'),
    ],
];
