<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Smart Tourism Admin Panel.
    | Customize these settings based on your application needs.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'per_page' => env('ADMIN_PER_PAGE', 15),
        'per_page_options' => [10, 15, 25, 50, 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'enabled' => env('ADMIN_UPLOADS_ENABLED', true),
        'max_file_size' => env('ADMIN_MAX_FILE_SIZE', 5120), // in KB (5MB)
        'max_file_size_image' => env('ADMIN_MAX_FILE_SIZE_IMAGE', 2048), // in KB (2MB)
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'allowed_document_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
        'storage_path' => env('ADMIN_STORAGE_PATH', 'admin-uploads'),
        'image_storage_path' => env('ADMIN_IMAGE_STORAGE_PATH', 'admin-uploads/images'),
        'chunk_size' => 1024 * 1024, // 1MB chunks for upload
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Configuration
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'enabled' => env('ADMIN_MAINTENANCE_MODE', false),
        'message' => env('ADMIN_MAINTENANCE_MESSAGE', 'Admin panel is under maintenance. Please try again later.'),
        'admin_can_access' => env('ADMIN_MAINTENANCE_ADMIN_ACCESS', true),
        'super_admin_bypass' => env('ADMIN_SUPER_ADMIN_BYPASS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Settings
    |--------------------------------------------------------------------------
    */
    'analytics' => [
        'enabled' => env('ADMIN_ANALYTICS_ENABLED', true),
        'cache_duration' => env('ADMIN_ANALYTICS_CACHE', 3600), // seconds
        'graph_points' => 30, // Number of data points in charts
        'date_range_days' => 90, // Default date range for analytics
        'log_activities' => true, // Log all admin activities
    ],

    /*
    |--------------------------------------------------------------------------
    | AI & Chatbot Configuration
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'enabled' => env('ADMIN_AI_ENABLED', true),
        'chatbot_enabled' => env('ADMIN_CHATBOT_ENABLED', true),
        'recommendation_engine' => env('ADMIN_RECOMMENDATION_ENGINE', 'basic'), // basic, advanced, ml
        'conversation_history_limit' => 50, // Max messages to keep in memory
        'auto_flag_sensitivity' => 'medium', // low, medium, high
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'enable_2fa' => env('ADMIN_2FA_ENABLED', false),
        'session_timeout' => env('ADMIN_SESSION_TIMEOUT', 3600), // seconds (1 hour)
        'idle_timeout' => env('ADMIN_IDLE_TIMEOUT', 1800), // seconds (30 minutes)
        'password_min_length' => 8,
        'password_require_special_chars' => true,
        'password_require_numbers' => true,
        'password_require_uppercase' => true,
        'login_attempts_limit' => 5,
        'login_lockout_duration' => 900, // seconds (15 minutes)
        'ip_whitelist_enabled' => false,
        'ip_whitelist' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => true,
        'email_enabled' => true,
        'queue_notifications' => true,
        'review_notifications' => true,
        'report_notifications' => true,
        'system_notifications' => true,
        'notification_channels' => ['database', 'mail'], // database, mail, slack, sms
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Settings
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'show_statistics' => true,
        'show_recent_activities' => true,
        'show_quick_actions' => true,
        'show_system_health' => true,
        'widgets_per_row' => 3,
        'refresh_interval' => 30, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Moderation Settings
    |--------------------------------------------------------------------------
    */
    'moderation' => [
        'enabled' => true,
        'auto_flag_keywords' => [],
        'require_approval' => [
            'reviews' => true,
            'events' => false,
            'destinations' => false,
        ],
        'spam_detection' => true,
        'nsfw_detection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | API & External Services
    |--------------------------------------------------------------------------
    */
    'api' => [
        'enable_api_keys' => true,
        'api_key_expiration_days' => 365,
        'rate_limiting' => [
            'enabled' => true,
            'requests_per_minute' => 60,
            'requests_per_hour' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup & Database Settings
    |--------------------------------------------------------------------------
    */
    'backups' => [
        'enabled' => env('ADMIN_BACKUPS_ENABLED', false),
        'auto_backup' => false,
        'backup_frequency' => 'daily', // daily, weekly, monthly
        'retention_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'log_all_activities' => true,
        'log_failed_logins' => true,
        'log_permission_denied' => true,
        'log_data_changes' => true,
        'retention_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'cache_queries' => true,
        'cache_duration' => 3600, // seconds
        'eager_loading' => true,
        'pagination_cache' => true,
    ],

];
