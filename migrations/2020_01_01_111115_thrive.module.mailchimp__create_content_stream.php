<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateContentStream extends Migration
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
        'slug' => 'content',
        'title_column' => 'content_name',
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
        "content_name" => [
            "type"   => "anomaly.field_type.text",
        ],
        'content_plain_text' => [
            "type"   => "anomaly.field_type.text",
        ],    
        'content_html' => [
            "type"   => "anomaly.field_type.editor",
            "config" => [
                "default_value" => null,
                "mode"          => "html",
                "height"        => 500,
                "word_wrap"     => null,
            ]            
        ],         
        'content_archive_html' => [
            "type"   => "anomaly.field_type.editor",
            "config" => [
                "default_value" => null,
                "mode"          => "html",
                "height"        => 500,
                "word_wrap"     => null,
            ]                        
        ],  
        'content_fields' => [
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
        'content_html' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'content_archive_html' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'content_fields' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
            
    ];

}