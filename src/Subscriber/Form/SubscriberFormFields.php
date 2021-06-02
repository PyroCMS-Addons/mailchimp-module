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
            'subscriber_email',
        ];

        $stream = $builder->getForm();


        if($stream->getMode() == "edit")
        {
            // Ensure al lfields are displayed for
            // for admin viewing.
            $fields =
            [
                '*',
                'subscriber_subscribed',
                'subscriber_fname',
                'subscriber_lname',
                'subscriber_audience_id' => [
                    'disabled' => 'edit',
                ],
                'subscriber_audience_name' => [
                    'disabled' => 'edit',
                ],
                'subscriber_email' => [
                    'disabled' => 'edit',
                ],
                'status_remote_timestamp' => [
                    'disabled' => 'edit',
                ],
                'local_timestamp_sync' => [
                    'disabled' => 'edit',
                ],
                'local_timestamp_save' => [
                    'disabled' => 'edit',
                ],          
                'status_sync_messages' => [
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
                'subscriber_email',
                'subscriber_audience_id',
                'subscriber_subscribed',
                'subscriber_fname',
                'subscriber_lname',
            ];
        }

        $builder->setFields(
            $fields
        );

    }
}
