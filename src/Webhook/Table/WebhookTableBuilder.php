<?php namespace Thrive\MailchimpModule\Webhook\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;

class WebhookTableBuilder extends TableBuilder
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
    protected $filters = [ ];

    /**
     * The table columns.
     *
     * @var array|string
     */
    protected $columns = [
        'webhook_name',
        'webhook_id', 
        'webhook_list_id',   
        'webhook_url',
        // 'entry.webhook_events_subscribe.toggle' => [
        //     'is_safe' => true,
        // ], 
    ];

    /**
     * The table buttons.
     *
     * @var array|string
     */
    protected $buttons = [
        'delete_remote' => [
            'type' => 'danger',
            'icon' => 'trash',
            'href'        => 'admin/mailchimp/webhooks/delete_force/{entry.id}', 
            'attributes' => [

                'data-toggle'  => 'confirm',
                'data-icon' => 'warning',
                'data-title' => 'Are you sure ?',
                'data-message' => 'This will delete the Item from the Remote Source too!'
            ],
        ],
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
