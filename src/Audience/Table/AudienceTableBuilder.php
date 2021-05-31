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
        // 'audience_name' => [
        //     'wrapper' => '<a href="{{ url("admin/mailchimp/audiences/edit/{entry.id}") }}"
        //                     data-toggle="modal"
        //                     data-target="#modal">{entry.audience_name}</a>',
        // ],
        'audience_name',      
        'audience_remote_id' => [
            'wrapper' => '<span class="tag tag-default">{entry.audience_remote_id}</span>',
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
