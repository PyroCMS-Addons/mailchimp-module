<?php namespace Thrive\MailchimpModule\Webhook\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\Form;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Support\Integration\Webhook;

/**
 * Class SubscriberFormBuilder
 *
 * @author Sam McDonald. <s.mcdonald@outlook.com.au>
 */
class WebhookFormBuilder extends FormBuilder
{

    /**
     * fields
     *
     * @var undefined
     */
    protected $fields = [
        '*',
        'webhook_id' => [
            'disabled' => 'edit',
        ],
        'webhook_id' => [
            'disabled' => 'create',
        ],
        'webhook_list_id' => [
            'disabled' => 'edit',
        ],
        'webhook_sources_api' => [
            'disabled' => 'edit',
        ],
        'webhook_url' => [
            'disabled' => 'edit',
        ],
        'webhook_url' => [
            'disabled' => 'create',
        ],
        'webhook_sources_api' => [
            'disabled' => 'create',
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
    protected $skips = [];

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
    protected $buttons = [];

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
                    'title'  => 'Webhook',
                    'fields' => [
                        'webhook_name',
                        'webhook_id',
                        'webhook_list_id',
                        'webhook_url',
                    ],
                ],
                'events' => [
                    'title'  => 'Events',
                    'fields' => [
                        'webhook_events_subscribe',
                        'webhook_events_unsubscribe',
                        'webhook_events_profile',
                        'webhook_events_upemail',
                        'webhook_events_cleaned',
                        'webhook_events_campaign',
                    ],
                ],
                'sources' => [
                    'title'  => 'Sources',
                    'fields' => [
                        'webhook_sources_api',
                        'webhook_sources_admin',
                        'webhook_sources_user',
                    ],
                ],
            ],
        ],
    ];


    /**
     * The form assets.
     *
     * @var array
     */
    protected $assets = [];


    // protected $handler = \Thrive\MailchimpModule\Subscriber\Form\SubscriberFormHandler::class;



    /**
     * onSaving
     *
     * @param  mixed $messages
     * @return void
     */
    public function onSaving(MessageBag $messages)
    {


    }


    /**
     * onSaved
     *
     * @param  mixed $messages
     * @return void
     */
    public function onSaved(MessageBag $messages)
    {
        $webhook = $this->getFormEntry();




        // Ensure to save the webhook with the right URL endpoint
        Webhook::SetCallbackUrl($webhook);

        Log::debug('WebHook Remote ID: ' . $webhook->webhook_id);

        //do we have a remote id, if not then its a create and post
        if($webhook->webhook_id == '')
        {
            //Log::debug(' --- attempt to Create: ');

            if(Webhook::PostCreate($webhook))
            {
                //Log::debug(' --- Create Success');

                $messages->success('Created Webhook');

            }
        }
        else
        {
            // Now sync with remote
            if(Webhook::Sync($webhook, 'Push' ))
            {

            }
            else
            {
                $messages->error('Failed to Post to Mailchimp');
            }
        }



        // Log::debug('onSaved for Webhook');
    }
}
