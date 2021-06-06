<?php namespace Thrive\MailchimpModule\Support\Mailchimp;

use GuzzleHttp\Exception\RequestException;

// Mailchimp
use Illuminate\Support\Facades\Log;
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
    public function getMembers($list_id, $fields = null, $exclude_fields = null, $count = '10', $offset = '0')
    {
        try
        {
            $lists = $this->mailchimp->lists->getListMembersInfo( $list_id, $fields, $exclude_fields, $count, $offset );

            if(isset($lists->members))
            {
                return $lists;
            }
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
        }

        return false;
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
        Log::debug('List ID : ' . $list_id);
        Log::debug('Email : ' . $email);

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


    public function addContactTags($list_id, $email, $tags = [])
    {
		Log::debug('--- [ Begin ] ---  Mailchimp::addContactToList() ');

        $subscriberHash = $this->getEmailHash($email);

        //tags status = inactive| active

        try
        {
            // $response = $client->lists->updateListMemberTags("list_id", "subscriber_hash", [
            //     "tags" => [["name" => "name", "status" => "active"]],
            // ]);

            $response = $this->mailchimp->lists->updateListMemberTags(
                $list_id,
                $subscriberHash,
                $tags,
                //"tags" => [["name" => "name", "status" => "active"]],
            );

            return $response;
        }
        catch(RequestException $gex)
        {
            Log::error('!! RequestException Error Found');

            if ($gex->hasResponse())
            {
                //$response = $gex->getResponse();
                $json = $gex->getResponse()->getBody()->getContents();
                $decoded_json = json_decode($json);

                Log::error($decoded_json->title);
                Log::error($decoded_json->detail);
                Log::error(print_r($decoded_json->errors,true));
            }
            else
            {
                Log::error('Error Updating Subscriber.');
            }
        }   
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
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
		Log::debug('--- [ Begin ] ---  Mailchimp::addContactToList() ');


        $contact["tags"]              = $tags;

        try
        {
            $response = $this->mailchimp->lists->addListMember(
                $list_id,
                $contact,
            );

            return $response;
        }
        catch(RequestException $gex)
        {
            Log::error('!! RequestException Error Found');

            if ($gex->hasResponse())
            {
                //$response = $gex->getResponse();
                $json = $gex->getResponse()->getBody()->getContents();
                $decoded_json = json_decode($json);

                Log::error($decoded_json->title);
                Log::error($decoded_json->detail);
                Log::error(print_r($decoded_json->errors,true));
            }
            else
            {
                Log::error('Error Updating Subscriber.');
            }
        }   
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
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
		Log::debug('--- [ Begin ] ---  Mailchimp::setListMember() ');


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
                    "status"            => ($subscribed) ? "subscribed" : "unsubscribed",
                ]
            );
        }
        catch(RequestException $gex)
        {
            Log::error('!! RequestException Error Found');

            if ($gex->hasResponse())
            {
                //$response = $gex->getResponse();
                $json = $gex->getResponse()->getBody()->getContents();
                $decoded_json = json_decode($json);

                Log::error($decoded_json->title);
                Log::error($decoded_json->detail);
                Log::error(print_r($decoded_json->errors,true));
            }
            else
            {
                Log::error('Error adding Subscriber.');
            }
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
		Log::debug('--- [ Begin ] ---  Mailchimp::setListMemberWithMergeFields() ');


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
                    "merge_fields"      => $merg_fields,
                    // "merge_fields"      => [
                    //     isset($fname) ?  "FNAME"         => $fname,
                    //     isset($lname) ?  "LNAME"         => $lname,
                    // ],
                ]
            );

            return $response;
        }
        catch(RequestException $gex)
        {
            Log::error('!! setListMemberWithMergeFields()->RequestException Error Found');

            if ($gex->hasResponse())
            {
                //$response = $gex->getResponse();
                $json = $gex->getResponse()->getBody()->getContents();
                $decoded_json = json_decode($json);

                Log::error($decoded_json->title);
                Log::error($decoded_json->detail);
                Log::error(print_r($decoded_json->errors,true));
            }
            else
            {
                Log::error('Error Updating Subscriber.');
            }
        }         
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
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