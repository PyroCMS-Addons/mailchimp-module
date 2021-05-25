<?php namespace Thrive\MailchimpModule\Campaign\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Campaign\Table\CampaignTableButtons;

class CampaignTableBuilder extends TableBuilder
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
    protected $filters = [];

    /**
     * The table columns.
     *
     * @var array|string
     */
    protected $columns = [
        'campaign_name' => [
            //'heading' => 'MC ID',
            'wrapper' => '<a href="{{ url("admin/mailchimp/audiences/edit/{entry.id}") }}"
                            data-toggle="modal"
                            data-target="#modal">{entry.campaign_name}</a>',
        ],
        'campaign_type',
        'list_id',
        'campaign_str_id',
        'campaign_sync_status',
        'status',
    ];

    /**
     * The table buttons.
     *
     * @var array|string
     */
    protected $buttons = CampaignTableButtons::class; 


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
