<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateSubscribersStream extends Migration
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
        'slug' => 'subscribers',
        'title_column' => 'subscriber_email',
        'translatable'  => false,
        'versionable'   => false,
        'trashable'     => true,
        'searchable'    => true,
        'sortable'      => false,
    ];

    /**
     * The addon fields.
     *
     * @var array
     */
    protected $fields = [
        'subscriber_email'              => [
            "type"   => "anomaly.field_type.email",
            "config" => [
                "default_value" => null,
            ]
        ],
        "subscriber_remote_id"          => "anomaly.field_type.text", 
        "subscriber_audience_id"        => "anomaly.field_type.text", 
        "subscriber_audience_name"      => "anomaly.field_type.text", 
        "subscriber_subscribed"         => [
            "type"   => "anomaly.field_type.boolean",
            "config" => [
                "default_value" => false,
                "on_color"      => "success",
                "off_color"     => "danger",
                "on_text"       => "YES",
                "off_text"      => "NO",
                "mode"          => "switch",
                "label"         => null,
            ]
        ], 
        "subscriber_status"             => "anomaly.field_type.text", 
        "subscriber_fname"              => "anomaly.field_type.text", 
        "subscriber_lname"              => "anomaly.field_type.text", 
 
        // @deprecated field
        "thrive_contact_synced"         => "anomaly.field_type.text",   

    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [
      
        'subscriber_email' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],    
        'subscriber_remote_id' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],             
        'subscriber_audience_id' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],         
        'subscriber_audience_name' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],   
        'subscriber_subscribed' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ], 
        'subscriber_fname' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'subscriber_lname' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],   
        'subscriber_status' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],   

        
        //
        // Old Sync Status Field
        // @deprecated
        //
        "thrive_contact_synced" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],  

        //
        // New Common Status Fields
        // These will replace old status
        // fields.
        //
        "status_remote_timestamp"   => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],  
        "status_local_timestamp"    => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],   
        "status_sync"               => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],    
        "status_created_locally"    => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],    
        "status_created_source"     => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],          
    ];

}