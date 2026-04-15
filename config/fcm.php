<?php

return [
    /**
     * The default FCM driver.
     * Supported: "http"
     */
    'driver' => env('FCM_DRIVER', 'http'),

    /**
     * The path to the Firebase credentials file.
     */
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', base_path('elite-guard-e8261-firebase.json')),
    ],
];
