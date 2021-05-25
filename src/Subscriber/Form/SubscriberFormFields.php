<?php namespace Thrive\MailchimpModule\Subscriber\Form;

use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormBuilder;

/**
 * Class SubscriberFormFields
 *
 * @author Sam McDonald. <s.mcdonald@outlook.com.au>
 */
class SubscriberFormFields
{

    public function handle(SubscriberFormBuilder $builder)
    {
        // Default fields for public
        // viewing & subscribing..
        $fields =
        [
            'email',
        ];

        $stream = $builder->getForm();


        if($stream->getMode() == "edit")
        {
            // Ensure al lfields are displayed for
            // for admin viewing.
            $fields =
            [
                '*',
                'subscribed',
                'fname',
                'lname',
                'audience' => [
                    'disabled' => 'edit',
                ],
                'audience_name' => [
                    'disabled' => 'edit',
                ],
                'email' => [
                    'disabled' => 'edit',
                ],
            ];
        }

        // before working on this,
        // need to check how it will affect
        // {{ forms(mailchimp) }}
        if($stream->getMode() == "create")
        {
            // Ensure al lfields are displayed for
            // for admin viewing.
            $fields =
            [
                'email',
                'audience',
                'subscribed',
                'fname',
                'lname',
            ];
        }


        $builder->setFields(
            $fields
        );

    }
}
