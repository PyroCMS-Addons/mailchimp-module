<?php namespace Thrive\MailchimpModule\Audience;

use Thrive\MailchimpModule\Audience\Contract\AudienceInterface;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpAudiencesEntryModel;

/**
 * AudienceModel
 */
class AudienceModel extends MailchimpAudiencesEntryModel implements AudienceInterface
{
    
    /**
     * getAudienceId
     *
     * @return void
     */
    public function getAudienceId()
    {
        return $this->audience_remote_id;
    }

}
