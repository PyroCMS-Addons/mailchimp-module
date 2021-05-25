<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

// Anomaly
use Anomaly\Streams\Platform\Http\Controller\AdminController;

// Thrive
use Thrive\MailchimpModule\Campaign\CampaignModel;
use Thrive\MailchimpModule\Support\Integration\Content;

/**
 * ContentController
 *
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
class ContentController extends AdminController
{

    public function view( $id )
    {
        $campaign = CampaignModel::find($id);

        $html = Content::Get($campaign->campaign_str_id);

        // dd($html->archive_html);
        // dd($html->html);
        // dd($html->plain_text);

        return view('thrive.module.mailchimp::admin.content.view')->withContent($html->html);
    }
}
