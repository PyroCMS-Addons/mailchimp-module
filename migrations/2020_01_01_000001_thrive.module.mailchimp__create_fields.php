<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateFields extends Migration
{

    /**
     * A WIP of fields tro use across streams
     * 
     * The addon fields.
     *
     * @var array
     */
    protected $fields = [

        // Timestamps
        'status_remote_timestamp'       => 'anomaly.field_type.text',     // date of remote upate timestamp
        'status_local_timestamp'        => 'anomaly.field_type.text',     // date of local upate timestamp

        // Flags
        'status_sync'                   => 'anomaly.field_type.text',     // (push|pull|delete|NULL) | NULL is default and allgood, if flagged for push or pull then during maintenance these will occur
        'status_created_locally'        => 'anomaly.field_type.boolean',  // was object created locally or remotely
        'status_created_source'         => 'anomaly.field_type.text',     // (api|webhook|module|external|mailchimp)
        
    ];

    // part of the Commands/Tasks. we could check 1once per day and update {local.status_sync_last_checked}. To minimize in app checks

}