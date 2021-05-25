<?php namespace Thrive\MailchimpModule\Campaign\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryRepositoryInterface;

interface CampaignRepositoryInterface extends EntryRepositoryInterface
{
    /**
     * Find a form by it's slug.
     *
     * @param $slug
     * @return null|FormInterface
     */
    public function findByStrId($str_id);
}
