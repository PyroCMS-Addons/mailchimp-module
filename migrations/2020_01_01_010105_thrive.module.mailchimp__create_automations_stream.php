<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateAutomationsStream extends Migration
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
        'slug' => 'automations',
        'title_column' => 'automation_title',
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
        "automation_title" => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_workflow_id' => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_status' => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_start_time' => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_create_time' => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_emails_sent' => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_list_id' => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_list_name' => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_from_name' => [
            "type"   => "anomaly.field_type.text",
        ],
        'automation_reply_to' => [
            "type"   => "anomaly.field_type.email",
        ],
    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [
        "automation_title"          => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'automation_workflow_id'    => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'automation_start_time'     => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'automation_create_time'    => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'automation_emails_sent'    => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'automation_list_id'        => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'automation_list_name'        => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],        
        'automation_from_name'      => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'automation_reply_to'       => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'automation_status'         => [
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
        "local_timestamp_sync" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        "local_timestamp_save" => [
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