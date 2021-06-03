<?php namespace Thrive\MailchimpModule\Http\Controller;

use Anomaly\Streams\Platform\Http\Controller\PublicController;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Thrive\MailchimpModule\Support\Integration\Subscriber;


class WebhookController extends PublicController
{

    public function __construct(Container $container, Redirector $redirect)
    {

    }

    public function handle(Request $request)
    {
        Log::debug('Handling Inbound Response');

        // $variable = \Request::input('type');

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $type = $_POST['type'];
            $id = $_POST['data']['id'];
            $data = $_POST['data'];

            //Check Source, if API, then block

            switch (strtolower($type))
            {
                case 'subscribe':
                    Log::debug('Request Subscribe of User : ' . $id);
                    $this->subscribe($id);
                    break;
                case 'unsubscribe':
                    Log::debug('Request Un-Subscribe of User : ' . $id);
                    $this->unsubscribe($id);
                    break;
                case 'profile':
                    Log::debug('profile Event Not Currently Supported : ' . $id);
                    break;
                case 'cleaned':
                    Log::debug('cleaned Event Not Currently Supported : ' . $id);
                    break;
                case 'upemail':
                    Log::debug('upemail Event Not Currently Supported : ' . $id);
                    break;
                case 'campaign':
                    Log::debug('campaign Event Not Currently Supported : ' . $id);
                    break;
                default:
                    Log::debug('Unknown response');
                    break;
            }


            switch (strtolower($type))
            {
                case 'profile':
                case 'cleaned':
                case 'upemail':
                case 'campaign':
                    Log::debug('Dump Data : ' . $type );
                    Log::debug('--------------');
                    Log::debug(print_r($id,true));
                    Log::debug(print_r($type,true));
                    Log::debug(print_r($data,true));
                    break;
                default:
                    Log::debug('');
                    break;
            }

          }

    }


    private function subscribe($id)
    {
        Subscriber::WebhookSubscribe($id);
    }

    private function unsubscribe($id)
    {
        Subscriber::WebhookUnSubscribe($id);
    }
}
