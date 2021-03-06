<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Campaign\CampaignModel;
use Thrive\MailchimpModule\Campaign\CampaignRepository;
use Thrive\MailchimpModule\Campaign\Form\CampaignFormBuilder;
use Thrive\MailchimpModule\Campaign\Table\CampaignTableBuilder;
use Thrive\MailchimpModule\Content\Form\ContentFormBuilder;
use Thrive\MailchimpModule\Content\Form\PreviewContentFormBuilder;
use Thrive\MailchimpModule\Support\Integration\Campaign;
use Thrive\MailchimpModule\Support\Integration\Content;
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Support\Ui\Chooser;

/**
 * CampaignsController
 *
 * Entry point for Admin/Campaigns
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
class CampaignsController extends AdminController
{
    
    /**
     * index
     *
     * @param  mixed $table
     * @return void
     */
    public function index(CampaignTableBuilder $table)
    {
        return $table->render();
    }
    
    /**
     * create
     *
     * @param  mixed $form
     * @return void
     */
    public function create(CampaignFormBuilder $form)
    {
        return $form->render();
    }

    
    /**
     * option
     * @todo    Will need to rename this method to 
     *          choose for best practice.
     *
     * @param  mixed $method
     * @param  mixed $id
     * @param  mixed $messages
     * @return void
     */
    public function option($method = 'edit', $id, MessageBag $messages)
    {
        if($campaign = CampaignModel::find($id))
        {
            $actions = Chooser::CampaignActions($campaign);

            return $this->view->make(
                'module::admin/campaigns/choose',
                [
                    'id'            => $id,
                    'method'        => $method,
                    'actions'       => $actions,
                ]
            );
        }

        // errr
        return redirect()->back();
    } 


    
    /**
     * edit
     *
     * @param  mixed $form
     * @param  mixed $id
     * @return void
     */
    public function edit(CampaignFormBuilder $form, $id, MessageBag $messages)
    {
        if($campaign = CampaignModel::find($id))
        {
            if(!$campaign->canEdit())
            {
                // @todo: add to lang files
                $messages->error('This is locked');

                //redirect away
                return redirect()->back();
            }

            if($result = Content::GetPreview($campaign))
            {
                // $form->getForm()->addData('email_template',$result->html);
                $form->addFormData('email_template',$result->content_html);
                // $form->getForm()->addData('id',$id);
                $form->addFormData('id',$id);

                return $form->render( $id );
            }
        }
  
        return $form->render( $id );
    } 

    
    /**
     * preview
     *
     * @param  mixed $form
     * @param  mixed $id    This is the Campaign id, not the content id. 
     * @param  mixed $messages
     * @return void
     */
    public function preview(PreviewContentFormBuilder $form, $id, MessageBag $messages)
    {
        if($campaign = CampaignModel::find($id))
        {
            if($result = Content::GetPreview($campaign))
            {
                $form->addFormData('email_template',$result->content_html);
                $form->addFormData('id', $id);

                return $form->render( $id );
            }
        }

        $messages->info('oops, this message needs updating.');

        return redirect()->back();
    } 

    public function template(ContentFormBuilder $form, $id, MessageBag $messages)
    {
        if($campaign = CampaignModel::find($id))
        {
            if($result = Content::GetPreview($campaign))
            {
                $form->addFormData('email_template',$result->html);
                $form->addFormData('id', $id);

                return $form->render( $id );
            }
        }

        $messages->info('oops, this message needs updating.');

        return redirect()->back();
    }  
    /**
     * copy
     *
     * @param  mixed $id
     * @return void
     */
    public function copy($id, MessageBag $messages)
    {
        if($campaign = CampaignModel::find($id))
        {
            if($remote_campaign = Campaign::Copy($campaign))
            {
                $messages->success('thrive.module.mailchimp::message.campaigns_sent');
            }
        }

        return redirect()->back();
    }
    
    /**
     * send
     *
     * @param  mixed $form
     * @param  mixed $id
     * @param  mixed $messages
     * @return void
     */
    public function send(CampaignFormBuilder $form, $id, MessageBag $messages)
    {
        if($campaign = CampaignModel::find($id))
        {
            if(Campaign::Send($campaign))
            {
                $messages->success('thrive.module.mailchimp::message.campaigns_sent');
            }
        }

        return redirect()->back();
    }
    
    /**
     * send_test
     *
     * @param  mixed $id
     * @param  mixed $messages
     * @return void
     */
    public function send_test($id, MessageBag $messages)
    {
        $settings = app(\Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface::class);

        if($campaign = CampaignModel::find($id))
        {
            $email = $settings->value('thrive.module.mailchimp::mailchimp_test_email');

            if(Campaign::SendTest($campaign, [$email] ))
            {
                $messages->success('thrive.module.mailchimp::message.campaigns_sent');
            }
        }

        return redirect()->back();
    }
    

    /**
     * sync
     *
     * @param  mixed $messages
     * @param  mixed $repository
     * @return void
     */
    public function sync( $id = null, MessageBag $messages, CampaignRepository $repository )
    {
        if($campaign = CampaignModel::find($id))
        {
            if(Campaign::Sync($campaign))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_campaigns');
            }
        }
        else
        {
            if(Campaign::SyncAll($repository))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_campaigns');
            }
        }
        
        return redirect()->back();
    }
}
