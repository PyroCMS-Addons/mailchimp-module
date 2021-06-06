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
                'entry.automation_workflow_id',
                'entry.automation_list_id', 
                'entry.automation_list_name',
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
                            case 'sent':
                                $status_color = 'default';
                                break;
                            case 'sending':
                                $status_color = 'info';
                                break;                                      
                            case 'schedule':
                                $status_color = 'default';
                                break;                                  
                            case 'paused':
                                $status_color = 'danger';
                                break;                                
                            case 'save':
                                $status_color = 'success';
                                break; 
                            default:                               
                                $status_color = 'success';
                        }
                        return "<span class='tag tag-{$status_color}'>".ucfirst($entry->automation_status)."</span>";
                    },
                ],                   
            ]
        );
    }
}
