<?php 

return [
    /*
    |--------------------------------------------------------------------------
    | Api keys
    |--------------------------------------------------------------------------
    |
    | This option controls the api keys for a diferents circle apis.
    |
    | Circle apis: "payments", "payouts"
    |
    */

    'payments_api_key' => env('CIRCLE_PAYMENTS_APY_KEY', ''),
    'payouts_api_key' => env('CIRCLE_PAYOUTS_APY_KEY', ''),
];