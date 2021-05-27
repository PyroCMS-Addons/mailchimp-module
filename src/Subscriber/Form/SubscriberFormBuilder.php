<?php namespace Thrive\MailchimpModule\Subscriber\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\Form;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormHandler;
use Thrive\MailchimpModule\Support\Integration\Subscribers;

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
        'thrive_contact_synced'
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
        $entry = $this->getFormEntry();

        Log::debug('--- [ Begin ] ---  SubscriberFormBuilder::onSaved ');

        Log::debug('  » 00 Pushing User        : ' . $entry->email);


        if($this->can_post_to_mailchimp)
        {
            if($item = Subscriber::PostSubscriberToMailchimp($entry))
            {
                $messages->info('Successfully POSTED to Mailchimp');
                $entry->update(['thrive_contact_synced' => 'thrive.module.mailchimp::common.sync_success']);
            }
            else
            {
                $messages->error('Failed to POST to Mailchimp');
                $entry->update(['thrive_contact_synced' => 'thrive.module.mailchimp::common.sync_failed']);
            }
        }

    }
}
