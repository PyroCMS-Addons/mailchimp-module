<?php namespace Thrive\MailchimpModule\Campaign\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryRepositoryInterface;

interface CampaignRepositoryInterface extends EntryRepositoryInterface
{
    /**
     * Find a form by it's remote id.
     *
     * @param $campaign_remote_id
     * @return null|FormInterface
     */
    public function findByStrId($campaign_remote_id);
}
