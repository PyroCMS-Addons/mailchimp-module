<?php namespace Thrive\MailchimpModule\Support\Mailchimp;

use GuzzleHttp\Exception\RequestException;

// Mailchimp
use Illuminate\Support\Facades\Log;
use MailchimpMarketing;
use MailchimpMarketing\ApiException;


/**
 * MailchimpWebhooksTrait
 *
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
trait MailchimpWebhooksTrait
{
    
    /**
     * getWebhook
     *
     * @param  mixed $list_id
     * @param  mixed $webhook_id
     * @return void
     */
    public function getWebhook($list_id, $webhook_id)
    {
        $status = true;

        try
        {
            $response = $this->mailchimp->lists->getListWebhook($list_id, $webhook_id);

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
     * getAllWebhooks
     *
     * @param  mixed $list_id
     * @return void
     */
    public function getAllWebhooks($list_id)
    {
        $status = true;

        try
        {
            $response = $this->mailchimp->lists->getListWebhooks($list_id);

            if(isset($response->webhooks))
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
     * updateWebhook
     *
     * @param  mixed $list_id
     * @param  mixed $webhook_id
     * @return void
     */
    public function updateWebhook($list_id, $webhook_id, array $values = [])
    {
        try
        {
            $response = $this->mailchimp->lists->updateListWebhook($list_id, $webhook_id, $values);

            if(isset($response->id))
                return true;
        }
        catch(RequestException $gex)
        {
            Log::error('!! RequestException Error Found');

            if ($gex->hasResponse())
            {
                $response = $gex->getResponse();
                $json = $gex->getResponse()->getBody()->getContents();
                $decoded_json = json_decode($json);

                Log::error($decoded_json->title);
                Log::error($decoded_json->detail);
                Log::error(print_r($decoded_json->errors,true));
            }
            else
            {
                Log::error('Error Updating Webhook.');
            }
        }        
        catch (\Exception $e)
        {
            Log::error('!! Exception Error Found');
            Log::error($e->getMessage());
        }

        return false;
    }    
    
    /**
     * addWebhook
     *
     * @param  mixed $list_id
     * @param  mixed $values
     * @return void
     */
    public function addWebhook($list_id, $values)
    {
        $status = true;

        try
        {
            $response = $this->mailchimp->lists->createListWebhook($list_id, $values);

            if(isset($response->id))
                return $response->id;
        }
        catch(RequestException $gex)
        {
            Log::error('!! RequestException Error Found');

            if ($gex->hasResponse())
            {
                $response = $gex->getResponse();
                $json = $gex->getResponse()->getBody()->getContents();
                $decoded_json = json_decode($json);

                Log::error($decoded_json->title);
                Log::error($decoded_json->detail);
                Log::error(print_r($decoded_json->errors,true));
            }
            else
            {
                Log::error('Error Updating Webhook.');
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
     * deleteWebhook
     *
     * @param  mixed $list_id
     * @param  mixed $webhook_id
     * @return void
     */
    public function deleteWebhook($list_id, $webhook_id)
    {
        try
        {
            $this->mailchimp->lists->deleteListWebhook($list_id, $webhook_id);

            return true;
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
        }

        return false;
    }  
}