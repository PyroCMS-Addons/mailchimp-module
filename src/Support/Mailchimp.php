<?php namespace Thrive\MailchimpModule\Support;

// Laravel
use Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface;
use app;

// Anomaly
use Illuminate\Support\Facades\Log;

// Mailchimp
use MailchimpMarketing;
use MailchimpMarketing\ApiException;
//use MailchimpTransactional\ApiException;

// Thrive
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Support\Mailchimp\MailchimpAudiencesTrait;
use Thrive\MailchimpModule\Support\Mailchimp\MailchimpAutomationsTrait;
use Thrive\MailchimpModule\Support\Mailchimp\MailchimpCampaignTrait;
use Thrive\MailchimpModule\Support\Mailchimp\MailchimpContactsTrait;
use Thrive\MailchimpModule\Support\Mailchimp\MailchimpContentTrait;
use Thrive\MailchimpModule\Support\Mailchimp\MailchimpStatsTrait;

/**
 * Mailchimp
 *
 * Mailchimp is the Api Wrapper for PyroCMS.
 * This is the root concrete class.
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
 */
class Mailchimp
{

    const MEMBER_SUBSCRIBED     = 'subscribed';
    const MEMBER_UNSUBSCRIBED   = 'unsubscribed';

    //
    // Contacts Handling
    //
    use MailchimpContactsTrait;

    //
    // Audience/List Managemnet Trait
    //
    use MailchimpAudiencesTrait;

    //
    // Statistics Trait
    //
    use MailchimpStatsTrait;

    //
    // All things Campaigny
    //
    use MailchimpCampaignTrait;

    //
    // All things Automnouse
    //
    use MailchimpAutomationsTrait;

    //
    // Content
    //
    use MailchimpContentTrait;


    /**
     * The mailchimp client
     *
     * @var mixed
     */
    protected $mailchimp;



    /**
     * __construct
     *
     * Setup the Mailchimp Library Wrapper
     *
     * @return void
     */
    public function __construct()
    {
        $this->initMailChimpConnection();
    }



    /**
     * ping
     *
     * This is a simple function to test if the
     * Api connection is working. If the
     * Api-client pings, then
     * it connects!.
     *
     * Just as Connect(), ping() will check the env file
     * for credentials, if no credentials are present
     * then it checks the settings.
     *
     * @return void
     */
    public function ping()
    {
        // Set the default message.
        // A successfull connection will override this value.
        $message = 'Unable to Ping!';

        try
        {
            $settings = app(\Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface::class);

            $apikey = env('THRIVE_MAILCHIMP_API') ?? $settings->value('thrive.module.mailchimp::mailchimp_api_key','');
            $server = env('THRIVE_MAILCHIMP_SERVER_PREFIX') ?? $settings->value('thrive.module.mailchimp::mailchimp_server_prefix','');

            $this->mailchimp->setConfig([
                'apiKey' => $apikey,
                'server' => $server,
            ]);

            if($response = $this->mailchimp->ping->get())
            {
                if($response->health_status)
                {
                    $message = $response->health_status;
                }

                return $message;

            }
        }
        catch(\Exception $e)
        {
            Log::error($e->getMessage());
        }

        return false;

    }


    /**
     * Connect
     *
     * This does not implemnt singleton.
     * This will return a new instance.
     *
     * <code>
     *  $chimp = Mailchimp::Connect();
     *  $chimp->getDefaultList();
     * </code>
     * @return void
     */
    public static function Connect()
    {
        return app('Thrive\MailchimpModule\Support\Mailchimp');
        // return new self();
    }


    /**
     * initMailChimpConnection
     *
     * This is a simple function to test if the
     * Api connection is working. If the
     * Api-client pings, then
     * it connects!.
     *
     * This will check the env file for credentials,
     * if no credentials are present then it
     * checks the settings.
     *
     * @return void
     */
    protected function initMailChimpConnection()
    {
        $settings = app(\Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface::class);

        try
        {
            $this->mailchimp = new \MailchimpMarketing\ApiClient();

            $apikey = env('THRIVE_MAILCHIMP_API') ?? $settings->value('thrive.module.mailchimp::mailchimp_api_key','');
            $server = env('THRIVE_MAILCHIMP_SERVER_PREFIX') ?? $settings->value('thrive.module.mailchimp::mailchimp_server_prefix','');

            $this->mailchimp->setConfig([
                'apiKey' => $apikey,
                'server' => $server,
            ]);

            if($response = $this->mailchimp->ping->get()) {

                if(isset($response->health_status)) {
                    $message = $response->health_status;
                }

                return $message;
            }
        }
        catch(\Exception $e)
        {
            Log::critical('Unable to connect to Mailchimp, please check your API credentials');
        }

        return true;

    }

}