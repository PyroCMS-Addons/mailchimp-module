<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Facades\Log;

// Thrive
use Thrive\MailchimpModule\Automation\Contract\AutomationInterface;
use Thrive\MailchimpModule\Campaign\Contract\CampaignInterface;
use Thrive\MailchimpModule\Campaign\Contract\CampaignRepositoryInterface;
use Thrive\MailchimpModule\Content\ContentModel;
use Thrive\MailchimpModule\Content\Contract\ContentInterface;
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
     * Sync
     *
     * @param  mixed $campaign
     * @return void
     */
    public static function Sync(CampaignInterface $campaign)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            // 2. ok, well if not then lets download the content
            if($remote = $mailchimp->getCampaignContent( $campaign->campaign_remote_id ))
            {
                if($local = ContentModel::where('content_campaign_id', $campaign->campaign_remote_id)->first())
                {
                    // we have a local copy
                    return self::UpdateLocalFromremote($local, $remote);
                }
                else
                {
                    //we dont have local copy, lets create
                    return self::CreateLocalFromremote($remote, $campaign);
                }
            }
        }

        return false;
    }


    /**
     * Push
     *
     * @param  mixed $campaign
     * @return void
     */
    public static function Post(ContentInterface $content)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            $content_values = self::PrepareContent($content);

            if($remote = $mailchimp->setCampaignContent( $content->content_campaign_id , $content_values))
            {
                return true;
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
            if($content = ContentModel::where('content_campaign_id', $campaign->campaign_remote_id)->first())
            {
                return $content;
            }
            else
            {
                // 2. ok, well if not then lets download the content
                if($remote = $mailchimp->getCampaignContent( $campaign->campaign_remote_id ))
                {
                    // Now store the content
                    $local = new ContentModel;
                    $local->content_name            = 'Template for ' . $campaign->campaign_name;
                    $local->content_campaign_id     = $campaign->campaign_remote_id;
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


    /**
     * UpdateLocalFromremote
     *
     * @param  mixed $local
     * @param  mixed $remote
     * @return void
     */
    public static function UpdateLocalFromremote(ContentInterface $local , $remote)
    {
        // Now store the content
        $local = new ContentModel;
        $local->content_name            = 'Template for ' . $campaign->campaign_name;
        $local->content_campaign_id     = $campaign->campaign_remote_id;
        $local->content_plain_text      = (isset($remote->plain_text)) ? $remote->plain_text : '' ;
        $local->content_html            = (isset($remote->html)) ? $remote->html : '' ;
        $local->content_archive_html    = (isset($remote->archive_html)) ? $remote->archive_html : '' ;
        $local->content_fields          = '';
        $local->save();

        return true;
    }


    /**
     * CreateLocalFromremote
     *
     * @param  mixed $remote
     * @return void
     */
    public static function CreateLocalFromremote($remote, CampaignInterface $campaign)
    {
        // Now store the content
        $local = new ContentModel;
        $local->content_name            = 'Template for ' . $campaign->campaign_name;
        $local->content_campaign_id     = $campaign->campaign_remote_id;
        $local->content_plain_text      = (isset($remote->plain_text)) ? $remote->plain_text : '' ;
        $local->content_html            = (isset($remote->html)) ? $remote->html : '' ;
        $local->content_archive_html    = (isset($remote->archive_html)) ? $remote->archive_html : '' ;
        $local->content_fields          = '';
        $local->save();

        return true;
    }

    
    /**
     * PrepareContent
     *
     * @param  mixed $local
     * @return void
     */
    public static function PrepareContent(ContentInterface $local)
    {
        $data_values = [];

        $data_values['plain_text']      =   $local->content_plain_text;
        $data_values['html']            =   $local->content_html;

        return $data_values;

    }
}