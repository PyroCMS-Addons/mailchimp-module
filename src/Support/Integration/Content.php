<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Facades\Log;

// Thrive
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Content\ContentModel;
use Thrive\MailchimpModule\Campaign\Contract\CampaignInterface;
use Thrive\MailchimpModule\Automation\Contract\AutomationInterface;
use Thrive\MailchimpModule\Campaign\Contract\CampaignRepositoryInterface;

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

    
    /**
     * GetPreview
     * 
     * Check status of local repository, and then Check if we 
     * need to go online for an update if need to update 
     * from remote, then we download and update local.
     * Once local if updated we can return the 
     * content record.
     * 
     * Otherwise, if all good from local copy, deliver local
     * copy to user.
     *
     * @param  mixed $campaign
     * @return void
     */
    public static function GetPreview(CampaignInterface $campaign)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            // 1. Do we have it locally
            if($content = ContentModel::where('content_campaign_id', $campaign->campaign_str_id)->first())
            {
                return $content;
            }
            else
            {
                // 2. ok, well if not then lets download the content
                if($remote = $mailchimp->getCampaignContent( $campaign->campaign_str_id ))
                {
                    // Now store the content
                    $local = new ContentModel;
                    $local->content_name            = 'Template for ' . $campaign->campaign_name;
                    $local->content_campaign_id     = $campaign->campaign_str_id;
                    $local->content_plain_text      = (isset($remote->plain_text)) ? $remote->plain_text : '' ;
                    $local->content_html            = (isset($remote->html)) ? $remote->html : '' ; 
                    $local->content_archive_html    = (isset($remote->archive_html)) ? $remote->archive_html : '' ;
                    $local->content_fields          = '';
                    $local->save();

                    return $local;
                }
            }

        }

        return false;
    }    

}