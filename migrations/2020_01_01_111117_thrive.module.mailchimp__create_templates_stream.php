<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateTemplatesStream extends Migration
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
        'slug'          => 'templates',
        'title_column'  => 'template_name',
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
        "template_name" => [
            "type"   => "anomaly.field_type.text",
        ],
        "template_id" => [
            "type"   => "anomaly.field_type.text",
        ],
        "template_type" => [
            "type"   => "anomaly.field_type.text",
        ],
        "template_drag_and_drop" => [
            "type"   => "anomaly.field_type.boolean",
        ],
        "template_responsive" => [
            "type"   => "anomaly.field_type.boolean",
        ],
        "template_active" => [
            "type"   => "anomaly.field_type.boolean",
        ],
        "template_thumbnail" => [
            "type"   => "anomaly.field_type.url",
        ],
        "template_category" => [
            "type"   => "anomaly.field_type.text",
        ],
        'template_html' => [
            "type"   => "anomaly.field_type.editor",
            "config" => [
                "default_value" => '<html>Default Email Template</html>',
                "mode"          => "html",
                "height"        => 500,
                "word_wrap"     => null,
            ]
        ],
        'template_plain_text' => [
            "type"   => "anomaly.field_type.editor",
            "config" => [
                "default_value" => 'Default Email Template',
                "mode"          => "twig",
                "height"        => 500,
                "word_wrap"     => null,
            ]
        ],
    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [
        "template_name" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'template_id' => [
            'translatable'      => false,
            'unique'            => true,
            'required'          => true,
        ],
        'template_type' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'template_drag_and_drop' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'template_responsive' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'template_active' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'template_thumbnail' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'template_category' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'template_html' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'template_plain_text' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
    ];
}