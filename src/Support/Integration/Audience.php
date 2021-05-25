<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Facades\Log;

// Thrive
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Audience\Contract\AudienceInterface;

/**
 * Audience
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
class Audience
{


    /**
     * PrepareList
     *
     * This prepares the List parameter that will be updated
     * Pass in the Audience entry, and you will get a
     * fully prepared array ready in the correct
     * format for Mailchimp.
     *
     * @param  mixed $entry
     * @return void
     */
    public static function PrepareList(AudienceInterface $entry)
    {


    }


}