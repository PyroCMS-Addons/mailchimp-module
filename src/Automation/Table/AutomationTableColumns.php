<?php namespace Thrive\MailchimpModule\Automation\Table;

// use Anomaly\Streams\Platform\Entry\EntryModel;
use Thrive\MailchimpModule\Automation\Contract\AutomationInterface;
use Thrive\MailchimpModule\Automation\Table\AutomationTableBuilder;


/**
 * Class AutomationTableColumns
 *
 */
class AutomationTableColumns
{

    /**
     * Handle the columns.
     *
     * @param AutomationTableBuilder $builder
     */
    public function handle(AutomationTableBuilder $builder)
    {
        $builder->setColumns( 
            [
                'automation_title',                
                'entry.automation_workflow_id'    => [
                    'wrapper' => function (AutomationInterface $entry) {
                        $status_color = 'info';
                        switch($entry->automation_status)
                        {
                            case 'sending':
                                $status_color = 'info';
                                break;
                            case 'saved':
                                $status_color = 'success';
                                break;   
                            case 'paused':
                            default:                               
                                $status_color = 'default';
                        }
                        return "<span class='small text-{$status_color}'>".ucfirst($entry->automation_workflow_id)."</span>";
                    },
                ],       
                'entry.automation_list_id'    => [
                    'wrapper' => function (AutomationInterface $entry) {
                        return "<span class='small text-info'>".ucfirst($entry->automation_list_id). "</span>";
                    },
                ],    
                'entry.automation_list_name'    => [
                    'wrapper' => function (AutomationInterface $entry) {
                        return "<span class='small text-info'>".ucfirst($entry->automation_list_name). "</span>";
                    },
                ],  
                'entry.automation_emails_sent'    => [
                    'wrapper' => function (AutomationInterface $entry) {
                        return "<span class='tag tag-success'>".ucfirst($entry->automation_emails_sent). "</span>";
                    },
                ],                  
                'entry.automation_status'    => [
                    'wrapper' => function (AutomationInterface $entry) {
                        $status_color = 'info';
                        switch($entry->automation_status)
                        {
                            case 'sending':
                                $status_color = 'info';
                                break;
                            case 'saved':
                                $status_color = 'success';
                                break;   
                            case 'paused':
                            default:                               
                                $status_color = 'default';
                        }
                        return "<span class='tag tag-{$status_color}'>".ucfirst($entry->automation_status)."</span>";
                    },
                ],                   
            ]
        );
    }
}
