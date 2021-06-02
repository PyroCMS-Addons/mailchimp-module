<?php namespace Thrive\MailchimpModule\Automation\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Support\Integration\Automation;

class AutomationFormBuilder extends FormBuilder
{

    /**
     * The form fields.
     *
     * @var array|string
     */
    protected $fields = [
        '*',
        'automation_workflow_id' => [
            'disabled' => 'edit',
        ],
        'automation_start_time' => [
            'disabled' => 'edit',
        ],
        'automation_create_time' => [
            'disabled' => 'edit',
        ],
        'automation_emails_sent' => [
            'disabled' => 'edit',
        ],
        'automation_list_id' => [
            'disabled' => 'edit',
        ],
        'automation_status' => [
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
                    'title'  => 'Automation',
                    'fields' => [
                        'automation_title',
                        'automation_workflow_id',
                        'automation_list_id',
                        'automation_list_name',
                        'automation_status',
                    ],
                ],
                'details2' => [
                    'title'  => 'Reply To',
                    'fields' => [
                        'automation_from_name',
                        'automation_reply_to',
                        'automation_emails_sent',
                    ],
                ],
                'details' => [
                    'title'  => 'From',
                    'fields' => [
                        'automation_start_time',
                        'automation_create_time',
                    ],
                ],
                'locked_fields' => [
                    'title'  => 'Locked Details',
                    'fields' => [
                        '*'
 
                    ],
                ],

            ],
        ],
    ];


    //protected $handler = \Thrive\MailchimpModule\Automation\Form\AutomationFormHandler::class;



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
     * onSaving
     *
     * @param  mixed $messages
     * @return void
     */
    public function onSaving(MessageBag $messages)
    {
        Log::debug('--- [ Begin ] ---  AutomationFormBuilder::onSaving ');

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

        Log::debug('--- [ Begin ] ---  AutomationFormBuilder::onSaved ');

        Log::debug('  » 00 Pushing Automation    : ' . $entry->automation_title . ' || '. $entry->automation_workflow_id );


        if($this->can_post_to_mailchimp)
        {
             if($item = Automation::Post($entry))
            {
                $messages->info('Successfully POSTED to Mailchimp');
            }
            else
            {
                $messages->error('Failed to POST to Mailchimp');
            }
        }

    }

}
