<?php namespace Thrive\MailchimpModule\Automation\Contract;

use Anomaly\Streams\Platform\Entry\Contract\EntryRepositoryInterface;

interface AutomationRepositoryInterface extends EntryRepositoryInterface
{
    /**
     * Find a form by it's workflow_id.
     *
     * @param $slug
     * @return null|FormInterface
     */
    public function findByWorkflowId($automation_workflow_id);
}
