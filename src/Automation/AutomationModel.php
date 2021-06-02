<?php namespace Thrive\MailchimpModule\Automation;

use Thrive\MailchimpModule\Automation\Contract\AutomationInterface;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpAutomationsEntryModel;

/**
 * AutomationModel
 */
class AutomationModel extends MailchimpAutomationsEntryModel implements AutomationInterface
{
    
    /**
     * getWorkflowId
     *
     * @return void
     */
    public function getWorkflowId()
    {
        return $this->automation_workflow_id;
    }


}
