<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Campaign\CampaignModel;
use Thrive\MailchimpModule\Campaign\CampaignRepository;
use Thrive\MailchimpModule\Campaign\Form\CampaignFormBuilder;
use Thrive\MailchimpModule\Campaign\Table\CampaignTableBuilder;
use Thrive\MailchimpModule\Support\Integration\Campaign;
use Thrive\MailchimpModule\Support\Integration\Content;
use Thrive\MailchimpModule\Support\Mailchimp;

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
     * edit
     *
     * @param  mixed $form
     * @param  mixed $id
     * @return void
     */
    public function edit(CampaignFormBuilder $form, $id)
    {

        if($campaign = CampaignModel::find($id))
        {
            if($result = Content::Get($campaign->campaign_str_id))
            {
                return $form->render( $id , ['html' => $result->html]);
            }
        }

        // the following view is a good for a popup
        // return view('thrive.module.mailchimp::admin.content.view')->withContent($html->result);
            
        return $form->render( $id );
    } 
    
    /**
     * copy
     *
     * @param  mixed $id
     * @return void
     */
    public function copy($id)
    {
        if($campaign = CampaignModel::find($id))
        {
            if($remote_campaign = Campaign::Copy($campaign))
            {

                // dd($remote_campaign);
                // create local
                $newcampaign = new CampaignModel;
                $newcampaign->campaign_name     = $remote_campaign->settings->title;
                $newcampaign->campaign_type     = $remote_campaign->type;
                $newcampaign->list_id           = $remote_campaign->recipients->list_id;
                $newcampaign->campaign_sync_status = 'Synched';
                $newcampaign->status            = $remote_campaign->status;
                $newcampaign->campaign_str_id   = $remote_campaign->id;

                $newcampaign->campaign_subject_line     = $remote_campaign->settings->subject_line;
                $newcampaign->campaign_from_name        = $remote_campaign->settings->from_name;
                $newcampaign->campaign_reply_to         = $remote_campaign->settings->reply_to;
                $newcampaign->save();

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
     * sync
     *
     * @param  mixed $messages
     * @param  mixed $repository
     * @return void
     */
    public function sync( MessageBag $messages, CampaignRepository $repository )
    {

        if($campaign = CampaignModel::find($id))
        {
            if(Campaign::Sync($campaign, $repository))
            {
                $messages->success('thrive.module.mailchimp::common.now_synched_campaigns');
            }
        }
        
        return redirect()->back();
    }
}
