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
     * @param  mixed $campaign
     * @return void
     */
    public static function Sync(CampaignInterface $campaign )
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            if($settings = self::PrepareCampaign($campaign))
            {
                if($mailchimp->hasCampaign($campaign->campaign_remote_id))
                {
                    return $mailchimp->updateCampaign($campaign->campaign_remote_id, $settings);
                }
                else
                {
                    return $mailchimp->createCampaign($settings);
                }
            }
        }

        return false;
    }
    

    public static function SyncById($campaign_id)
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            Log::debug('Search Remote Mailchimp for Campaign ID :' . $campaign_id);

            if($remote = $mailchimp->getCampaign($campaign_id))
            {
                if($local = CampaignModel::where('campaign_remote_id',$campaign_id)->first())
                {
                    // do we have a local campaign with that ID ?
                    // yes -> update/pull
                    Log::debug('Found Local Campaign, now Updating Local Values');

                    self::UpdateLocalCampaignFromRemote($local, $remote);
                }
                else
                {
                    //no
                    //create with remote details
                    Log::debug('Unable to find Local Campaign, Creating a Local Campaign');

                    self::CreateLocalCampaignFromRemote($remote);
                }

                return true;
            }
            else
            {
                //exit, this function requires external campaign
                return false;
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
        // Log::error('Start SyncAll from Campaigns');

        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            $campaigns = $mailchimp->getAllCamapigns();

            if(isset(($campaigns->campaigns)))
            {
                $campaigns = $campaigns->campaigns;
    
                foreach($campaigns as $campaign)
                {
                    if($local = $repository->findBy('campaign_remote_id', $campaign->id))
                    {
                        //Log::error('Found Local Campaign - Lets Update ID : ' .$campaign->id);

                        // Update Local
                        self::UpdateLocalCampaignFromRemote($local, $campaign);    
                    }
                    else
                    {
                        // Not found
                        //check if exist if deleted
                        if($repository->allWithTrashed()->findBy('campaign_remote_id',$campaign->id))
                        {
                            //$messages->error('You may have some Camapigns not included as we found similar Ids in the trashed area..');
                        }
                        else
                        {
                            self::CreateLocalCampaignFromRemote($campaign);    
                        }                
                    }
                }            
            }

            return true;
        }

        return false;
    }
    
        
    /**
     * Post local Campaig  to Mailchimp
     * Override remote
     *
     * @param  mixed $campaign
     * @return void
     */
    public static function Post(CampaignInterface $campaign )
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            if($settings = self::PrepareCampaign($campaign))
            {
                if($mailchimp->hasCampaign($campaign->campaign_remote_id))
                {
                    return $mailchimp->updateCampaign($campaign->campaign_remote_id, $settings);
                }
                else
                {
                    return $mailchimp->createCampaign($settings);
                }
            }
        }

        return false;
    }
    
    /**
     * PostAll
     *
     * @param  mixed $repository
     * @return bool
     */
    public static function PostAll(CampaignRepository $repository) : bool
    {
        if($mailchimp = Mailchimp::Connect())
        {
            foreach($repository->all() as $campaign)
            {
                if(!self::Post($campaign))
                {
                    Log::info('Unable to Post ' . $campaign->campaign_name . ' [' . $campaign->campaign_remote_id . ']' );
                }
            }

            return true;
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
            if($remote_campaign = $mailchimp->copyCampaign($entry->campaign_remote_id))
            {
                return self::CreateLocalCampaignFromRemote($remote_campaign);
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
            return $mailchimp->sendCampaign($entry->campaign_remote_id);
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
            return $mailchimp->sendTestCampaign($entry->campaign_remote_id, $email_array);
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

    public static function CreateLocalCampaignFromRemote($remote)
    {
        //Log::debug( print_r($remote,true) );
        try 
        {
            $local = new CampaignModel();
            $local->campaign_name            = $remote->settings->title;
            $local->campaign_type            = $remote->type;
            $local->campaign_list_id         = $remote->recipients->list_id;  
            $local->campaign_status          = $remote->status;
            $local->campaign_remote_id       = $remote->id;
            $local->campaign_subject_line    = $remote->settings->subject_line;
            $local->campaign_from_name       = $remote->settings->from_name;
            $local->campaign_reply_to        = $remote->settings->reply_to;        

            // @deprecated ststus field
            $local->campaign_sync_status = ''; //@deprecated

            $local->save();

            return $local;
        }
        catch(\Exception $e)
        {
            Log::error($e->getMessage());
        }

        return false;
    }

    public static function UpdateLocalCampaignFromRemote($local, $remote)
    {
        // Update Local
        $local->campaign_name           = $remote->settings->title;
        $local->campaign_subject_line   = $remote->settings->subject_line;
        $local->campaign_subject_line   = $remote->settings->subject_line;
        $local->campaign_from_name      = $remote->settings->from_name;
        $local->campaign_reply_to       = $remote->settings->reply_to;            
        $local->campaign_type           = $remote->type;
        $local->campaign_list_id        = $remote->recipients->list_id;  
        $local->campaign_status         = $remote->status;
        $local->campaign_remote_id      = $remote->id;

        // @deprecated status field
        $local->campaign_sync_status = ''; //@deprecated
        $local->save();

        return true;
    }
}