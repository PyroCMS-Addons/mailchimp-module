<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

// Anomaly
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;

// Thrive
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Audience\Form\AudienceFormBuilder;
use Thrive\MailchimpModule\Audience\Table\AudienceTableBuilder;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Integration\Audience;
use Thrive\MailchimpModule\Support\Mailchimp;

/**
 * AudiencesController
 *
 * Handles all audiunce/list functionality
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
class AudiencesController extends AdminController
{
    
    /**
     * index
     *
     * @param  mixed $table
     * @return void
     */
    public function index(AudienceTableBuilder $table)
    {
        return $table->render();
    }
    
    /**
     * create
     *
     * @param  mixed $form
     * @return void
     */
    public function create(AudienceFormBuilder $form)
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
    public function edit(AudienceFormBuilder $form, $id)
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
    public function sync($id = null, MessageBag $messages, AudienceRepository $repository)
    {
        if($entry = AudienceModel::find($id))
        {
            Audience::Sync($entry);
        }
        else
        {
            Audience::SyncAll($repository);
        }

        return redirect()->back();
    }  


    
    /**
     * delete
     *
     * @param  mixed $id
     * @param  mixed $messages
     * @param  mixed $subscribers
     * @return void
     */
    public function delete($id = null, MessageBag $messages, SubscriberRepository $subscribers)
    {
        if($audience = AudienceModel::find($id))
        {
            if(Audience::Delete($audience))
            {
                // Now delete all subscribers
                if($subscribers->deleteByAudienceId($audience->audience_remote_id))
                {
                    // set messages
                }
            }
        }

        return redirect()->back();
    }      
 
}
