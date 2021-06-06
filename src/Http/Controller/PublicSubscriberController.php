<?php namespace Thrive\MailchimpModule\Http\Controller;

// Laravel
use Anomaly\Streams\Platform\Http\Controller\PublicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Anomaly
use Illuminate\Support\Str;
use Thrive\MailchimpModule\Http\Requests\SubscribeRequest;
use Thrive\MailchimpModule\Support\Integration\Subscriber;

// Thrive
class PublicSubscriberController extends PublicController
{
    public function handle(SubscribeRequest $request)
    {
        Log::debug('--- [ Begin ] ---  PublicSubscriberController::handle ');

        if($validated = $request->validated()) {
            
            Log::debug('  » 00 validation          : Success');

            if($subscriber = Subscriber::CreateOrUpdateSubscriebrFromRequest($request))
            {
                if(Subscriber::AddOrUpdateSubscriberToRemote($subscriber))
                {
                    $redirect_url = $request->input('redirect_url');
                    if($redirect_url != 'thrive.module.mailchimp.redirect.back')
                    {
                        Log::debug('  » 00 RedirectURL         : '. $redirect_url);

                        return redirect($redirect_url);
                    }

                    Log::debug('  » 00 RedirectURL         : Return Back');

                }
            }
        } 
        else 
        {
            Log::debug('  » 00 validation          : Fail');
        }

        return redirect()->back();
    }
}