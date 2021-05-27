<?php namespace Thrive\MailchimpModule\Support\Ui;

// Laravel
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Campaign\Contract\CampaignInterface;

// Thrive


/**
 * Chooser
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
 *
 */
class Chooser
{
    public static function CampaignActions(CampaignInterface $campaign)
    {
        $actions = [];

        $id = $campaign->id;

        if($campaign->canEdit())
        {
            $actions['edit'] = 
            [
                'name'          => 'Edit Camapign',
                'slug'          => 'edit',
                'description'   => 'Edit the Camapign',
                'url'           => 'admin/mailchimp/campaigns/edit/' . $id,
            ];

            $actions['send'] =
            [
                'slug'          => 'send',
                'name'          => 'Send Camapign',
                'description'   => 'Send the Camapign',
                'url'           => 'admin/mailchimp/campaigns/send/' . $id,
            ];

            $actions['send_test'] =
            [
                'slug'          => 'send_test',
                'name'          => 'Send a Test',
                'description'   => 'Send a test to the Camapign Test email address',
                'url'           => 'admin/mailchimp/campaigns/send_test/' . $id,
            ];

            $actions['template'] =
            [
                'slug'          => 'template',
                'name'          => 'Edit HTML Template',
                'description'   => 'Edit the HTML Template for this campaign',
                'url'           => 'admin/mailchimp/campaigns/template/' . $id,
            ];              
            
        }

        $actions['copy'] = 
        [
            'slug'          => 'copy',
            'name'          => 'Duplicate Camapign',
            'description'   => 'Duplicate/Copy the Camapign',
            'url'           => 'admin/mailchimp/campaigns/copy/' . $id,
        ];

        $actions['preview'] = 
        [
            'slug'          => 'preview',
            'name'          => 'Preview HTML Template',
            'description'   => 'Views the Email Newsletter Template.',
            'url'           => 'admin/mailchimp/campaigns/preview/' . $id,
        ];

        return $actions;

    }
}