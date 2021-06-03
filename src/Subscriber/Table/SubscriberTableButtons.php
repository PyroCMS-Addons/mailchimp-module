<?php namespace Thrive\MailchimpModule\Subscriber\Table;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Subscriber\Table\SubscriberTableBuilder;

class SubscriberTableButtons extends TableBuilder
{    
    /**
     * handle
     *
     * @param  mixed $builder
     * @return void
     */
    public function handle(SubscriberTableBuilder $builder)
    {
        $builder->setButtons([
            'edit' =>
            [
                'type' => 'success',
                'disabled'    => function (EntryInterface $entry) {
                    if ($entry->subscriber_status == 'cleaned') {
                        return true;
                    }                
                    return false;
                },
            ],
                        
        ]);

    }
}
