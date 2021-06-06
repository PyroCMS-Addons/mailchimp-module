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
    public function handle(SubscriberFormBuilder $builder)
    {
        Log::debug('--- [ Begin ] ---  SubscriberFormHandler::handle ');

        // $stream = $builder->getForm();
        // Is Admin Create
        // if( $stream->getMode() == "create" || $stream->getMode() == "edit" )
        // $create_or_edit = $stream->getMode();

        if($builder->canSave())
        {
            return $builder->saveForm();
        }

        return redirect()->back();
    }

}
