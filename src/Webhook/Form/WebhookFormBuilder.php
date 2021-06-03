<?php namespace Thrive\MailchimpModule\Webhook\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\Form;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;

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
    protected $fields = [];


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
    protected $sections = [];


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
        // Post to Mailchimp to Update them
    }
}
