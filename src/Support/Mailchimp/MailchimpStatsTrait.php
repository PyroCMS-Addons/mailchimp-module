<?php namespace Thrive\MailchimpModule\Support\Mailchimp;


use MailchimpMarketing;
use Illuminate\Support\Facades\Log;
use MailchimpMarketing\ApiException;


/**
 * MailchimpStatsTrait
 *
 * Gets Stats from the Mailchimp api
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
trait MailchimpStatsTrait
{

    /**
     *
     */
    public function getStats($list_id)
    {
        $response = null;
        $status = true;

        try
        {
            $response = $this->mailchimp->lists->getListRecentActivity($list_id);
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

}