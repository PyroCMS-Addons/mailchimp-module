<?php namespace Thrive\MailchimpModule\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Support\Integration\Audience;

class ActionRule implements Rule
{

    public function passes($attribute, $value)
    {
        //dd($attribute,$value);
        // "action"
        // "subscribe|unsubscribe|"
        if(is_string($value))
        {
            switch($value)
            {
                case 'subscribe':
                case 'unsubscribe':
                    $status = true;
                    break;
                default: 
                    $status = false;
                    break;
            }
        }

        return $status;
    }


    public function message()
    {
        return 'Invalid Action. Please select Unsubscribe or Subscribe.';
    }
}