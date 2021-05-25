<?php namespace Thrive\MailchimpModule\Support\Mailchimp;

// Laravel
use Illuminate\Support\Facades\Log;

// Mailchimp
use MailchimpMarketing;
use MailchimpMarketing\ApiException;


/**
 * MailchimpCampaignTrait
 *
 * Handles all camapign functions for
 * the Mailchimp api-wrapper
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
trait MailchimpCampaignTrait
{

    /**
     * copyCampaign
     * 
     * Copies a campaign.
     * Makes it so much easier to start a new camapign
     * when you can just copy another one!
     *
     * @param  mixed $campaign_id
     * @return void
     * 
     * @see https://mailchimp.com/developer/marketing/api/campaigns/replicate-campaign/
     */
    public function copyCampaign($campaign_id)
    {
        try 
        {             
            $response = $this->mailchimp->campaigns->replicate($campaign_id);
            
            return $response;

        } 
        catch (\Exception $e) 
        {
            Log::error('Unable to locate Campaign, campaign not copied.');
        }
    }

    
    /**
     * updateCampaign
     * 
     * Update some or all of the settings for a specific campaign.
     *
     * @param  mixed $campaign_id
     * @param  mixed $settings
     * @return void
     * 
     * @see https://mailchimp.com/developer/marketing/api/campaigns/update-campaign-settings/
     * 
     */
    public function updateCampaign($campaign_id, $settings)
    {
        try 
        {             
            $response = $this->mailchimp->campaigns->update( $campaign_id, [
                "settings" => $settings,
            ]);

            return $response;
        } 
        catch (\Exception $e) 
        {
            Log::error('Unable to locate Campaign, campaign updated.');
        }
    }
    
    /**
     * sendCampaign
     * 
     * Send a Mailchimp campaign. For RSS Campaigns, 
     * the campaign will send according to its 
     * schedule. All other campaigns will 
     * send immediately.
     *
     * @param  mixed $campaign_id
     * @return void
     * 
     * @see https://mailchimp.com/developer/marketing/api/campaigns/send-campaign/
     */
    public function sendCampaign($campaign_id)
    {
        $response = '';

        try 
        {             
            if($response = $this->mailchimp->campaigns->send($campaign_id))
            {
                return $response;
            }
        } 
        catch (\Exception $e) 
        {
            Log::error('Unable to locate Campaign, campaign not sent.');
        }

        return false;

    }   

    
    /**
     * getAllCamapigns
     *
     * @return void
     */
    public function getAllCamapigns()
    {
        $found = false;
        $response = '';

        try 
        {             
            if($response = $this->mailchimp->campaigns->list())
            {
                $found = true;
            }
        } 
        catch (\Exception $e) 
        {
            Log::error('Unable to fetch Campaigns');
        }

        if($found)
        {
            return $response;
        }

        return false;

    }   

}
