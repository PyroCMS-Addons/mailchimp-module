<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateWebhooksStream extends Migration
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
        'slug'          => 'webhooks',
        'title_column'  => 'webhook_name',
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
        "webhook_name" => [
            "type"   => "anomaly.field_type.text",
        ],
        "webhook_id" => [
            "type"   => "anomaly.field_type.text",
        ],
        "webhook_list_id" => [
            "type"   => "anomaly.field_type.text",
        ],        
        "webhook_url" => [
            "type"   => "anomaly.field_type.text",
        ],
        "webhook_events_subscribe" => [
            "type"   => "anomaly.field_type.boolean",
        ],
        "webhook_events_unsubscribe" => [
            "type"   => "anomaly.field_type.boolean",
        ],
        "webhook_events_profile" => [
            "type"   => "anomaly.field_type.boolean",
        ],        
        "webhook_events_upemail" => [
            "type"   => "anomaly.field_type.boolean",
        ],   
        "webhook_events_cleaned" => [
            "type"   => "anomaly.field_type.boolean",
        ],       
        "webhook_events_campaign" => [
            "type"   => "anomaly.field_type.boolean",
        ],    
        // api should be false
        "webhook_sources_api" => [
            "type"   => "anomaly.field_type.boolean",
        ],   
        "webhook_sources_admin" => [
            "type"   => "anomaly.field_type.boolean",
        ],   
        "webhook_sources_user" => [
            "type"   => "anomaly.field_type.boolean",
        ],   
        "webhook_enabled" => [
            "type"   => "anomaly.field_type.boolean",
        ],   


    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [
        "webhook_name" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'webhook_id' => [
            'translatable'      => false,
            'unique'            => true,
            'required'          => false,
        ],
        "webhook_list_id" => [
            'translatable'      => false,
            'unique'            => true,
            'required'          => false,
        ],        
        'webhook_url' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'webhook_events_subscribe' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'webhook_events_unsubscribe' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'webhook_events_profile' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'webhook_events_upemail' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'webhook_events_cleaned' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'webhook_events_campaign' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'webhook_sources_api' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        "webhook_sources_admin" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        "webhook_sources_user" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        "webhook_enabled" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],

    ];
}