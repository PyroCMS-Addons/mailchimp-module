<?php

return [

    //
    // @deprecated status fields
    // 
    'campaign_sync_status' => [
        'name' => 'Sync Status',
        'instructions' => '',
        'placeholder' => '',
    ],



    //
    // Status Fields
    // 
    "status_remote_timestamp" => [
        'name'          => 'Remote Timestamp',
        'instructions'  => '',
    ],   
    "local_timestamp_sync" => [
        'name'          => 'Last Sync Time',
        'instructions'  => '[Last Sync Time] must be in Sync with [Last Save Time]. If not, the Sync action will perform nessessary action',
    ],   
    "local_timestamp_save" => [
        'name'          => 'Last Save Time',
        'instructions'  => '[Last Sync Time] must be in Sync with [Last Save Time]. If not, the Sync action will perform nessessary action',
    ],   

    "status_sync" => [
        'name'          => 'Sync Status',
        'instructions'  => '',
    ],    
    "status_created_locally" => [
        'name'          => 'Created Locally',
        'instructions'  => '',
    ],    
    "status_created_source" => [
        'name'          => 'Created Source',
        'instructions'  => '',
    ],   
    "status_sync_messages" => [
        'name'          => 'Messages',
        'instructions'  => '',
    ],   
    
    
    //
    // Subscriber Fields
    //
    'subscriber_email' => [
        'name' => 'Email',
        'instructions' => 'Email Address',
    ],
    'subscriber_remote_id' => [
        'name'             => 'Subscriber Hash',
        'instructions'     => '',
    ],
    'subscriber_audience_id' => [
        'name'              => 'Audience ID',
        'instructions'      => '',
        'placeholder'       => '',
    ],
    'subscriber_audience_name' => [
        'name'              => 'Audience',
        'instructions'      => '',
        'placeholder'       => '',
    ],
    'subscriber_subscribed' => [
        'name'              => 'Subscribed',
        'instructions'      => 'Is User Subscribed',
    ],   
    'subscriber_status' => [
        'name'              => 'Status',
        'instructions'      => 'Membership Status',
    ],      
    'subscriber_fname'      => [
        'name'              => 'First Name',
        'instructions'      => '',
        'placeholder'       => '',
    ],
    'subscriber_lname' => [
        'name'              => 'Last Name',
        'instructions'      => '',
        'placeholder'       => '',
    ],


    //
    // Audience Fields
    //
    'audience_name' => [
        'name' => 'Name',
        'instructions' => 'Give a Valid Audience Name',
        'placeholder' => 'My Audience',
    ],
    'audience_remote_id' => [
        'name' => 'List-ID',
        'instructions' => 'ID of the Audience',
        'placeholder' => '',
    ],  
    'audience_permission_reminder' => [
        'name' => 'Permission Reminder',
        'instructions' => 'The text reminder explaining to the user why they have been subscribed',
        'placeholder' => 'You have been added to this list when you subscribed on our website.',
    ],    
    'audience_email_type_option' => [
        'name' => 'Email Type Option',
        'instructions' => 'Enable this if you prefer different email types per users. We do not recomend this unless you are an advanced user.',
    ],    
    'audience_contact_company_name' => [
        'name' => 'Company Name',
        'instructions' => 'Enter your company Name',
        'placeholder' => 'Impressive Co.',
    ],      
    'audience_contact_address1' => [
        'name' => 'Company Address',
        'instructions' => 'Enter your company Address',
        'placeholder' => '123 Fake St',
    ],      
    'audience_contact_city' => [
        'name' => 'Company City',
        'instructions' => 'Enter your company HQ City Location',
        'placeholder' => 'Brisbane',
    ],     
    'audience_contact_state' => [
        'name' => 'Company State',
        'instructions' => 'Enter your company HQ State Location',
        'placeholder' => 'QLD',
    ],
    'audience_contact_country' => [
        'name' => 'Country',
        'instructions' => 'Enter your company HQ Country Location',
        'placeholder' => 'AU',
    ],    
    'audience_contact_zip' => [
        'name' => 'Company Zip',
        'instructions' => 'Enter your company HQ ZIP Location',
        'placeholder' => '4000',
    ],   
    'audience_campaign_from_name' => [
        'name' => 'Campaign From Name',
        'instructions' => 'Who is this Sent From (name)',
        'placeholder' => 'Jo Jackson',
    ],    
    'audience_campaign_from_email' => [
        'name' => 'Campaign From Email',
        'instructions' => 'Who is this Sent From (email)',
        'placeholder' => 'JoJackson@email.com',
    ],            
    'audience_campaign_subject' => [
        'name' => 'Campaign Subject',
        'instructions' => 'Set a Subject for the campaign',
        'placeholder' => 'The Best Campaign ever',
    ],        
    'audience_campaign_language' => [
        'name' => 'Campaign language',
        'instructions' => 'Set a Language',
        'placeholder' => 'EN_US',
    ],  


    //
    // Campaigns
    //
    'campaign_from_name' => [
        'name' => 'Campaign From Name',
        'instructions' => 'Who is this Sent From (name)',
        'placeholder' => 'Jo Jackson',
    ], 
    'campaign_subject' => [
        'name' => 'Campaign Subject',
        'instructions' => 'Set a Subject for the campaign',
        'placeholder' => 'The Best Campaign ever',
    ],       
    'campaign_name' => [
        'name' => 'Name/Title',
        'instructions' => '',
        'placeholder' => '',
    ],
    'campaign_remote_id' => [
        'name' => 'Remote ID',
        'instructions' => '',
        'placeholder' => '',
    ],
    'campaign_list_id' => [
        'name' => 'List ID',
        'instructions' => '',
        'placeholder' => '',
    ],
    'campaign_type' => [
        'name' => 'Type',
        'instructions' => '',
        'placeholder' => '',
    ],
    'campaign_reply_to' => [
        'name' => 'Reply to (Email)',
        'instructions' => '',
        'placeholder' => '',
    ],
    'campaign_subject_line' => [
        'name'          => 'Subject',
        'instructions'  => '',
        'placeholder'   => '',
    ],
    'campaign_status' => [
        'name'          => 'Status',
        'instructions'  => '',
        'placeholder'   => '',
    ],



    //
    // Automations
    //
    'automation_title' => [
        'name' => 'Title',
        'instructions' => '',
        'placeholder' => '',
    ],   
    'automation_workflow_id' => [
        'name' => 'Workflow Id',
        'instructions' => '',
        'placeholder' => '',
    ],   
    'automation_status' => [
        'name' => 'Status',
        'instructions' => '',
        'placeholder' => '',
    ],   
    'automation_start_time' => [
        'name' => 'Start Time',
        'instructions' => '',
        'placeholder' => '',
    ],   
    'automation_create_time' => [
        'name' => 'Create Time',
        'instructions' => '',
        'placeholder' => '',
    ],   
    'automation_emails_sent' => [
        'name' => 'Emails Sent',
        'instructions' => '',
        'placeholder' => '',
    ],   
    'automation_list_id' => [
        'name' => 'Audience ID',
        'instructions' => '',
        'placeholder' => '',
    ],    
    'automation_reply_to' => [
        'name' => 'Reply To',
        'instructions' => '',
        'placeholder' => '',
    ],    
    'automation_list_name' => [
        'name' => 'Audience',
        'instructions' => '',
        'placeholder' => '',
    ],    

    //
    // Webhooks
    //
    'webhook_name' => [
        'name' => 'Name',
        'instructions' => '',
        'placeholder' => '',
    ],  
    'webhook_id' => [
        'name' => 'Remote ID',
        'instructions' => '',
        'placeholder' => '',
    ], 
    'webhook_list_id' => [
        'name' => 'List/Audience',
        'instructions' => '',
        'placeholder' => '',
    ], 
    
    'webhook_url' => [
        'name' => 'URL Endpoint',
        'instructions' => '',
        'placeholder' => '',
    ], 
    'webhook_events_subscribe' => [
        'name' => 'Subscribe',
        'instructions' => 'Enable for Subscribe',
        'placeholder' => '',
    ], 
    'webhook_events_unsubscribe' => [
        'name' => 'UnSubscribe',
        'instructions' => 'Enable for UnSubscribe',
        'placeholder' => '',
    ], 
    'webhook_events_profile' => [
        'name' => 'Profile',
        'instructions' => 'Enable for Profile Changes',
        'placeholder' => '',
    ], 
    'webhook_events_upemail' => [
        'name' => 'Update Email',
        'instructions' => 'Enable for Email Changes',
        'placeholder' => '',
    ], 
    'webhook_events_cleaned' => [
        'name' => 'Cleaned',
        'instructions' => 'Enable for Cleaned',
        'placeholder' => '',
    ], 
    'webhook_events_campaign' => [
        'name' => 'Campaign',
        'instructions' => 'Enable for Campaign Canges',
        'placeholder' => '',
    ], 
    'webhook_sources_api' => [
        'name' => 'Via API',
        'instructions' => 'Trigger Via API',
        'placeholder' => '',
    ], 
    'webhook_sources_admin' => [
        'name' => 'Admin',
        'instructions' => 'Trigger By a Admin',
        'placeholder' => '',
    ], 
    'webhook_sources_user' => [
        'name' => 'User',
        'instructions' => 'Trigger By a Subscriber',
        'placeholder' => '',
    ],     
    'webhook_enabled' => [
        'name' => 'Enabled',
        'instructions' => 'Is the Webhook Enabled',
        'placeholder' => '',
    ],      
];
