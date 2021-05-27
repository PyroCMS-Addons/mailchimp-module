<?php namespace Thrive\MailchimpModule\Support\Integration;

use Exception;

// Thrive
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Automation\AutomationModel;
use Thrive\MailchimpModule\Automation\AutomationRepository;
use Thrive\MailchimpModule\Automation\Contract\AutomationInterface;
use Thrive\MailchimpModule\Support\Mailchimp;

/**
 * Automation
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
class Automation
{
    
        
    /**
     * Sync
     * 
     * There is no UpdateAutomation on MC, however there is a 
     * AddAutomation . So we look for any created locally, 
     * that need to be added remotely.
     * 
     * @see https://mailchimp.com/developer/marketing/api/automation/add-automation/
     *
     * @param  mixed $automation
     * @return void
     * @throws Exception
     */    

    public static function Sync(AutomationInterface $automation)
    {
        throw new \Exception('Not Yet Implemented');
        
        if($mailchimp = Mailchimp::Connect())
        {
            // $remote-list = get a list of all remote automations
            //foreach local automations
                // is there a copy in the $remote-list
                // if not, push new automation
            //end
            return true;
        }

        return false;
    }

    public static function SyncAll(AutomationRepository $repository)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            if($automations = $mailchimp->getAllAutomations())
            {
                foreach($automations as $automation)
                {
                    // check to see if we have the automation
                    if($local_entry = $repository->findBy('automation_workflow_id',$automation->id ))
                    {
                        self::UpdateLocalAutomationFromRemote( $local_entry, $automation );
                    }
                    else
                    {
                        // we dont create archived
                        if($automation->status != 'archived')
                        {
                            self::CreateLocalAutomationFromRemote( $automation );
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    
    /**
     * Post
     *
     * @param  mixed $automation
     * @return void
     */
    public static function Post(AutomationInterface $automation)
    {
        throw new \Exception('Not Yet Implemented');
    }
	
	/**
	 * PostAll
	 *
	 * @param  mixed $repository
	 * @return void
	 */
	public static function PostAll(AutomationRepository $repository)
    {
        throw new \Exception('Not Yet Implemented');
    }


    /**
     * Start
     *
     * @param  mixed $entry
     * @return void
     */
    public static function Start(AutomationInterface $entry)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            if($automation = $mailchimp->startAutomation($entry->automation_workflow_id))
            {
                $entry->automation_status = $automation->status;
                $entry->save();
            }
            else
            {
                Log::error('failed return');
            }
        }

        return false;
    }

    
    /**
     * Pause
     *
     * @param  mixed $entry
     * @return void
     */
    public static function Pause(AutomationInterface $entry)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            if($automation = $mailchimp->pauseAutomation($entry->automation_workflow_id))
            {
                $entry->automation_status = $automation->status;
                $entry->save();
            }
            else
            {
                Log::error('failed return');
            }
        }

        return false;
    }

    
    /**
     * Stop
     *
     * @param  mixed $entry
     * @return void
     */
    public static function Stop(AutomationInterface $entry)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            return $mailchimp->stopAutomation($entry->automation_workflow_id);
        }

        return false;
    }    

        
    /**
     * CreateLocalAutomationFromRemote
     *
     * @param  mixed $remote
     * @return void
     */
    public static function CreateLocalAutomationFromRemote( $remote )
    {
        try
        {
            $local = new AutomationModel;
            $local->automation_workflow_id      = $remote->id;
            $local->automation_title            = $remote->settings->title;
            $local->automation_status           = $remote->status;
            $local->automation_start_time       = $remote->start_time;
            $local->automation_create_time      = $remote->create_time;
            $local->automation_emails_sent      = $remote->emails_sent;
            $local->automation_list_id          = $remote->recipients->list_id;
            $local->automation_from_name        = $remote->settings->from_name;
            $local->automation_reply_to         = $remote->settings->reply_to;
            $local->save();
            return true;
        }
        catch(\Exception $e)
        {
            // do-stuff
        }

        return false;
    }

    /**
     * UpdateLocalAutomationFromRemote
     *
     * @param  mixed $automation
     * @param  mixed $remote
     * @return void
     */
    public static function UpdateLocalAutomationFromRemote( AutomationInterface $automation, $remote )
    {
        try
        {
            $automation->automation_workflow_id      = $remote->id;
            $automation->automation_title            = $remote->settings->title;
            $automation->automation_status           = $remote->status;
            $automation->automation_start_time       = $remote->start_time;
            $automation->automation_create_time      = $remote->create_time;
            $automation->automation_emails_sent      = $remote->emails_sent;
            $automation->automation_list_id          = $remote->recipients->list_id;
            $automation->automation_from_name        = $remote->settings->from_name;
            $automation->automation_reply_to         = $remote->settings->reply_to;
            $automation->save();
            return true;
        }
        catch(\Exception $e)
        {
            // do-stuff
        }

        return false;
    }
}