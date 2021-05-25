<?php namespace Thrive\MailchimpModule\Subscriber;

use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpSubscribersEntryModel;

class SubscriberModel extends MailchimpSubscribersEntryModel implements SubscriberInterface
{

}
