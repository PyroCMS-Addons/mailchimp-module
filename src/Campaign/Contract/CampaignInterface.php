<?php namespace Thrive\MailchimpModule\Campaign\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

interface CampaignInterface extends EntryInterface
{
    public function canEdit();
}
