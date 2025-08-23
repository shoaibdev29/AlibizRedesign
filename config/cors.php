<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'], // '*' se har route cover ho jayega (dev ke liye)
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:*',
        'http://127.0.0.1:*',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],   // false ki jagah empty array
    'max_age' => 0,            // false ki jagah 0
    'supports_credentials' => false,
];
