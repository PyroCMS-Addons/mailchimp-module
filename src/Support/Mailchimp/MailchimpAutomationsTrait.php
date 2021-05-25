<?php namespace Thrive\MailchimpModule\Support\Mailchimp;

// Laravel
use Illuminate\Support\Facades\Log;

// Mailchimp
use MailchimpMarketing;
use MailchimpMarketing\ApiException;

/**
 * MailchimpAutomationsTrait
 *
 * Handles all automation-list functionality
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
trait MailchimpAutomationsTrait
{

    /**
     * pauseAutomation
     * 
     * Pause all emails in a specific classic automation workflow.
     *
     * @param  mixed $workflow_id
     * @return bool
     * 
     * @see https://mailchimp.com/developer/marketing/api/automation/pause-automation-emails/
     */
    public function pauseAutomation($workflow_id)
    {
        $response = null;

        $status = true;

        try
        {
            $response = $this->mailchimp->automations->pauseAllEmails($workflow_id);

            return $response;
        }
        catch (\Exception $e)
        {
            $status = false;
        }

        return false;
    }

    /**
     * startAutomation
     * 
     * Start all emails in a classic automation workflow.
     *
     * @param  mixed $workflow_id
     * @return bool
     * 
     * @see https://mailchimp.com/developer/marketing/api/automation/start-automation-emails/
     */
    public function startAutomation($workflow_id)
    {
        $response = null;

        $status = true;

        try
        {
            $response = $this->mailchimp->automations->startAllEmails($workflow_id);

            return $response;
        }
        catch (\Exception $e)
        {
            $status = false;
        }

        return false;
    }



    /**
     * hasAutomation
     *
     * @param  mixed $workflow_id
     * @return bool
     */
    public function hasAutomation($workflow_id)
    {
        $status = true;

        try
        {
            if($response = $this->mailchimp->automations->get($workflow_id))
            {
                return true;
            }
        }
        catch (\Exception $e)
        {
            $status = false;
        }

        return false;
    }


    /**
     * getAutomation
     * 
     * Get a summary of an individual classic automation workflow's 
     * settings and content. The trigger_settings object returns 
     * information for the first email in the workflow.
     *
     * @param  mixed $workflow_id
     * @return bool
     * 
     * @see https://mailchimp.com/developer/marketing/api/automation/get-automation-info/
     */
    public function getAutomation($workflow_id)
    {
        // lets do some basic str check before we send it away !
        $status = true;

        try
        {
            if($response = $this->mailchimp->automations->get($workflow_id))
            {
                return $response;
            }
        }
        catch (\Exception $e)
        {
            $status = false;
        }

        return false;
    }


    /**
     * getAllAutomations
     * 
     * Get a summary of an account's classic automations.
     *
     * @return array
     * 
     * @see https://mailchimp.com/developer/marketing/api/automation/list-automations/
     * 
     */
    public function getAllAutomations()
    {
        $response = $this->mailchimp->automations->list();

        return $response->automations;
    }

}