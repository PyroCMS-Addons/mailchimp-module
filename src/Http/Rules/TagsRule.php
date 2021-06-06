<?php namespace Thrive\MailchimpModule\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Support\Integration\Audience;

class TagsRule implements Rule
{

    public function passes($attribute, $value)
    {
        //dd($attribute,$value);

        // "mc_tags"
        // array:5 [▼
        //   0 => "2021"
        //   1 => "Home Page"
        //   2 => "Newsletter"
        //   3 => "2021"
        //   4 => "Jun"
        // ]
        $status = true;

        foreach($value as $tag) {
            if(!is_string($tag) && strlen($tag) > 0) {
                $status = false;
            }
        }

        return $status;

    }


    public function message()
    {
        return 'Tags must be strings and longer than 0 characters.';
    }
}