<?php

$localBcaAccountNumber = env('APP_ENV', 'production') === 'local'
    ? '1935 0009 1200'
    : null;

return [
    'default_destination_bank' => env('PAYMENT_DEFAULT_DESTINATION_BANK', 'bca'),

    'destination_account_name' => env('PAYMENT_DESTINATION_ACCOUNT_NAME', 'Split TheBill'),

    'destination_banks' => [
        'bca' => [
            'name' => 'BCA',
            'account_number' => env('PAYMENT_BCA_ACCOUNT_NUMBER', $localBcaAccountNumber),
            'logo' => 'bca.svg',
        ],
        'bri' => [
            'name' => 'BRI',
            'account_number' => env('PAYMENT_BRI_ACCOUNT_NUMBER'),
            'logo' => 'bri.svg',
        ],
        'bni' => [
            'name' => 'BNI',
            'account_number' => env('PAYMENT_BNI_ACCOUNT_NUMBER'),
            'logo' => 'bni.svg',
        ],
    ],
];
