<?php namespace Thrive\MailchimpModule\Subscriber\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

interface SubscriberInterface extends EntryInterface
{
    public function findSubscriber($email, $list_id);


    public function findByRemoteId($id);
}
