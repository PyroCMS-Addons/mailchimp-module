<?php namespace Thrive\MailchimpModule\Campaign\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Campaign\Table\CampaignTableButtons;
use Thrive\PortfolioModule\Gallery\Table\CampaignTableColumns;

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
    protected $columns = \Thrive\MailchimpModule\Campaign\Table\CampaignTableColumns::class;
    

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
