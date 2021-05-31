<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateMergeFieldsStream extends Migration
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
        'slug'          => 'merge_fields',
        'title_column'  => 'merge_field_name',
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
        "merge_field_name" => [
            "type"   => "anomaly.field_type.text",
        ],
        'merge_field_list_id' => [
            "type"   => "anomaly.field_type.text",
        ],
    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [
        "merge_field_name" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'merge_field_list_id' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],


        //
        // New Common Status Fields
        // These will replace old status
        // fields.
        //
        "status_remote_timestamp" => [
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
        "status_sync" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        "status_created_locally" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        "status_created_source" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
    ];
}