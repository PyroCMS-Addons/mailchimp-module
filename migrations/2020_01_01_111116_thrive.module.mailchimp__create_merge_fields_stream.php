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
        "content_name" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'content_plain_text' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
    ];

}