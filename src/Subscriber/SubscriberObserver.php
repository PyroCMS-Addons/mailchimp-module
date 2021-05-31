<?php namespace Thrive\MailchimpModule\Subscriber;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Entry\EntryObserver;
use Illuminate\Support\Facades\Log;

class SubscriberObserver extends EntryObserver
{

    public function updating(EntryInterface $subscriber)
    {
        // Log::info('Updating from Observer');

        // $subscriber->status_local_timestamp = '';
    }
}
