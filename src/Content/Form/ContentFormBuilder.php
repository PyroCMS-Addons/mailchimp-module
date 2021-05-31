<?php namespace Thrive\MailchimpModule\Content\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Support\Integration\Content;

class ContentFormBuilder extends FormBuilder
{

    // protected $model = UserModel::class;
    /**
     * The form fields.
     *
     * @var array|string
     */
    protected $fields = [
        '*',
        'content_fields' => 
        [
            'disabled' => 'edit'
        ],
        'content_campaign_id' => 
        [
            'disabled' => 'edit'
        ]        
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
                'details' => [
                    'title'  => 'Details',
                    'fields' => [
                        'content_name',
                        'content_campaign_id',
                    ],
                ],                  
                'html' => [
                    'title'  => 'HTML',
                    'fields' => [
                        'content_html',
                    ],
                ],                     
                'plain_text' => [
                    'title'  => 'Plain Text',
                    'fields' => [
                        'content_plain_text',
                    ],
                ],                                                                 
            ],
        ],        
    ];

    //protected $handler = \Thrive\MailchimpModule\Content\Form\ContentFormHandler::class;


    /**
     * The form assets.
     *
     * @var array
     */
    protected $assets = [];


    protected $can_post_to_mailchimp;


    public function onSaving(MessageBag $messages)
    {
        Log::debug('--- [ Begin ] ---  ContentFormBuilder::onSaving ');

        $this->can_post_to_mailchimp = true;

    }

    public function onSaved(MessageBag $messages)
    {
        $entry = $this->getFormEntry();

        Log::debug('--- [ Begin ] ---  ContentFormBuilder::onSaved ');

        Log::debug('  » 00 Pushing Content     : ' . $entry->content_name . ' || '. $entry->content_campaign_id );


        if($this->can_post_to_mailchimp)
        {
            // Change this to Campaign::Post(xx);
             if($item = Content::Post($entry))
            {
                $messages->info('Successfully POSTED to Mailchimp');
                // $entry->update(['campaign_sync_status' => 'Updated']);
            }
            else 
            {
                $messages->error('Failed to POST to Mailchimp');
                // $entry->update(['campaign_sync_status' => 'Post Failed']);
            }
        }

    }    
}
