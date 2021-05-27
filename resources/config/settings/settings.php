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
];

