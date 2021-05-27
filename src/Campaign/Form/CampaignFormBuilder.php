<?php namespace Thrive\MailchimpModule\Campaign\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Support\Integration\Campaign;

class CampaignFormBuilder extends FormBuilder
{

    /**
     * The form fields.
     *
     * @var array|string
     */
    protected $fields = [
        '*',
        'campaign_str_id' => [
            'disabled' => 'edit',
        ],
        'campaign_sync_status' => [
            'disabled' => 'edit',
        ],
        'campaign_type' => [
            'disabled' => 'edit',
        ],
        'list_id' => [
            'disabled' => 'edit',
        ],    
    ];

    /**
     * Additional validation rules.
     *
     * @var array|string
     */
    protected $rules = [];

    /**
     * Fields to skip.
     *
     * @var array|string
     */
    protected $skips = [
    ];

    /**
     * The form actions.
     *
     * @var array|string
     */
    protected $actions = [];

    /**
     * The form buttons.
     *
     * @var array|string
     */
    protected $buttons = [
        'cancel',
    ];

    /**
     * The form options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The form sections.
     *
     * @var array
     */
    protected $sections = [
        'metafield'   => [
            'stacked' => false,
            'tabs' => [
                'general' => [
                    'title'  => 'Campaign',
                    'fields' => [
                        'campaign_name',
                        'campaign_subject_line',
                    ],
                ],   
                'details2' => [
                    'title'  => 'Reply To',
                    'fields' => [
                        'campaign_reply_to',
                    ],
                ],   
                'details' => [
                    'title'  => 'From',
                    'fields' => [
                        'campaign_from_name',
                    ],
                ],                  
                'locked_fields' => [
                    'title'  => 'Locked Details',                  
                    'fields' => [
                        'campaign_str_id',
                        'campaign_sync_status',
                        'thrive_sync_status',
                        'campaign_type',
                        'list_id',
                    ],
                ],                 
                'viewtab' => [
                    'title'  => 'Actions',
                    'view' => [
                        'module::admin/tabs/campaign-actions',
                    ],
                    'fields' => [
                        'list_id',
                    ],                    
                ],                                                                               
            ],
        ],        
    ];


    //protected $handler = \Thrive\MailchimpModule\Campaign\Form\CampaignFormHandler::class;

    /**
     * The form assets.
     *
     * @var array
     */
    protected $assets = [];

    
    /**
     * can_post_to_mailchimp
     *
     * @var mixed
     */
    protected $can_post_to_mailchimp;

   
    /**
     * Push to Mailchimp
     */
    public function onSaving(MessageBag $messages)
    {
        Log::debug('--- [ Begin ] ---  CampaignFormBuilder::onSaving ');

        $this->can_post_to_mailchimp = true;

    }

    
    /**
     * onSaved
     *
     * @param  mixed $messages
     * @return void
     */
    public function onSaved(MessageBag $messages)
    {
        $entry = $this->getFormEntry();

        Log::debug('--- [ Begin ] ---  CampaignFormBuilder::onSaved ');

        Log::debug('  » 00 Pushing Campaign    : ' . $entry->campaign_name . ' || '. $entry->campaign_str_id );


        if($this->can_post_to_mailchimp)
        {
            // Change this to Campaign::Post(xx);
             if($item = Campaign::PostLocalEntryToMailchimp($entry))
            {
                $messages->info('Successfully POSTED to Mailchimp');
                $entry->update(['campaign_sync_status' => 'Updated']);
            }
            else 
            {
                $messages->error('Failed to POST to Mailchimp');
                $entry->update(['campaign_sync_status' => 'Post Failed']);
            }
        }

    }

}
