<?php namespace Thrive\MailchimpModule\Automation;

use Thrive\MailchimpModule\Automation\Contract\AutomationInterface;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpAutomationsEntryModel;

/**
 * AutomationModel
 */
class AutomationModel extends MailchimpAutomationsEntryModel implements AutomationInterface
{
    
    /**
     * getStrId
     *
     * @return void
     */
    public function getStrId()
    {
        return $this->campaign_str_id;
    }


}
