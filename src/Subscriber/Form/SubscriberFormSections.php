<?php namespace Thrive\MailchimpModule\Subscriber\Form;

use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormBuilder;


/**
 * Class SubscriberFormSections
 *
 * @author Sam McDonald. <s.mcdonald@outlook.com.au>
 */
class SubscriberFormSections
{

    /**
     * handle
     *
     * @param  mixed $builder
     * @return void
     */
    public function handle(SubscriberFormBuilder $builder)
    {
        $stream = $builder->getForm();

        //
        // No sections are to be used for public
        //
        $sections = [];

        //
        // If Admin/edit, we need to show all sections
        //
        if($stream->getMode() == "edit")
        {
            $sections = [
                'metafield'   => [
                    'stacked' => false,
                    'tabs' => [
                        'general' => [
                            'title'  => 'thrive.module.mailchimp::tabs.subscriber_status',
                            'fields' => [
                                'subscriber_email',
                                'subscriber_subscribed',
                            ],
                        ],
                        'name' => [
                            'title'  => 'thrive.module.mailchimp::tabs.subscriber_name',
                            'fields' => [
                                'subscriber_fname',
                                'subscriber_lname',
                            ],
                        ],
                        'options' => [
                            'title'  => 'thrive.module.mailchimp::tabs.subscriber_audience_id',
                            'fields' => [
                                'subscriber_audience_id',
                                'subscriber_audience_name',
                            ],
                        ],
                    ],
                ],
            ];
        }

        $builder->setSections( $sections );
    }
}
