<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;


class ThriveModuleMailchimpAddListNameFieldToAutomations extends Migration
{

    /**
     * Don't delete the stream here
     * it's only for reference use.
     *
     * @var bool
     */
    protected $delete = false;

    /**
     * The addon fields.
     *
     * @var array
     */
    protected $fields = [
        'automation_list_name' => [
            "type"   => "anomaly.field_type.text",
        ],
    ];

    /**
     * The addon stream.
     * This is only for
     * reference for below.
     *
     * @var array
     */
    protected $stream = [
        'slug' => 'automations',
    ];

    
    /**
     * The addon assignments.
     *
     * @var array
     */
    protected $assignments = [
        'automation_list_name'  => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],  
    ];
}
