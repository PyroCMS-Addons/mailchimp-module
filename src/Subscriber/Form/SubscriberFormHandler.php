<?php namespace Thrive\MailchimpModule\Subscriber\Form;

// Laravel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Thrive
use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormBuilder;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Support\Integration\Audience;
use Thrive\MailchimpModule\Support\Integration\Subscriber;
use Thrive\MailchimpModule\Support\Mailchimp;

/**
 * Class SubscriberFormHandler
 *
 * @author Sam McDonald. <s.mcdonald@outlook.com.au>
 */
class SubscriberFormHandler
{

    /**
     * Handle the form.
     *
     * @param FormBuilder $builder
     */
    public function handle(SubscriberFormBuilder $builder, Request $request)
    {

        Log::debug('--- [ Begin ] ---  SubscriberFormHandler::handle ');

        $stream = $builder->getForm();

        // Is Admin Create
        if( $stream->getMode() == "create" || $stream->getMode() == "edit" )
        {
            return $this->adminHandle( $stream, $builder,  $request );
        }
        else
        {
            return $this->publicHandle( $stream, $builder,  $request );
        }

        return redirect()->back();

    }

    public function adminHandle($stream, SubscriberFormBuilder $builder, Request $request)
    {
        // If Admin/edit, we need to show all sections

        // Mode
        $create_or_edit = $stream->getMode();

        if( $builder->canSave() )
        {
            Log::debug('  » 00 Admin Handle        : ');
            Log::debug('  » 00 Admin Mode          : ' . $create_or_edit );

            return $builder->saveForm();
        }

        return redirect()->back();
    }


    public function publicHandle($stream, SubscriberFormBuilder $builder, Request $request)
    {
        //$create_or_edit = $stream->getMode();

        Log::debug('  » 00 Public Handle       : ');
        Log::debug('  » 00 Admin Mode          : Public'); //. $create_or_edit );

        $email              = $request->input('subscriber_email');
        $strid              = $request->input('strid');
        
        //new version
        //$strid2             = $request->input('campaign_id');
        
        $action             = $request->input('action');
        $action_bool        = ($action == 'subscribe') ? true : false ;
        $subscribe_flag     = ($action_bool) ? 'subscribed' : 'unsubscribed' ;
        $tags               = $request->input('mc_tags');

        if($this->testUserInput( $email, $strid, $action, $subscribe_flag, $tags ))
        {
            // Step 1: Update local Value
            $this->addUpdateLocalValue($strid, $email, $action_bool, $tags);

            // Step 2: Update Remote Value
            $this->pushToMailchimp($strid, $email, $action_bool, $tags);
        }

        return redirect()->back();
    }


    /**
     * Step 1: Add or Update the local Value
     * @param $strid            - @required     - The Str ID of the Audience
     * @param $email_adddress   - @required     - Email address of user
     * @param $subscribe        - @required     - Should we subscribe or unsubscribe
     */
    private function addUpdateLocalValue( $strid, $email_address, $subscribe, $tags = null)
    {

        Log::debug('  » 02 Local Entry         : BEGIN ');

        $action = 'Local not found, will CREATE';

        if(Subscriber::IsSubscriberLocallyRecorded($email_address, $strid))
        {
            $action = 'Has Local, so will UPDATE';
        }

        Log::debug('        » Executing Action : ' . $action);

        Subscriber::CreateOrUpdateLocalSubscriber( $email_address, $strid, $subscribe );

        return true;

    }


    /**
     * Step 2: add or Update the remote / MailChimp value
     */
    private function pushToMailchimp( $strid, $email_adddress, $subscribe, $tags = null)
    {
        Log::debug('  » 03 Remote Entry        : BEGIN ');

        if($mailchimp = Mailchimp::Connect())
        {
            // Step 1
            if($contact = $mailchimp->checkContactStatus($strid, $email_adddress))
            {
                if(isset($contact->status))
                {
                    Log::debug('        » Has Remote       : YES');
                    Log::debug('        » Remote Status    : '. $contact->status);
                    Log::debug('        » New Status       : '. (($subscribe)?'subscribed':'unsubscribed') );

                    if($mailchimp->setListMember($strid, $email_adddress, $subscribe))
                    {
                        Log::debug('        » Remote Updated   : YES');
                    }
                    else
                    {
                        Log::debug('        » Remote Updated   : NO');
                    }
                }
                else
                {
                    //unexpected error
                    Log::debug('        » Unexpected Error : 3.2');
                }
            }
            else
            {
                $contact = Subscriber::FormatContact($email_adddress, $subscribe );

                if($mailchimp->addContactToList($strid, $contact, $tags))
                {
                    Log::debug('        » Push Status      : Added Success');
                }
                else
                {
                    Log::debug('        » Push Status      : Add Error' );
                    Log::debug('        » Contact Variable : ');
                    Log::debug( print_r($contact,true) );
                }
            }
        }
    }


    /**
     * testUserInput
     *
     * @param  mixed $email
     * @param  mixed $strid
     * @param  mixed $action
     * @param  mixed $subscribe_flag
     * @return void
     */
    private function testUserInput( $email, $strid, $action, $subscribe_flag, $tags = null )
    {
        // dd($builder);
        Log::debug('  » 01 Request Tests       : BEGIN   ');

        // Check to see if the email is valid
        $pass_email = (filter_var($email, FILTER_VALIDATE_EMAIL))? true : false ;

        Log::debug('        » Email            : '. $email);
        Log::debug('        » Pass Email       : '. $pass_email);

        // Check to see if the strid is valid
        $pass_strid = Audience::LocalHasAudience($strid);
        Log::debug('        » StrId            : '. $strid);
        Log::debug('        » Pass StrId       : '. $pass_strid);


        // Check to see if the action is valid
        $pass_action = (($action == 'subscribe') || ($action == 'unsubscribe')) ?? false;
        Log::debug('        » Action           : '. $action);
        Log::debug('        » Pass Action      : '. $pass_action);


        // Check to see if the action is valid
        $pass_subflag = (($subscribe_flag == 'subscribed') || ($subscribe_flag == 'unsubscribed')) ?? false;
        Log::debug('        » Sub Flag         : '. $subscribe_flag);
        Log::debug('        » Pass Sub Flag    : '. $pass_subflag);


        // Check to see if we have any tags
        $tags_found = '';
        if($tags && is_array($tags))
        {
            foreach($tags as $tag)
            {
                $tags_found .= $tag . ' | ';
            }
        }
        Log::debug('        » Tags Is Array    : ' . $tags_found);


        if($pass_email && $pass_strid && $pass_action && $pass_subflag)
        {
            Log::debug('        » All Test         : Complete Success');
            return true;
        }

        return false;
    }

}
