<?php namespace Thrive\MailchimpModule\Subscriber\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\Form;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormHandler;
use Thrive\MailchimpModule\Support\Sync\SyncAction;
use Thrive\MailchimpModule\Support\Integration\Subscriber;

/**
 * Class SubscriberFormBuilder
 *
 * @author Sam McDonald. <s.mcdonald@outlook.com.au>
 */
class SubscriberFormBuilder extends FormBuilder
{

    /**
     * fields
     *
     * @var undefined
     */
    protected $fields = \Thrive\MailchimpModule\Subscriber\Form\SubscriberFormFields::class;


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
        'subscriber_status',
        // 'subscriber_remote_id',
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
        'sync' => [
            'type' => 'info',
            'attributes' => [
                'data-icon'     => 'warning',
                // 'data-icon'     => 'success',
                'data-toggle'   => 'confirm',
                'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_sync_subscribers'
            ]
        ],
        'pull' => [
            'type' => 'info',
            'attributes' => [
                'data-icon'     => 'warning',
                // 'data-icon'     => 'success',
                'data-toggle'   => 'confirm',
                'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_pull_subscribers'
            ]            
        ],      
        'push' => [
            'type' => 'danger',
            'attributes' => [
                'data-icon'     => 'warning',
                // 'data-icon'     => 'success',
                'data-toggle'   => 'confirm',
                'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_push_subscribers'
            ]            
        ],            
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
    protected $sections = \Thrive\MailchimpModule\Subscriber\Form\SubscriberFormSections::class;


    /**
     * The form assets.
     *
     * @var array
     */
    protected $assets = [];


    // protected $handler = 'Thrive\MailchimpModule\Subscriber\Form\SubscriberFormHandler@handle';
    protected $handler = \Thrive\MailchimpModule\Subscriber\Form\SubscriberFormHandler::class;


    /**
     * @bool    flag tyo see if we can post to
     *          mailchimp during the
     *          saving process.
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
        Log::debug('--- [ Begin ] ---  SubscriberFormBuilder::onSaving ');

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
        $subscriber = $this->getFormEntry();

        Log::debug('--- [ Begin ] ---  SubscriberFormBuilder::onSaved ');

        Log::debug('  » 00 Pushing User        : ' . $subscriber->subscriber_email);

        Subscriber::UpdateSubscriberTimestamp($subscriber);

        $this->can_post_to_mailchimp = true;

        if($this->can_post_to_mailchimp)
        {
            if($item = Subscriber::ExecuteSyncAction($subscriber, SyncAction::Push))
            {
                $messages->info('Successfully POSTED to Mailchimp');
            }
            else
            {
                $messages->error('Failed to POST to Mailchimp');
            }
        }
        else
        {
            //$messages->info(' !! Did not POST to Mailchimp');
        }
    }
}
