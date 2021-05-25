<?php namespace Thrive\MailchimpModule\Automation\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryRepositoryInterface;

interface AutomationRepositoryInterface extends EntryRepositoryInterface
{
    /**
     * Find a form by it's slug.
     *
     * @param $slug
     * @return null|FormInterface
     */
    public function findByStrId($str_id);
}
