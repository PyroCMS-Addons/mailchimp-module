<?php namespace Thrive\MailchimpModule\Audience\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

interface AudienceInterface extends EntryInterface
{
    
    public function getAudienceId();


    public function findByAudienceId($audience_id);
}
