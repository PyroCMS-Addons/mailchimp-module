<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

// Anomaly
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;
use Thrive\MailchimpModule\Support\Integration\Webhook;
use Thrive\MailchimpModule\Webhook\Form\WebhookFormBuilder;
use Thrive\MailchimpModule\Webhook\Table\WebhookTableBuilder;
use Thrive\MailchimpModule\Webhook\WebhookModel;
use Thrive\MailchimpModule\Webhook\WebhookRepository;

// Thrive



/**
 * WebhooksController
 *
 * The Entrypoint to Admin/Webhooks
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
class WebhooksController extends AdminController
{

    /**
     * index
     *
     * @param  mixed $table
     * @return void
     */
    public function index(WebhookTableBuilder $table)
    {
        return $table->render();
    }

    /**
     * create
     *
     * @param  mixed $form
     * @return void
     */
    public function create(WebhookFormBuilder $form)
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
    public function edit(WebhookFormBuilder $form, $id)
    {
        return $form->render($id );
    }

    

    /**
     * sync
     *
     * @param  mixed $messages
     * @param  mixed $repository
     * @return void
     */
    public function sync( $id = null, MessageBag $messages, WebhookRepository $repository )
    {
        if($id == null)
        {
            //syncall
            if(Webhook::SyncAll($repository))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_webhooks');
            }
        }
        else
        {
            if(Webhook::Sync($repository->find($id)))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_webhooks');
            }
        }

        return redirect()->back();
    }


    public function pull( $id = null, MessageBag $messages, WebhookRepository $repository )
    {
        if($id == null)
        {
            //syncall
            if(Webhook::PullAll())
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_webhooks');
            }
        }

        return redirect()->back();
    }    

    public function delete_force( $id = null, MessageBag $messages, WebhookRepository $repository )
    {
        if($id != null)
        {
            //syncall
            if($webhook = WebhookModel::find($id))
            {
                if(Webhook::DeleteFromRemote($webhook))
                {
                    $webhook->forceDelete();   
                }
                else
                {
                    $messages->error('Unable to delete from remote System. The local Webhook has been moved to trash.');

                    $webhook->delete();
                }
                
                $messages->success('thrive.module.mailchimp::common.now_synched_webhooks');
            }
        }

        return redirect()->back();
    }    

    
}
