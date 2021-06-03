<?php namespace Thrive\MailchimpModule\Subscriber;

use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpSubscribersEntryModel;

class SubscriberModel extends MailchimpSubscribersEntryModel implements SubscriberInterface
{    
    /**
     * findSubscriber
     *
     * @param  mixed $email
     * @param  mixed $list_id
     * @return void
     */
    public function findSubscriber($email, $list_id)
    {
        return $this->where
                ->where('subscriber_email', $email)
                ->where('subscriber_audience_id',$list_id)
                ->first();
    }
    
    /**
     * findByRemoteId
     *
     * @param  mixed $id
     * @return void
     */
    public function findByRemoteId($id)
    {
        return $this->where->where('subscriber_remote_id', $id)->first();
    }

}
