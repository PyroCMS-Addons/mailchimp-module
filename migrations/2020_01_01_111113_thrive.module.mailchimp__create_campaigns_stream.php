<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateCampaignsStream extends Migration
{

    /**
     * This migration creates the stream.
     * It should be deleted on rollback.
     *
     * @var bool
     */
    protected $delete = true;

    /**
     * The stream definition.
     *
     * @var array
     */
    protected $stream = [
        'slug' => 'campaigns',
        'title_column' => 'campaign_name',
        'translatable'  => false,
        'versionable'   => false,
        'trashable'     => true,
        'searchable'    => true,
        'sortable'      => true,
    ];

    /**
     * The addon fields.
     *
     * @var array
     */
    protected $fields = [
        "campaign_name" => [
            "type"   => "anomaly.field_type.text",
        ],
        'campaign_str_id' => [
            "type"   => "anomaly.field_type.text",
            "config" => [
                "default_value" => null,
            ]
        ],        
        'campaign_type' => [
            "type"   => "anomaly.field_type.select",
            "config" => [
                "options"       =>
                [
                    "regular"       => "Regular",
                    "variate"       => "Variate",
                    "rss"           => "Rss",
                    "absplit"       => "AB Split",
                    "plaintext"     => "Plain Text",
                ],
                "separator"     => ":",
                "default_value" => 'regular',
                "button_type"   => "info",
                "handler"       => "options",
                "mode"          => "dropdown",
            ]
        ],     
        'list_id' => [
            "type"   => "anomaly.field_type.text",
            "config" => [
                "default_value" => null,
            ]
        ],
        'campaign_sync_status' => [
            "type"   => "anomaly.field_type.text",
            "config"  =>[
                "default_value" => ""
            ]
        ],
        'status' => [
            "type"   => "anomaly.field_type.text",
        ],        

        'campaign_subject_line' => [
            "type"   => "anomaly.field_type.text",
            "config" => [
                "default_value" => null,
            ]
        ],   
        'campaign_from_name' => [
            "type"   => "anomaly.field_type.text",
            "config" => [
                "default_value" => null,
            ]
        ],    
        'campaign_reply_to' => [
            "type"   => "anomaly.field_type.email",
        ],    
        'campaign_template_id' => [
            "type"   => "anomaly.field_type.text",
            "config" => [
                "default_value" => null,
            ]
        ],                     
    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [
        "campaign_name" => [
            'translatable'      => false,
            'unique'            => true,
            'required'          => true,
        ],
        'campaign_str_id' => [
            'translatable'      => false,
            'unique'            => true,
            'required'          => true,
        ],        
        'list_id' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'campaign_type' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'status' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],        
        'campaign_sync_status' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'campaign_subject_line' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'campaign_from_name' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'campaign_reply_to' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        // have considered this as unique
        // but perhaps during alpha lets 
        // leave this loosy.
        'campaign_template_id' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
           
    ];

}