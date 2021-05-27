<?php namespace Thrive\MailchimpModule\Content\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Content\ContentModel;
use Thrive\MailchimpModule\Support\Integration\Content;

class PreviewContentFormBuilder extends FormBuilder
{

    protected $model = ContentModel::class;


    /**
     * The form fields.
     *
     * @var array|string
     */
    protected $fields = [
        '*',
        'content_fields' => 
        [
            'disabled' => 'edit'
        ],
        'content_campaign_id' => 
        [
            'disabled' => 'edit'
        ]        
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
    protected $sections = [
        'metafield'   => [
            'stacked' => false,
            'tabs' => [                
                'html' => [
                    'title'  => 'Email Preview',
                    'view' => [
                        'module::admin/tabs/campaign-actions',
                    ],
                    'fields' => [
                        '*'
                    ]
                ],                                                                                    
            ],
        ],        
    ];

}
