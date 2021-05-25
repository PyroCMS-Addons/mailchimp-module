<?php namespace Thrive\MailchimpModule\Support;

use Illuminate\Support\Facades\Log;
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Support\Mailchimp;


class Harmony
{
    public static function prepareContact($email, $status = 'subscribed', $FNAME = '', $LNAME = '')
    {
        $contact = 
        [
            "email_address" => $email,
            "status" => $status,
            "merge_fields" => 
            [
                "FNAME" => $FNAME,
                "LNAME" => $LNAME
            ]
        ];

        return $contact;
    }
    public static function prepareContact2($email, $subscribe = true, $FNAME = '', $LNAME = '')
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


    /**
     * @todo , need the list-id/audience
     */
    public static function setSubscriberStatus($email, $subscribed = true)
    {
        if($subscriber = SubscriberModel::where('email',$email)->first())
        {
            $subscriber->subscribed              = $subscribed;
            return $subscriber;
        }

        return false;
    }

    /**
     * If the local system has a given subsriber by email+audience_list_id
     * Then return it.
     * @return mixed
     */
    public static function hasSubscriber($email,$audience)
    {
        if($subscriber = SubscriberModel::where('email',$email)->where('audience',$audience)->first())
        {
            return $subscriber;
        }

        return false;
    }

    public static function hasAudience($str_id)
    {
        if($a = AudienceModel::where('str_id',$str_id)->first())
        {
            return true;
        }

        return false;

    }

    public static function createFromMailchimp($list, $item)
    {
        $item->name                     = $list->name;
        $item->str_id                   = $list->id;
        $item->permission_reminder      = $list->permission_reminder;
        $item->email_type_option        = $list->email_type_option;
        $item->contact_company_name     = $list->contact->company;
        $item->contact_address1         = $list->contact->address1;
        $item->contact_state            = $list->contact->state;
        $item->contact_zip              = $list->contact->zip;
        $item->contact_country          = $list->contact->country;
        $item->contact_city             = $list->contact->city;
        $item->campaign_from_name       = $list->campaign_defaults->from_name;
        $item->campaign_from_email      = $list->campaign_defaults->from_email;
        $item->campaign_subject         = $list->campaign_defaults->subject;
        $item->campaign_language        = $list->campaign_defaults->language;

        return $item;

    }

    /**
     * $list - The Mailchimp Audience/List Item
     * $item - Repository Item from Stream
     */
    public static function mergeFromMailchimp($list, $item)
    {
        $item->name = $list->name;
        $item->permission_reminder      = $list->permission_reminder;
        $item->email_type_option        = $list->email_type_option;
        $item->contact_company_name     = $list->contact->company;
        $item->contact_address1         = $list->contact->address1;
        $item->contact_state            = $list->contact->state;
        $item->contact_city             = $list->contact->city;
        $item->contact_zip              = $list->contact->zip;
        $item->contact_country          = $list->contact->country;
        $item->campaign_from_name       = $list->campaign_defaults->from_name;
        $item->campaign_from_email      = $list->campaign_defaults->from_email;
        $item->campaign_subject         = $list->campaign_defaults->subject;
        $item->campaign_language        = $list->campaign_defaults->language;


        return $item;

    }

    public static function pushToMailchimp($list, $item)
    {

    }

    /**
     * Use for Save individual List/Audience
     * This will push the changes if the list-id matches
     */
    public static function updateOnMailchimp($item)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            $list_values = 
            [
                "name"                  => $item->name,
                "permission_reminder"   => $item->permission_reminder,
                "email_type_option"     => $item->email_type_option,
                "contact"           => 
                [
                    "company"           => $item->contact_company_name,
                    "address1"          => $item->contact_address1,
                    "city"              => $item->contact_city,
                    "state"             => $item->contact_state,
                    "zip"               => $item->contact_zip,
                    "country"           => $item->contact_country,
                ],
                "campaign_defaults" => 
                [
                    "from_name"         => $item->campaign_from_name,
                    "from_email"        => $item->campaign_from_email,
                    "subject"           => $item->campaign_subject,
                    "language"          => $item->campaign_language,
                ]
            ];

            if(!$mailchimp->hasList($item->str_id))
            {
                //dd($item->str_id, $list_values);
                // Find the Audience List on Mailchimp
                if($list = $mailchimp->createList($list_values))
                {
                    $item->update(['str_id' => $list->id]);
                    return true;
                }
            }
            else
            {
                //dd($item->str_id, $list_values);
                // Find the Audience List on Mailchimp
                return $mailchimp->updateList($item->str_id, $list_values);
            }

        }

        return false;

    }
    
}