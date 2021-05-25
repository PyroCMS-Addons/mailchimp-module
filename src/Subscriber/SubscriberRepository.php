<?php namespace Thrive\MailchimpModule\Subscriber;

use Thrive\MailchimpModule\Subscriber\Contract\SubscriberRepositoryInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;

class SubscriberRepository extends EntryRepository implements SubscriberRepositoryInterface
{

    /**
     * The entry model.
     *
     * @var SubscriberModel
     */
    protected $model;

    /**
     * Create a new SubscriberRepository instance.
     *
     * @param SubscriberModel $model
     */
    public function __construct(SubscriberModel $model)
    {
        $this->model = $model;
    }
}
