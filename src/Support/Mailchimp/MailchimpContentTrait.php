<?php namespace Thrive\MailchimpModule\Support\Mailchimp;


// Laravel
use Illuminate\Support\Facades\Log;

// Mailchimp
use MailchimpMarketing;
use MailchimpMarketing\ApiException;


/**
 * MailchimpContentTrait
 *
 * Handles content/campaign newsletter functionality
 * for the Mailchimp api-wrapper
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
trait MailchimpContentTrait
{
    
    /**
     * getCampaignContent
     *
     * @param  mixed $campaign_id
     * @return void
     */
    public function getCampaignContent($campaign_id)
    {
        try 
        {             
            $response = $this->mailchimp->campaigns->getContent($campaign_id);
            
            return $response;
        } 
        catch (\Exception $e) 
        {
            Log::error('Unable to locate Campaign content.');
        }

        return false;
    }
 
}
