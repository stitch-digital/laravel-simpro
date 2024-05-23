<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Simpro Base URL
    |--------------------------------------------------------------------------
    |
    | Set the base URL for the Simpro API. This option is not required when
    | using a multi-tenancy setup, as values are stored in the database.
    |
    */
    'base_url' => env('SIMPRO_BASE_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Simpro API Key
    |--------------------------------------------------------------------------
    |
    | Set the API key for the Simpro API. This option is not required when
    | using a multi-tenancy setup, as values are stored in the database.
    |
    */
    'api_key' => env('SIMPRO_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Multi Tenancy Configuration
    |--------------------------------------------------------------------------
    |
    | Configure multi-tenancy for Simpro. When enabled, the base URL and API
    | key are stored in the database. The tenant model specifies the
    | relationship between the tenant and the Simpro credentials.
    |
    */
    'multi_tenancy' => [
        'enabled' => 'false',
        'tenant_model' => 'App\Models\User',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable caching for Simpro GET requests. The cache driver can
    | be set to any of the Laravel cache drivers. The cache expiry time is
    | set in seconds.
    |
    */
    'cache' => [
        'enabled' => 'true',
        'driver' => config('cache.default', 'database'),
        'expire' => 120,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limit Configuration
    |--------------------------------------------------------------------------
    |
    | Set the rate limit for Simpro requests. The rate limit is set per second
    | and the threshold is the percentage of the rate limit that is accepted.
    | The threshold must be a number between 0 and 1 (e.g. 0.5 for 50%).
    |
    */

    'rate_limit' => [
        'per_second' => 10, // Simpro rate limit is 10 requests per second
        'threshold' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | OAuth Configuration (Partner Level Engagement)
    |--------------------------------------------------------------------------
    |
    | Simpro integration partners can use the Authorisation Code Grant method
    | to authenticate with the Simpro. Credentials would pre-exist in Simpro
    | so your application can be easily enabled by a client.
    |
     */

    'oauth' => [
        'enabled' => 'false',
        'authentication_url' => env('SIMPRO_AUTHENTICATION_URL', ''),
        'client_id' => env('SIMPRO_CLIENT_ID', ''),
        'client_secret' => env('SIMPRO_CLIENT_SECRET', ''),
        'redirect_uri' => env('SIMPRO_REDIRECT_URI', '/oauth2/accesscode'),
        'token_url' => env('SIMPRO_TOKEN_URL', ''),
    ],
];
