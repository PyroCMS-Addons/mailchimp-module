<?php namespace Thrive\MailchimpModule\Http\Controller;

use Anomaly\Streams\Platform\Http\Controller\PublicController;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Thrive\MailchimpModule\Support\Integration\Campaign;
use Thrive\MailchimpModule\Support\Integration\Subscriber;


class WebhookController extends PublicController
{

    public function __construct(Container $container, Redirector $redirect)
    {

    }

    public function handle(Request $request)
    {
        Log::debug('[Entry] WebhookController  --------------------------------------------');
        Log::debug('');

        // $variable = \Request::input('type');

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {

            $type = $_POST['type'];
            // $id = $_POST['data']['id'];
            // $data = $_POST['data'];

            // Log::debug('Dump Data : ' . $type );
            // Log::debug('--------------');
            // Log::debug(print_r($id,true));
            // Log::debug(print_r($type,true));
            // Log::debug(print_r($data,true));


            //Check Source, if API, then block
            switch (strtolower($type))
            {
                case 'subscribe':
                    $this->subscribe();
                    break;
                case 'unsubscribe':
                    $this->unsubscribe();
                    break;
                case 'profile':
                    $this->profile();
                    break;
                case 'campaign':
                    $this->campaign();      
                    break;              
                case 'cleaned':
                    $this->cleaned();
                    Log::debug('cleaned Event Not Currently Supported : ');
                    break;
                case 'upemail':
                    $this->upemail();
                    Log::debug('upemail Event Not Currently Supported : ');
                    break;
                default:
                    Log::debug('Unknown response');
                    break;
            }

          }

          Log::debug('[Exit]  WebhookController  --------------------------------------------');

    }


    private function subscribe()
    {
        Log::debug('[Entry] WebhookController@subscribe -----------------------------------');

        if($web_id = $_POST['data']['web_id'])
        {
            Subscriber::SyncUserByWebId($web_id);
        }
        else
        {
            Log::debug('Unable to determin User WebID');
        }
    }

    private function unsubscribe()
    {
        Log::debug('[Entry] WebhookController@unsubscribe ---------------------------------');

        if($web_id = $_POST['data']['web_id'])
        {
            Subscriber::SyncUserByWebId($web_id);
        }
        else
        {
            Log::debug('Unable to determin User WebID');
        }
    }

    private function profile()
    {
        Log::debug('[Entry] WebhookController@profile ---------------------------------');

        // $type = $_POST['type'];
        // $id = $_POST['data']['id'];
        // $data = $_POST['data'];

        if($web_id = $_POST['data']['web_id'])
        {
            Subscriber::SyncUserByWebId($web_id);
        }
        else
        {
            Log::debug('Unable to determin User WebID');
        }


    }   
    
    
    
    private function campaign()
    {
        Log::debug('[Entry] WebhookController@campaign ---------------------------------');

        $campaign_id       = $_POST['data']['id'];
        // $status         = $_POST['data']['status'];
        // $reason         = $_POST['data']['reason'];
        // $list_id        = $_POST['data']['list_id'];
        // $subject        = $_POST['data']['subject'];

        // $type   = $_POST['type'];
        // $data   = $_POST['data'];

        // Log::debug(print_r($_POST,true));

        if($campaign_id)
        {
            Campaign::SyncById($campaign_id);
        }
        else
        {
            Log::debug('Unable to determin WebHook Action for Campaign,,No Valid ID');
        }  
    }

    /**
     * Unable to send to the user again, 
     * possible issue with invalid email
     * so mailchimp has 'cleaned' then
     */
    private function cleaned()
    {
        Log::debug('[Entry] WebhookController@cleaned ---------------------------------');
        Log::debug('');

        $data           = $_POST['data'];
        $reason         = $_POST['data']['reason'];
        $campaign_id    = $_POST['data']['campaign_id'];
        $list_id        = $_POST['data']['list_id'];
        $email          = $_POST['data']['email'];

        if(($_POST['data']['email']) && ($_POST['data']['list_id']))
        {
            Subscriber::CleanSubscriber( $email, $list_id );
        }
        else
        {
            Log::debug('Unable to determine Cleaned ID');
        }  
    }

    private function upemail()
    {
        Log::debug('[Entry] WebhookController@upemail ---------------------------------');
        Log::debug('           upemail Not currently Supported.');
        Log::debug('');

        $type   = $_POST['type'];
        //$id     = $_POST['data']['id'];
        $data   = $_POST['data'];

        Log::debug(print_r($data,true));

    }
    
}
