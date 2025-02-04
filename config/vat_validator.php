<?php

return [
    'use_sandbox' => env('HMRC_USE_SANDBOX', false),

    'live' => [
        'api_base' => 'https://api.service.hmrc.gov.uk',
        'oauth_url' => 'https://api.service.hmrc.gov.uk/oauth/token',
        'client_id' => env('HMRC_CLIENT_ID'),
        'client_secret' => env('HMRC_CLIENT_SECRET'),
        'grant_type' => env('HMRC_GRANT_TYPE', 'client_credentials'),
        'scope' => env('HMRC_SCOPE', 'read:vat'),
    ],

    'sandbox' => [
        'api_base' => 'https://test-api.service.hmrc.gov.uk',
        'oauth_url' => 'https://test-api.service.hmrc.gov.uk/oauth/token',
        'client_id' => env('HMRC_CLIENT_ID'),
        'client_secret' => env('HMRC_CLIENT_SECRET'),
        'grant_type' => env('HMRC_GRANT_TYPE', 'client_credentials'),
        'scope' => env('HMRC_SCOPE', 'read:vat'),
    ],
];
