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
        'title_column' => 'email',
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
        "thrive_contact_synced" => "anomaly.field_type.text",   
        "audience"              => "anomaly.field_type.text", 
        "audience_name"         => "anomaly.field_type.text", 
        "fname"                 => "anomaly.field_type.text", 
        "lname"                 => "anomaly.field_type.text", 
        "subscribed"            => [
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
        'email'                 => [
            "type"   => "anomaly.field_type.email",
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
        "thrive_contact_synced" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],        
        'subscribed' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'email' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],        
        'audience' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],         
        'audience_name' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],   
        'fname' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'lname' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],                                       
    ];

}