<?php namespace Thrive\MailchimpModule\Subscriber\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;

class SubscriberTableBuilder extends TableBuilder
{

    /**
     * The table views.
     *
     * @var array|string
     */
    protected $views = [];

    /**
     * The table filters.
     *
     * @var array|string
     */
    protected $filters = [
        'subscriber_email'
    ];

    /**
     * The table columns.
     *
     * @var array|string
     */
    protected $columns = [
        'subscriber_email',
        'subscriber_fname',    
        'subscriber_lname',    
        'entry.subscriber_subscribed.toggle' => [
            'is_safe' => true,
        ], 
    ];

    /**
     * The table buttons.
     *
     * @var array|string
     */
    protected $buttons = [
        'edit',
    ];

    /**
     * The table actions.
     *
     * @var array|string
     */
    protected $actions = [
        'delete'
    ];

    /**
     * The table options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The table assets.
     *
     * @var array
     */
    protected $assets = [];

}
