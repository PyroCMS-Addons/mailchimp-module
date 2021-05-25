<?php namespace Thrive\MailchimpModule\Audience;

use Thrive\MailchimpModule\Audience\Contract\AudienceInterface;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpAudiencesEntryModel;

/**
 * AudienceModel
 */
class AudienceModel extends MailchimpAudiencesEntryModel implements AudienceInterface
{
    
    /**
     * getStrId
     *
     * @return void
     */
    public function getStrId()
    {
        return $this->str_id;
    }


}
