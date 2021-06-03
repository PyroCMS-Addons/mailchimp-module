<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormBuilder;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Subscriber\Table\SubscriberTableBuilder;
use Thrive\MailchimpModule\Support\Integration\Subscriber;
use Thrive\MailchimpModule\Support\Mailchimp;

/**
 * SubscribersController
 *
 * Entry point to admin Subscribers
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
class SubscribersController extends AdminController
{

    /**
     * Display an index of existing entries.
     *
     * @param SubscriberTableBuilder $table
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(SubscriberTableBuilder $table)
    {
        return $table->render();
    }



    /**
     * Create a new entry.
     *
     * @param SubscriberFormBuilder $form
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(SubscriberFormBuilder $form)
    {
        return $form->render();
    }

    /**
     * Edit an existing entry.
     *
     * @param SubscriberFormBuilder $form
     * @param        $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(SubscriberFormBuilder $form, $id)
    {

        return $form->render($id);
    }

    
    /**
     * sync
     *
     * @param  mixed $id
     * @param  mixed $messages
     * @param  mixed $repository
     * @return void
     */
    public function sync($id = null, MessageBag $messages, SubscriberRepository $repository) 
    {
        if($id == null)
        {
            if(Subscriber::SyncAll($repository))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_subscribers');
            }
        }
        else
        {
            if(Subscriber::Sync($repository->find($id)))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_subscribers');
            }
        }

        return redirect()->back();
    }

    /**
     * sync_pull
     *
     * @param  MessageBag           $messages
     * @param  AudienceRepository $subscribers
     * @return void
     */
    public function pull($id = null, MessageBag $messages, SubscriberRepository $repository) 
    {
        if($id != null)
        {
            if(Subscriber::Pull($repository->find($id)))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_subscribers');
            }
        }

        // no supporet for controller-pull-all
        // use command line

        return redirect()->back();
    }


    /**
     * sync_push
     * 
     * @param  MessageBag               $messages
     * @param  SubscriberRepository     $subscribers
     *
     * @return void
     */
    public function push($id = null, MessageBag $messages, SubscriberRepository $repository)
    {
        if($id != null)
        {
            if(Subscriber::Post($repository->find($id)))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_subscribers');
            }
        }

        // no supporet for controller-push-all
        // use command line
        return redirect()->back();

    }


}