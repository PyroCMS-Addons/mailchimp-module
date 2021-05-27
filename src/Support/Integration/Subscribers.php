<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Facades\Log;

// Thrive
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Mailchimp;

/**
 * Subscribers
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
class Subscribers
{

    const MEMBER_SUBSCRIBED     = 'subscribed';
    const MEMBER_UNSUBSCRIBED   = 'unsubscribed';
    
    /**
     * Sync
     * @todo Needs implementastion
     *
     * @param  mixed $subscriber
     * @return void
     */
    public static function Sync( SubscriberInterface $subscriber )
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            return true;
        }

        return false;
    }

    /**
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * 
     * This is a mess, but better than back in the controller class
     * Needs rework!
     * 
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     */
    public static function SyncAll(AudienceRepository $repository )
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            foreach($repository->all() as $local)
            {
                $lists          = $mailchimp->getMembers( $local->str_id, 'total_items');
                $max_records    = ($lists->total_items) ?? 0;
                $offset     = 2;
                $count      = 3;
                $fields     = null;
                $fields     = 'members.email_address,members.status,members.merge_fields';
                $exfields   = null; //'members.email_address,members.vip,full_name,total_items';

                for($offset = 0; $offset <= $max_records; $offset = $offset + $count)
                {
                    $lists = $mailchimp->getMembers( $local->str_id, $fields, $exfields, $count, $offset );

                    if(isset($lists->members))
                    {
                        if($lists->members)
                        {
                            // dd($lists->members);
                            foreach($lists->members as $member)
                            {
                                // Do we have on in our table ?
                                // remember it has to match the EMAIL and List/Audience to be a match
                                if($model = SubscriberModel::where('audience',$local->str_id)->where('email',$member->email_address)->first())
                                {
                                    // update
                                    $model->email                   = $member->email_address;
                                    $model->thrive_contact_synced   = true;
                                    $model->audience                = $local->str_id;
                                    $model->subscribed              = ($member->status == 'subscribed') ? true: false ;
                                    $model->audience_name           = $local->name ;

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
                                    $model->audience                = $local->str_id;
                                    $model->subscribed              = ($member->status == 'subscribed') ? true: false ;
                                    $model->audience_name           = $local->name ;
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

            }

            return true;
        }

        return false;
    }
    
    
    /**
     * PostSubscriberToMailchimp
     *
     * @param  mixed $entry
     * @return void
     */
    public static function PostSubscriberToMailchimp(SubscriberInterface $entry)
    {
        // Connect to Mailchimp
        if($mailchimp = Mailchimp::Connect())
        {
            // Check to ensure mailchimp still 
            // has this list.
            if($mailchimp->hasList($entry->audience))
            {
                // update contact
                $fname = ($entry->fname != "") ? $entry->fname : null ;
                $lname = ($entry->lname != "") ? $entry->lname : null ;

                //Log::info('Pushing Contact: '. $entry->fname);
                return $mailchimp->setListMemberWithMergeFields(
                                                        $entry->audience, 
                                                        $entry->email, 
                                                        $entry->subscribed, 
                                                        $fname,
                                                        $lname);
            }
        }

        return false;

    }
    
    /**
     * FormatContact
     *
     * @param  mixed $email
     * @param  mixed $subscribe
     * @param  mixed $FNAME
     * @param  mixed $LNAME
     * @return void
     */
    public static function FormatContact($email, $subscribe = true, $FNAME = null, $LNAME = null)
    {
        if($FNAME == null)
        {
            $FNAME = Str::substr($email, 0,  strpos($email, '@') );
        }

        $subscribe_string = ($subscribe) ? self::MEMBER_SUBSCRIBED : self::MEMBER_UNSUBSCRIBED;

        $contact = 
        [
            "email_address"     => $email,
            "status"            => $subscribe_string,
            "merge_fields"      => 
            [
                "FNAME" => $FNAME,
                "LNAME" => $LNAME
            ]
        ];

        return $contact;
    }


    public static function LocalhasSubscriber($email,$audience_id)
    {
        if($subscriber = SubscriberModel::where('email',$email)->where('audience',$audience_id)->first())
        {
            return $subscriber;
        }

        return false;
    }


    public static function PrepareContact($email, $subscribe = true, $FNAME = '', $LNAME = '')
    {

        $subscribe_string = ($subscribe) ? 'subscribed' : 'unsubscribed';

        $contact = 
        [
            "email_address" => $email,
            "status" => $subscribe_string,
            "merge_fields" => 
            [
                "FNAME" => $FNAME,
                "LNAME" => $LNAME
            ]
        ];

        return $contact;
    }
}