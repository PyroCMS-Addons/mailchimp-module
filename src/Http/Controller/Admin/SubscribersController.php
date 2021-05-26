<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;
use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormBuilder;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Subscriber\Table\SubscriberTableBuilder;
use Thrive\MailchimpModule\Support\Integration\Subscribers;
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
     * sync_pull
     *
     * @param  MessageBag           $messages
     * @param  AudienceRepository   $repository
     * @param  SubscriberRepository $subscribers
     * @return void
     */
    public function sync_pull(MessageBag $messages,
                              AudienceRepository $repository,
                              SubscriberRepository $subscribers) {

        // Pass 1:
        //@todo - get the list to sync, or find a way to loop thropugh all lists
        foreach($repository->all() as $repo)
        {
            $this->doPull($repo->str_id, $repo);
        }
        $messages->success('thrive.module.mailchimp::common.now_synched_subscribers');


        return redirect()->back();
    }

    /**
     * doPull
     *
     * @param  string                   $list_id
     * @param  SubscriberInterface      $repo
     * @return void
     */
    private function doPull($list_id, $repo)
    {

        $mailchimp = Mailchimp::Connect();

        // get the total count
        $lists = $mailchimp->getMembers( $list_id, 'total_items');

        $max_records = ($lists->total_items) ?? 0;
        // dd($max_records);

        $offset     = 2;
        $count      = 3;
        // $fields     = null;
        // $exfields   = null; //'members.email_address,members.vip,full_name,total_items';

        $fields     = null;
        $fields     = 'members.email_address,members.status,members.merge_fields';
        $exfields   = null; //'members.email_address,members.vip,full_name,total_items';

        // $lists = $mailchimp->getMembers( $list_id, $fields, $exfields, $count, $offset );
        // dd($lists);

        // make a call to get the total recods
        for($offset = 0; $offset <= $max_records; $offset = $offset + $count)
        {
            $lists = $mailchimp->getMembers( $list_id, $fields, $exfields, $count, $offset );

            if(isset($lists->members))
            {
                if($lists->members)
                {
                    // dd($lists->members);
                    foreach($lists->members as $member)
                    {
                        // Do we have on in our table ?
                        // remember it has to match the EMAIL and List/Audience to be a match
                        if($model = SubscriberModel::where('audience',$list_id)->where('email',$member->email_address)->first())
                        {
                            // update
                            $model->email                   = $member->email_address;
                            $model->thrive_contact_synced   = true;
                            $model->audience                = $list_id;
                            $model->subscribed              = ($member->status == 'subscribed') ? true: false ;
                            $model->audience_name           = $repo->name ;

                            // dd($member);
                            $model->fname                   = $member->merge_fields->FNAME ;
                            $model->lname                   = $member->merge_fields->LNAME ;
                            $model->save();
                        }
                        else
                        {
                            // create
                            $model = new SubscriberModel();
                            $model->email                   = $member->email_address;
                            $model->thrive_contact_synced   = true;
                            $model->audience                = $list_id;
                            $model->subscribed              = ($member->status == 'subscribed') ? true: false ;
                            $model->audience_name           = $repo->name ;
                            $model->fname                   = $member->merge_fields->FNAME ;
                            $model->lname                   = $member->merge_fields->LNAME ;
                            $model->save();
                        }

                    }
                }
                else
                {
                    //oops we did not expect this.
                    //report issue and rediurect away
                }
            }
            else
            {
                //oops we did not expect this.
                //report issue and rediurect away
            }

        }

        // @todo - now we want to check if the members are part of a list that no longer exist!
        // what do we do here?

    }



    /**
     * sync_push
     *
     * @param  MessageBag               $messages
     * @param  AudienceRepository       $repository
     * @param  SubscriberRepository     $subscribers
     *
     * @return void
     */
    public function sync_push(
        MessageBag $messages,
        AudienceRepository $repository,
        SubscriberRepository $subscribers)
    {

        // Pass 1:
        //@todo - get the list to sync, or find a way to loop thropugh all lists
        foreach($repository->all() as $repo)
        {
            $this->doPush($repo->str_id, $repo);
        }
        
        $messages->success('thrive.module.mailchimp::common.now_synched_subscribers');
        return redirect()->back();

    }


    /**
     * Push users to mailchimp
     */
    private function doPush($list_id, $repo)
    {
        Log::debug('--- [ Begin ] ---  SubscribersController::doPush ');

        $local = SubscriberModel::all();

        // dd($local);
        foreach($local as $subscriber)
        {
            Log::debug('  » 00 Pushing User        : ' . $subscriber->email);

            Subscribers::PostSubscriberToMailchimp($subscriber);
        }

    }

}