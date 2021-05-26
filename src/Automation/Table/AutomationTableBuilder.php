<?php namespace Thrive\MailchimpModule\Automation\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Automation\Table\AutomationTableButtons;

/**
 * AutomationTableBuilder
 */
class AutomationTableBuilder extends TableBuilder
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
    protected $columns = \Thrive\MailchimpModule\Automation\Table\AutomationTableColumns::class;
    // [
    //     'automation_title',
    //     'automation_workflow_id',
    //     'automation_status',
    //     'automation_start_time',
    //     'automation_list_id',
    // ];

    /**
     * The table buttons.
     *
     * @var array|string
     */
    protected $buttons = AutomationTableButtons::class;


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
