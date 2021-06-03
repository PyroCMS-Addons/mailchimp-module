<?php namespace Thrive\MailchimpModule;

use Anomaly\Streams\Platform\Addon\Module\Module;

/**
 * MailchimpModule
 *
 * This is the Main Module Class for MailchimpModule
 *
 * @package    	Thrive\MailchimpModule
 * @author 		Sam McDonald <s.mcdonald@outlook.com.au>
 * @author 		Thrive
 * @copyright  	2000-2021 Thrive Developement
 * @license    	https://mit-license.org/
 * @license    	https://opensource.org/licenses/MIT
 * @version    	Release: 1.0.0
 * @link       	https://github.com/PyroCMS-Addons/mailchimp-module
 * @since      	Class available since Release 1.0.0
 */
class MailchimpModule extends Module
{

    /**
     * Are we gonna show the Admin Nav ?
     *
     * @var bool
     */
    protected $navigation = true;

    /**
     * The addon icon.
     *
     * @var string
     */
    protected $icon = 'fa fa-cloud';

    /**
     * The module sections.
     *
     * @var array
     */
    protected $sections = [
        'dashboard' => [
            'href' => 'admin/mailchimp',
        ],
        'audiences' => [
            'href' => 'admin/mailchimp/audiences',
            'buttons' => [
                'sync_audience' => [
                    'href' => 'admin/mailchimp/audiences/sync',
                    'type' => 'primary',
                    'icon' => 'refresh',
                    'attributes' => [
                        'data-icon'     => 'warning',
                        // 'data-icon'     => 'success',
                        'data-toggle'   => 'confirm',
                        'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                        'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_sync_audience_list'
                    ]
                ]
            ],
        ],
        'automations' => [
            'href' => 'admin/mailchimp/automations',
            'buttons' => [
                'sync_automation_pull' => [
                    'href' => 'admin/mailchimp/automations/sync',
                    'type' => 'primary',
                    'icon' => 'refresh',
                    'attributes' => [
                        'data-icon'     => 'warning',
                        'data-toggle'   => 'confirm',
                        'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                        'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_sync_automations'
                    ]
                ],
            ],
        ],
        'campaigns' => [
            'href' => 'admin/mailchimp/campaigns',
            'buttons' => [
                'sync_campaign_pull' => [
                    'href' => 'admin/mailchimp/campaigns/sync',
                    'type' => 'primary',
                    'icon' => 'refresh',
                    'attributes' => [
                        'data-icon'     => 'warning',
                        'data-toggle'   => 'confirm',
                        'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                        'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_sync_campaigns'
                    ]
                ],
            ],
        ],
        'subscribers' => [
            'href' => 'admin/mailchimp/subscribers',
            'buttons' => [
                'new_subscriber' => [
                    'type' => 'success',
                    'icon' => 'fa fa-user',
                    'attributes' => [
                        'data-icon'     => 'warning',
                        'data-toggle'   => 'confirm',
                        'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                        'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_add_subscribers'
                    ]                    
                ],
                'sync_all_contacts' => [
                    'href' => 'admin/mailchimp/subscribers/sync',
                    'type' => 'primary',
                    'icon' => 'refresh',
                    'attributes' => [
                        'data-icon'     => 'warning',
                        'data-toggle'   => 'confirm',
                        'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                        'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_sync_subscribers'
                    ]
                ],
            ],
        ],
        'webhooks' => [
            'href' => 'admin/mailchimp/webhooks',
            'buttons' => [
                'sync_webhooks' => [
                    'href' => 'admin/mailchimp/webhooks/sync',
                    'type' => 'primary',
                    'icon' => 'refresh',
                    'attributes' => [
                        'data-icon'     => 'warning',
                        'data-toggle'   => 'confirm',
                        'data-title'    => 'thrive.module.mailchimp::common.are_you_sure',
                        'data-message'  => 'thrive.module.mailchimp::common.are_you_sure_sync_webhooks'
                    ]
                ],
            ],
        ],        
        'settings' => [
            'href' => 'admin/mailchimp/settings',
        ]
    ];
}