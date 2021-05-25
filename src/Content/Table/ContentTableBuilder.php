<?php namespace Thrive\MailchimpModule\Content\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Content\Table\ContentTableButtons;

/**
 * ContentTableBuilder
 */
class ContentTableBuilder extends TableBuilder
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
        'automation_title',
        'automation_workflow_id',
        'automation_status',
        'automation_start_time',
        'automation_list_id',
    ];

    /**
     * The table buttons.
     *
     * @var array|string
     */
    protected $buttons = ContentTableButtons::class;


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
