<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

// Anomaly
use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

// Thrive
use Thrive\MailchimpModule\Support\Harmony;
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Audience\Form\AudienceFormBuilder;
use Thrive\MailchimpModule\Audience\Table\AudienceTableBuilder;

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
     * @param  mixed $messages
     * @param  mixed $repository
     * @return void
     */
    public function sync(MessageBag $messages, AudienceRepository $repository)
    {
        $mailchimp = Mailchimp::Connect();

        $lists = $mailchimp->getAllLists();

        //
        // Import Lists that dont exist locally
        //
        foreach($lists as $list)
        {
            if(!$repository->findBy('str_id',$list->id))
            {
                //check if exist if deleted
                if($repository->allWithTrashed()->findBy('str_id',$list->id))
                {
                    // skip
                    $messages->error('thrive.module.mailchimp::common.error_audiences_clash');
                }
                else
                {
                    $item = new AudienceModel();

                    // we dont have a list
                    if($item = Harmony::createFromMailchimp($list,$item))
                    {
                        $item->save();
                        //updated
                        $item->update(['thrive_sync_status' => 'thrive.module.mailchimp::common.sync_success']);
                    }
                }

            }

        }

        // check if still exist online
        foreach($repository->all() as $item)
        {
            if(!$mailchimp->getList($item->str_id))
            {
                $item->update(['thrive_sync_status' => 'thrive.module.mailchimp::common.not_found']);
            }
        }

        $messages->success('thrive.module.mailchimp::common.now_synched_subscribers');

        return redirect()->back();
    }
}
