<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

// Anomaly
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;

// Thrive
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Automation\AutomationModel;
use Thrive\MailchimpModule\Support\Integration\Automation;
use Thrive\MailchimpModule\Automation\AutomationRepository;
use Thrive\MailchimpModule\Automation\Form\AutomationFormBuilder;
use Thrive\MailchimpModule\Automation\Table\AutomationTableBuilder;


/**
 * AutomationsController
 *
 * The Entrypoint to Admin/Automations
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
class AutomationsController extends AdminController
{

    /**
     * index
     *
     * @param  mixed $table
     * @return void
     */
    public function index(AutomationTableBuilder $table)
    {
        return $table->render();
    }

    /**
     * create
     *
     * @param  mixed $form
     * @return void
     */
    public function create(AutomationFormBuilder $form)
    {
        return $form->render();
    }


    /**
     * edit
     *
     * @param  mixed $form
     * @param  mixed $id
     * @return void
     */
    public function edit(AutomationFormBuilder $form, $id)
    {
        return $form->render($id );
    }


    /**
     * send
     *
     * @param  mixed $form
     * @param  mixed $id
     * @param  mixed $messages
     * @return void
     */
    public function send(AutomationFormBuilder $form, $id, MessageBag $messages)
    {
        $mailchimp = Mailchimp::Connect();

        $campaign = AutomationModel::find($id);

        $messages->success('thrive.module.mailchimp::message.automation_sent');

        return redirect()->back();
    }


    /**
     * sync
     *
     * @param  mixed $messages
     * @param  mixed $repository
     * @return void
     */
    public function sync( MessageBag $messages, AutomationRepository $repository )
    {

        if(Automation::sync($repository))
        {
            $messages->success('thrive.module.mailchimp::common.now_synched_automations');
        }

        return redirect()->back();
    }
}
