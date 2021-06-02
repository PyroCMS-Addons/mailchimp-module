<?php namespace Thrive\MailchimpModule\Support\Mailchimp;

use Illuminate\Support\Facades\Log;

// Mailchimp
use MailchimpMarketing;
use MailchimpMarketing\ApiException;


/**
 * MailchimpContactsTrait
 *
 * Handles all Contacts/Subscribers functionality
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
trait MailchimpContactsTrait
{

    /**
     * getMembers
     *
     * @param  mixed $list_id
     * @return void
     */
    public function getMembers($list_id,
                                $fields         = null,
                                $exclude_fields = null,
                                $count          = '10',
                                $offset         = '0',
                                $email_type     = null,
                                $status         = null,
                                $since_timestamp_opt = null,
                                $before_timestamp_opt = null,
                                $since_last_changed = null,
                                $before_last_changed = null,
                                $unique_email_id = null,
                                $vip_only       = null,
                                $interest_category_id = null,
                                $interest_ids   = null,
                                $interest_match = null,
                                $sort_field     = null,
                                $sort_dir       = null,
                                $since_last_campaign = null,
                                $unsubscribed_since = null)
    {
        //assume good from start
        $status = true;
        $response = false;

        try
        {
            $response = $this->mailchimp->lists->getListMembersInfo( $list_id, $fields, $exclude_fields, $count, $offset );
            // $response = $this->mailchimp->lists->getListMembersInfo( $list_id, $fields, $exclude_fields, $count, $offset, $email_type, $status, $since_timestamp_opt, $before_timestamp_opt, $since_last_changed , $before_last_changed , $unique_email_id, $vip_only, $interest_category_id, $interest_ids, $interest_match, $sort_field, $sort_dir, $since_last_campaign, $unsubscribed_since );
        }
        catch (\Exception $e)
        {
            $status = false;
            Log::error($e->getMessage());
        }

        //need to rework status return
        return $response;
    }

    /**
     * checkContactStatus
     *
     * @param  mixed $list_id
     * @param  mixed $email
     * @return void
     */
    public function checkContactStatus($list_id, $email)
    {
        $found = false;
        $subscriber_hash   = $this->getEmailHash($email);
        $response = '';

        try
        {
            if($response = $this->mailchimp->lists->getListMember($list_id, $subscriber_hash))
            {
                $found = true;
            }
        }
        catch (\Exception $e)
        {
            Log::error('Unable to Check User Status or User does not Exist');
            // echo $e->getMessage();
        }

        if($found)
        {
            return $response;
        }

        return false;

    }

    /**
     * resubscribeContactListmember
     *
     * @param  mixed $list_id
     * @param  mixed $email
     * @return void
     */
    public function resubscribeContactListmember($list_id, $email)
    {
        $status = true;

        $subscriberHash = $this->getEmailHash($email);

        try
        {
            $response = $this->mailchimp->lists->updateListMember(
                $list_id,
                $subscriberHash,
                ["status" => "subscribed"]
            );
        }
        catch (\Exception $e)
        {
            $status = false;
            Log::error($e->getMessage());
        }

        if($status) {
            return $response;
        }

        return false;
    }

    /**
     * unsubscribeContactFromList
     *
     * @param  mixed $list_id
     * @param  mixed $email
     * @return void
     */
    public function unsubscribeContactFromList($list_id, $email)
    {
        $status = true;

        $subscriberHash = $this->getEmailHash($email);

        try
        {
            $response = $this->mailchimp->lists->updateListMember(
                $list_id,
                $subscriberHash,
                ["status" => "unsubscribed"]
            );
        }
        catch (\Exception $e)
        {
            $status = false;
            Log::error($e->getMessage());
        }

        if($status) {
            return $response;
        }

        return false;
    }


    
    /**
     * addContactToList
     *
     * @param  mixed $list_id
     * @param  mixed $contact
     * @param  mixed $tags
     * @return void
     */
    public function addContactToList($list_id, $contact, $tags = [])
    {
        $status = true;

        $contact["tags"]              = $tags;

        try
        {
            $response = $this->mailchimp->lists->addListMember(
                $list_id,
                $contact,
            );
        }
        catch (\Exception $e)
        {
            $status = false;
            //echo $e->getMessage();
        }

        if($status) {
            return $response;
        }

        return false;
    }

    
    /**
     * getListMember
     *
     * @param  mixed $list_id
     * @param  mixed $email
     * @return void
     */
    public function getListMember($list_id, $email)
    {
        $status = true;

        $subscriberHash = $this->getEmailHash($email);

        try
        {
            $response = $this->mailchimp->lists->getListMember($list_id, $subscriberHash);

            if(isset($response->id))
                return $response;
        }
        catch (\Exception $e)
        {
            $status = false;
            Log::error($e->getMessage());
        }

        if($status) {
            return $response;
        }

        return false;
    }


    /**
     * setListMember
     *
     * @param  mixed $list_id
     * @param  mixed $email
     * @param  mixed $subscribed
     * @return void
     */
    public function setListMember($list_id, $email, $subscribed = true)
    {
        $status = true;

        $subscriberHash = $this->getEmailHash($email);

        try
        {
            $response = $this->mailchimp->lists->setListMember(
                $list_id,
                $subscriberHash,
                [
                    "status_if_new"     => ($subscribed) ? self::MEMBER_SUBSCRIBED : self::MEMBER_UNSUBSCRIBED,
                    "status"            => ($subscribed) ? self::MEMBER_SUBSCRIBED : self::MEMBER_UNSUBSCRIBED,
                    // "status"            => ($subscribed) ? "subscribed" : "unsubscribed",
                ]
            );
        }
        catch (\Exception $e)
        {
            $status = false;
            Log::error($e->getMessage());
        }

        if($status) {
            return $response;
        }

        return false;
    }


    /**
     * setListMemberWithMergeFields
     *
     * @param  mixed $list_id
     * @param  mixed $email
     * @param  mixed $subscribed
     * @param  mixed $fname
     * @param  mixed $lname
     * @return void
     */
    public function setListMemberWithMergeFields($list_id, $email, $subscribed = true, $fname = null, $lname = null)
    {
        $status = true;

        $subscriberHash = $this->getEmailHash($email);

        $merg_fields  = [];

        if(isset($fname))
            $merg_fields["FNAME"]  = $fname;

        if(isset($lname))
            $merg_fields["LNAME"]  = $lname;

        try
        {
            $response = $this->mailchimp->lists->setListMember(
                $list_id,
                $subscriberHash,
                [
                    "status_if_new"     => ($subscribed) ? self::MEMBER_SUBSCRIBED : self::MEMBER_UNSUBSCRIBED,
                    "status"            => ($subscribed) ? self::MEMBER_SUBSCRIBED : self::MEMBER_UNSUBSCRIBED,
                    "status"            => ($subscribed) ? "subscribed" : "unsubscribed",
                    "merge_fields"      => $merg_fields,
                    // "merge_fields"      => [
                    //     isset($fname) ?  "FNAME"         => $fname,
                    //     isset($lname) ?  "LNAME"         => $lname,
                    // ],
                ]
            );
        }
        catch (\Exception $e)
        {
            $status = false;
            Log::error($e->getMessage());
        }

        if($status) {
            return $response;
        }

        return false;
    }


    /**
     * getEmailHash
     *
     * @param  mixed $email
     * @return void
     */
    public function getEmailHash(string $email)
    {
        return md5(strtolower($email));
    }

}