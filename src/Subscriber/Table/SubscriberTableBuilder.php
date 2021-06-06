<?php namespace Thrive\MailchimpModule\Subscriber\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;

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
        'subscriber_email',
        'subscriber_fname',
        'subscriber_lname',
        'subscriber_subscribed',
    ];

    /**
     * The table columns.
     *
     * @var array|string
     */
    protected $columns = \Thrive\MailchimpModule\Subscriber\Table\SubscriberTableColumns::class;


    /**
     * The table buttons.
     *
     * @var array|string
     */
    // protected $buttons = [
    //     'edit',
    // ];
    
    protected $buttons = \Thrive\MailchimpModule\Subscriber\Table\SubscriberTableButtons::class;

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
