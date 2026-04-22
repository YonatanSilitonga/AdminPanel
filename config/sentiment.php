<?php

return [
    'sentiment' => [
        /*
         * Enable/disable sentiment analysis service
         */
        'enabled' => env('SENTIMENT_SERVICE_ENABLED', true),
        
        /*
         * Python Flask API URL
         */
        'url' => env('SENTIMENT_API_URL', 'http://127.0.0.1:5000'),
        
        /*
         * Timeout in seconds for API requests
         */
        'timeout' => env('SENTIMENT_API_TIMEOUT', 30),
        
        /*
         * Auto-save predictions to database
         */
        'auto_save' => env('SENTIMENT_AUTO_SAVE', true),
    ],
];
