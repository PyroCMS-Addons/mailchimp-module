<?php namespace Thrive\MailchimpModule\Http\Requests;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Thrive\MailchimpModule\Http\Rules\TagsRule;
use Thrive\MailchimpModule\Http\Rules\ActionRule;
use Thrive\MailchimpModule\Http\Rules\AudienceRule;

class SubscribeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "subscriber_email"      => "required|email:rfc",
            "redirect_url"          => "required|string|max:200",
            "mc_tags"               => ['nullable', 'array',    new TagsRule],
            "action"                => ['required', 'string',   new ActionRule],
            'audience_id'           => ['required', 'string',   new AudienceRule],
        ];
    }    


    public function messages()
    {
        return [
            "subscriber_email.required"        => "The Email Address is required",
            "redirect_url.required"            => "You must have a redirect URL",
        ];
    }

}
