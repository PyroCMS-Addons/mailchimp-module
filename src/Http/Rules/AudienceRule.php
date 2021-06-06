<?php namespace Thrive\MailchimpModule\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Support\Integration\Audience;

class AudienceRule implements Rule
{

    public function passes($attribute, $value)
    {
        //dd($attribute,$value);
        // "audience_id"
        // "eb6f344742"
        if(Audience::LocalHasAudience($value))
        {
            return true;
        }
    }


    public function message()
    {
        return 'The :attribute must be uppercase.';
    }
}