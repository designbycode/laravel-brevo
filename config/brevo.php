<?php

return [
    'api_key' => env('BREVO_API_KEY'),

    'default_list_id' => env('BREVO_DEFAULT_LIST_ID'),

    'sender' => [
        'email' => env('BREVO_SENDER_EMAIL', 'no-reply@example.com'),
        'name' => env('BREVO_SENDER_NAME', 'No Reply'),
    ],

];
