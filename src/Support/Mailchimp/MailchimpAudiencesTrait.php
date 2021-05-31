<?php namespace Thrive\MailchimpModule\Support\Mailchimp;

// Laravel
// use GuzzleHttp\Exception\ClientException;

// Mailchimp
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use MailchimpMarketing;
use MailchimpMarketing\ApiException;


/**
 * MailchimpAudiencesTrait
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
trait MailchimpAudiencesTrait
{

    /**
     * createList
     *
     * Create a new list in your Mailchimp account.
     *
     * @param  string           $list_id
     * @return boolean
     *
     * PrepareList(..) can prepare the correct values for $list_values.
     * @see Thrive\MailchimpModule\Support\Integartion\Audience::PrepareList(AudienceInterface $entry)
     *
     * @see https://mailchimp.com/developer/marketing/api/lists/delete-list/
     *
     */
    public function createList($list_values = [])
    {
        $response = null;
        $status = true;

        try
        {
            $response = $this->mailchimp->lists->createList($list_values);
        }
        catch (ApiException $ex)
        {
            $status = false;

            Log::error('MailchimpMarketing\ApiException ex, see below export of response.');
            Log::error(print_r($ex,true));
        }
        catch(RequestException $gex)
        {
            $status = false;

            if ($gex->hasResponse())
            {
                $response = $gex->getResponse();
                $json = $gex->getResponse()->getBody()->getContents();
                $decoded_json = json_decode($json);

                Log::error($decoded_json->title);
                Log::error($decoded_json->detail);
            }
            else
            {
                Log::error('Error Creating Audience, possible restriction applied.');
            }

        }
        catch(\Exception $e)
        {
            $status = false;

            $json = $e->getResponse()->getBody()->getContents();
            Log::error('Create Audience Error : '.print_r($json,true));
        }
        finally
        {
            if($status)
                return $response;
        }

        return false;
    }

    /**
     * updateList
     *
     * Update the settings for a specific list.
     *
     *
     * @param  string           $list_id
     * @param  array            $list_values
     * @return object|false
     *
     * PrepareList(..) can prepare the correct values for $list_values.
     * @see Thrive\MailchimpModule\Support\Integartion\Audience::PrepareList(AudienceInterface $entry)
     *
     * @see https://mailchimp.com/developer/marketing/api/lists/update-lists/
     *
     */
    public function updateList(string $list_id, array $list_values)
    {
        $response = null;

        $status = true;

        try
        {
            $response = $this->mailchimp->lists->updateList($list_id, $list_values);
        }
        catch (\Exception $e)
        {
            $status = false;
        }
        finally
        {
            if($status)
                return $response;
        }

        return false;
    }


    /**
     * deleteList
     *
     * Delete a list from your Mailchimp account. If you delete a list,
     * you'll lose the list history—including subscriber activity,
     * unsubscribes, complaints, and bounces. You’ll also lose
     * subscribers’ email addresses, unless you exported
     * and backed up your list.
     *
     * @param  string           $list_id
     * @return boolean
     *
     * @see https://mailchimp.com/developer/marketing/api/lists/delete-list/
     *
     */
    public function deleteList(string $list_id)
    {
        try
        {
            if($response = $this->mailchimp->lists->deleteList($list_id))
            {
                return true;
            }
        }
        catch (ApiException $ex)
        {
            Log::error('MailchimpMarketing\ApiException ex, see below export of response.');
            Log::error(print_r($ex,true));
        }
        catch(RequestException $gex)
        {
            if ($gex->hasResponse())
            {
                $response = $gex->getResponse();
                $json = $gex->getResponse()->getBody()->getContents();
                $decoded_json = json_decode($json);

                Log::error($decoded_json->title);
                Log::error($decoded_json->detail);
            }
            else
            {
                Log::error('Error Deleting Audience,  possible restriction applied.');
            }

        }
        catch(\Exception $e)
        {
            $json = $e->getResponse()->getBody()->getContents();
            Log::error('Delete Audience Error : '.print_r($json,true));
        }

        return false;
    }


    /**
     * hasList
     *
     * Does Mailchimp have a given list ?.
     *
     * @param  string           $list_id
     * @return boolean
     *
     *
     */
    public function hasList($list_id)
    {
        $status = true;

        try
        {
            if($response = $this->mailchimp->lists->getList($list_id))
            {
                return true;
            }
        }
        catch (\Exception $e)
        {
            $status = false;
        }

        return false;
    }

    /**
     * getList
     *
     * Get information about a specific list in your
     * Mailchimp account. Results include list
     * members who have signed up but haven't
     * confirmed their subscription yet and
     * unsubscribed or cleaned.
     *
     *
     * @param  string           $list_id
     * @return object
     *
     * @see https://mailchimp.com/developer/marketing/api/lists/get-list-info/
     *
     */
    public function getList($list_id)
    {
        $status = true;

        try
        {
            if($response = $this->mailchimp->lists->getList($list_id))
            {
                return $response;
            }
        }
        catch (\Exception $e)
        {
            $status = false;
        }
        finally
        {
            if($status)
                return $response;
        }

        return false;
    }


    /**
     * Returns the first list.
     * Useful for free/basic accounts that are
     * limited by a single list.
     */
    public function getDefaultList()
    {
        if($response = $this->mailchimp->lists->getAllLists())
        {
            if(count($response->lists))
                return $response->lists[0];
        }

        return false;
    }

    /**
     * getAllLists
     *
     * Get information about all lists in the account.

     * @return object
     *
     * @see https://mailchimp.com/developer/marketing/api/lists/get-lists-info/
     *
     */
    public function getAllLists()
    {
        $response = $this->mailchimp->lists->getAllLists();

        return $response->lists;
    }

}