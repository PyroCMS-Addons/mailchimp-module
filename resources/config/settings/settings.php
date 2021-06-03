<?php

return [

    // "enable_clients_public" => [
    //     "type"   => "anomaly.field_type.select",
    //     "config" => [
    //         "options"       => 
    //         [
    //             '1' => 'Enabled',
    //             '0' => 'Disabled'
    //         ],
    //         "separator"     => ":",
    //         "default_value" => null,
    //         "button_type"   => "info",
    //         "handler"       => "options",
    //         "mode"          => "dropdown",
    //     ]
    // ],
    "mailchimp_server_prefix" => [
        "type"   => "anomaly.field_type.text",
    ],
    "mailchimp_api_key" => [
        "type"   => "anomaly.field_type.text",
    ],
    "mailchimp_test_email" => [
        "type"   => "anomaly.field_type.email",
    ],
    'mailchimp_sync_interval' => [
        "type"   => "anomaly.field_type.select",
        "config" => [
            "options"       =>
            [
                "everyMinute"   => "Every Minute",
                "everySixHours" => "Every Six Hours",
                "daily"         => "Daily",
                "weekly"        => "Weekly",
                "monthly"       => "Monthly",
                "quarterly"     => "Quarterly",
                "yearly"        => "Yearly",
            ],
            "separator"     => ":",
            "default_value" => 'weekly',
            "button_type"   => "info",
            "handler"       => "options",
            "mode"          => "dropdown",
        ]
    ],    
    'mailchimp_http_secure' => [
        "type"   => "anomaly.field_type.select",
        "config" => [
            "options"       =>
            [
                "https"     => "Yes (HTTPS)",
                "http"      => "No (HTTP)",
            ],
            "separator"     => ":",
            "default_value" => 'weekly',
            "button_type"   => "info",
            "handler"       => "options",
            "mode"          => "dropdown",
        ]
    ],   
    'mailchimp_pro' => [
        "type"   => "anomaly.field_type.select",
        "config" => [
            "options"       =>
            [
                "pro"     => "Yes I have Pro Subscription",
                "free"      => "No I do not have a Pro Subscription",
            ],
            "separator"     => ":",
            "default_value" => 'weekly',
            "button_type"   => "info",
            "handler"       => "options",
            "mode"          => "dropdown",
        ]
    ],       
];

