<?php namespace Thrive\MailchimpModule\Subscriber\Table;

// use Anomaly\Streams\Platform\Entry\EntryModel;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\Table\SubscriberTableBuilder;


/**
 * Class SubscriberTableColumns
 *
 */
class SubscriberTableColumns
{

    /**
     * Handle the columns.
     *
     * @param SubscriberTableBuilder $builder
     */
    public function handle(SubscriberTableBuilder $builder)
    {
        $builder->setColumns( 
            [
                'subscriber_email',
                'subscriber_fname',    
                'subscriber_lname',    
                'subscriber_status' => [
                    'wrapper' => function (SubscriberInterface $entry) {
                        if($entry->subscriber_status == 'subscribed') {
                            return "<span class='tag tag-info'>".ucfirst($entry->subscriber_status)."</span>";
                        }
                        if($entry->subscriber_status == 'unsubscribed') {
                            return "<span class='tag tag-default'>".ucfirst($entry->subscriber_status)."</span>";
                        }      

                        return "<span class='small text-danger'>".ucfirst($entry->subscriber_status)."</span>";
                    },
                ],  
            ]
        );
    }
}
