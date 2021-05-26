<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Facades\Log;

// Thrive
use Thrive\MailchimpModule\Campaign\CampaignModel;
use Thrive\MailchimpModule\Campaign\CampaignRepository;
use Thrive\MailchimpModule\Campaign\Contract\CampaignInterface;
use Thrive\MailchimpModule\Support\Mailchimp;


/**
 * Campaign
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
class Campaign
{
    
    /**
     * Sync
     *
     * @param  mixed $entry
     * @return void
     */
    public static function Sync(CampaignInterface $entry )
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            if($settings = self::PrepareCampaign($entry))
            {
                if($mailchimp->hasCampaign($entry->str_id))
                {
                    return $mailchimp->updateCampaign($entry->str_id, $settings);
                }
                else
                {
                    $mailchimp->createCampaign($settings);
                }
            }
        }

        return false;
    }
    

    /**
     * SyncAll
     *
     * @param  mixed $repository
     * @return void
     */
    public static function SyncAll(CampaignRepository $repository )
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            $campaigns = $mailchimp->getAllCamapigns();

            if(isset(($campaigns->campaigns)))
            {
                $campaigns = $campaigns->campaigns;
    
                foreach($campaigns as $campaign)
                {
                    if($local = $repository->findBy('campaign_str_id', $campaign->id))
                    {
                        // Update Local
                        $local->campaign_name        = $campaign->settings->title;
                        $local->campaign_type        = $campaign->type;
                        $local->list_id              =  $campaign->recipients->list_id;  
                        $local->campaign_sync_status = 'Synched';
                        $local->status               = $campaign->status;
                        $local->campaign_str_id      = $campaign->id;
                        $local->save();
    
                    }
                    else
                    {
                        // Not found
                        //check if exist if deleted
                        if($repository->allWithTrashed()->findBy('str_id',$campaign->id))
                        {
                            $messages->error('You may have some Camapigns not included as we found similar Ids in the trashed area..');
                        }
                        else
                        {
                            // dd($campaign);
                            $item = new CampaignModel();
                            $item->campaign_name        = $campaign->settings->title;
                            $item->campaign_type        = $campaign->type;
                            $item->list_id              = $campaign->recipients->list_id;  
                            $item->campaign_sync_status = 'Synched';
                            $item->status               = $campaign->status;
                            $item->campaign_str_id      = $campaign->id;
                            $item->save();
                            // $item->update(['thrive_sync_status' => 'Just Created']);
                        }                
                    }
                }            
            }
        }

        return false;
    }
    
        
    /**
     * Post local Campaig  to Mailchimp
     * Override remote
     *
     * @param  mixed $entry
     * @return void
     */
    public static function Post(CampaignInterface $entry )
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            if($settings = self::PrepareCampaign($entry))
            {
                if($mailchimp->hasCampaign($entry->str_id))
                {
                    return $mailchimp->updateCampaign($entry->str_id, $settings);
                }
                else
                {
                    $mailchimp->createCampaign($settings);
                }
            }
        }

        return false;
    }

    public static function PostAll(CampaignRepository $repository )
    {
        if($mailchimp = Mailchimp::Connect())
        {

        }

        return false;
    }

    /**
     * PostLocalEntryToMailchimp
     * 
     * @deprecated - use self::Post
     *
     * @param  mixed $entry
     * @return void
     */
    public static function PostLocalEntryToMailchimp(CampaignInterface $entry)
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            $settings = [];

            if(isset($entry->campaign_subject_line) && $entry->campaign_subject_line != "")
            {
                $settings["subject_line"] = $entry->campaign_subject_line;
            }

            if(isset($entry->campaign_from_name) && $entry->campaign_from_name != "")
            {
                $settings["from_name"] = $entry->campaign_from_name;
            }

            if(isset($entry->campaign_reply_to) && $entry->campaign_reply_to != "")
            {
                $settings["reply_to"] = $entry->campaign_reply_to;
            }

            if(isset($entry->campaign_name) && $entry->campaign_name != "")
            {
                $settings["title"] = $entry->campaign_name;
            }

            return $mailchimp->updateCampaign(
                                $entry->campaign_str_id,
                                $settings);
        }

        return false;

    }


    /**
     * Copy
     *
     * @param  mixed $entry
     * @return void
     */
    public static function Copy(CampaignInterface $entry)
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            if($newcampaign = $mailchimp->copyCampaign($entry->campaign_str_id))
            {
                // create local
                $newcampaign = new CampaignModel;
                $newcampaign->campaign_name             = $remote_campaign->settings->title;
                $newcampaign->campaign_type             = $remote_campaign->type;
                $newcampaign->list_id                   = $remote_campaign->recipients->list_id;
                $newcampaign->campaign_sync_status      = 'Synchronized';
                $newcampaign->status                    = $remote_campaign->status;
                $newcampaign->campaign_str_id           = $remote_campaign->id;
                $newcampaign->campaign_subject_line     = $remote_campaign->settings->subject_line;
                $newcampaign->campaign_from_name        = $remote_campaign->settings->from_name;
                $newcampaign->campaign_reply_to         = $remote_campaign->settings->reply_to;
                $newcampaign->save();
            }
        }

        return false;
    }

    
    /**
     * Send
     *
     * @param  mixed $entry
     * @return void
     */
    public static function Send(CampaignInterface $entry)
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            return $mailchimp->sendCampaign($entry->campaign_str_id);
        }

        return false;

    }
    
        
    /**
     * SendTest
     *
     * @param  mixed $entry
     * @param  mixed $email_array
     * @return void
     */
    public static function SendTest(CampaignInterface $entry, array $email_array)
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            return $mailchimp->sendTestCampaign($entry->campaign_str_id, $email_array);
        }

        return false;
    }
    


    
    /**
     * PrepareCampaign
     *
     * @param  mixed $entry
     * @return void
     */
    public static function PrepareCampaign(CampaignInterface $entry)
    {

        $settings = [];

        // required for create
        if(isset($entry->campaign_type) && $entry->campaign_type != "")
        {
            $settings["type"] = $entry->campaign_type;
        }

        if(isset($entry->campaign_subject_line) && $entry->campaign_subject_line != "")
        {
            $settings["subject_line"] = $entry->campaign_subject_line;
        }

        if(isset($entry->campaign_from_name) && $entry->campaign_from_name != "")
        {
            $settings["from_name"] = $entry->campaign_from_name;
        }

        if(isset($entry->campaign_reply_to) && $entry->campaign_reply_to != "")
        {
            $settings["reply_to"] = $entry->campaign_reply_to;
        }

        if(isset($entry->campaign_name) && $entry->campaign_name != "")
        {
            $settings["title"] = $entry->campaign_name;
        }

        return $settings;

    }    
}