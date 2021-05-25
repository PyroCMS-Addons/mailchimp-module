<?php namespace Thrive\MailchimpModule\Audience\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Audience\Table\AudienceTableButtons;

/**
 * AudienceTableBuilder
 */
class AudienceTableBuilder extends TableBuilder
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
        // 'name',
        'name' => [
            'wrapper' => '<a href="{{ url("admin/mailchimp/audiences/edit/{entry.id}") }}"
                            data-toggle="modal"
                            data-target="#modal">{entry.name}</a>',
        ],
        'thrive_sync_status',
        'str_id' => [
            'wrapper' => '<a href="{{ url("admin/mailchimp/audiences/edit/{entry.id}") }}"
                            data-toggle="modal"
                            data-target="#modal">{entry.str_id}</a>',
        ]
    ];

    /**
     * The table buttons.
     *
     * @var array|string
     */
    protected $buttons = AudienceTableButtons::class;

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
