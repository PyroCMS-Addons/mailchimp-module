<?php namespace Thrive\MailchimpModule\Audience\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryRepositoryInterface;

interface AudienceRepositoryInterface extends EntryRepositoryInterface
{
    /**
     * Find a form by it's slug.
     *
     * @param $slug
     * @return null|FormInterface
     */
    public function findByAudienceId($audience_id);
}
