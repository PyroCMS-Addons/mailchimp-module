<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Facades\Log;

// Thrive
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
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