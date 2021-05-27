<?php namespace Thrive\MailchimpModule\Content\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Support\Integration\Content;

class ContentFormBuilder extends FormBuilder
{

    /**
     * The form fields.
     *
     * @var array|string
     */
    protected $fields = [
        '*'
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
    protected $sections = [];


    //protected $handler = \Thrive\MailchimpModule\Content\Form\ContentFormHandler::class;


    /**
     * The form assets.
     *
     * @var array
     */
    protected $assets = [];


    protected $can_post_to_mailchimp;


}
