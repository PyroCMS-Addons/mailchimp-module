<?php namespace Thrive\MailchimpModule\Webhook;

use Thrive\MailchimpModule\Webhook\Contract\WebhookRepositoryInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;

class WebhookRepository extends EntryRepository implements WebhookRepositoryInterface
{

    /**
     * The entry model.
     *
     * @var WebhookModel
     */
    protected $model;

    /**
     * Create a new WebhookRepository instance.
     *
     * @param WebhookModel $model
     */
    public function __construct(WebhookModel $model)
    {
        $this->model = $model;
    }

    
}
