<?php namespace Thrive\MailchimpModule\Support\Integration;

use Illuminate\Support\Facades\Log;

// Thrive
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Automation\AutomationModel;
use Thrive\MailchimpModule\Automation\AutomationRepository;
use Thrive\MailchimpModule\Automation\Contract\AutomationInterface;

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
     * Sync Automations to the PyroCMS system
     *
     * @param  mixed $repository
     * @return void
     */
    public static function Sync(AutomationRepository $repository)
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
                        $local_entry->automation_workflow_id      = $automation->id;
                        $local_entry->automation_title            = $automation->settings->title;
                        $local_entry->automation_status           = $automation->status;
                        $local_entry->automation_start_time       = $automation->start_time;
                        $local_entry->automation_create_time      = $automation->create_time;
                        $local_entry->automation_emails_sent      = $automation->emails_sent;
                        $local_entry->automation_list_id          = $automation->recipients->list_id;
                        $local_entry->automation_from_name        = $automation->settings->from_name;
                        $local_entry->automation_reply_to         = $automation->settings->reply_to;
                        $local_entry->save();
                    }
                    else
                    {
                        if($automation->status == 'archived')
                        {
                            // do we download this ?
                        }   
                        else
                        {
                            $m = new AutomationModel;
                            $m->automation_workflow_id      = $automation->id;
                            $m->automation_title            = $automation->settings->title;
                            $m->automation_status           = $automation->status;
                            $m->automation_start_time       = $automation->start_time;
                            $m->automation_create_time      = $automation->create_time;
                            $m->automation_emails_sent      = $automation->emails_sent;
                            $m->automation_list_id          = $automation->recipients->list_id;
                            $m->automation_from_name        = $automation->settings->from_name;
                            $m->automation_reply_to         = $automation->settings->reply_to;
                            $m->save();
                        }

                    }


                }
            }
        }

        return false;
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
            // dd('here');
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
}