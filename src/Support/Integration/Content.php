<?php namespace Thrive\MailchimpModule\Support\Integration;

use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Automation\AutomationModel;
use Thrive\MailchimpModule\Automation\AutomationRepository;
use Thrive\MailchimpModule\Automation\Contract\AutomationInterface;
use Thrive\MailchimpModule\Campaign\Contract\CampaignInterface;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Support\Mailchimp;

/**
 * Content
 *
 * Business Logic Connecter to the api.
 *
 * The Business Logic classes handle errros,
 * messages, functionality and integrating
 * the system to the api.
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
class Content
{
        
    /**
     * Get
     * 
     *  return $html->archive_html;
     *  return $html->html;
     *  return $html->plain_text;
     *
     * @param  mixed $campaign_id
     * @return void
     */
    public static function Get($campaign_id)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            if($content = $mailchimp->getCampaignContent($campaign_id))
            {
                return $content;
            }
        }

        return false;
    }

}