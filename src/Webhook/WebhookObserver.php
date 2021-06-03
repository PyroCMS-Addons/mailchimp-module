<?php namespace Thrive\MailchimpModule\Webhook;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Entry\EntryObserver;
use Illuminate\Support\Facades\Log;

class WebhookObserver extends EntryObserver
{

    public function updating(EntryInterface $subscriber)
    {

    }
}
