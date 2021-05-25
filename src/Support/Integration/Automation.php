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
            return $mailchimp->startAutomation($entry->automation_workflow_id);
        }

        return false;
    }
}