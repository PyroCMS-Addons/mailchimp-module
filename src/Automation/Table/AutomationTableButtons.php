<?php namespace Thrive\MailchimpModule\Automation\Table;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Automation\Table\AutomationTableBuilder;

/**
 * AutomationTableButtons
 */
class AutomationTableButtons extends TableBuilder
{    
    /**
     * handle
     *
     * @param  mixed $builder
     * @return void
     */
    public function handle(AutomationTableBuilder $builder)
    {
           
        $builder->setButtons([  
            'edit' => 
            [
                'type' => 'success',
            ],
            'start' => 
            [
                'type' => 'info',
                'attributes' => [
                    'data-toggle'       => 'confirm',
                    'data-title'        => 'thrive.module.mailchimp::common.are_you_sure',
                    'data-message'      => 'thrive.module.mailchimp::common.are_you_sure_start_automation'
                ],
                'enabled'    => function (EntryInterface $entry) {
                    if ($entry->status == 'sent') {
                        return false;
                    }
                    return true;
                },                
            ],
            'stop' => 
            [
                'type' => 'danger',
                'attributes' => [
                    'data-toggle'       => 'confirm',
                    'data-title'        => 'thrive.module.mailchimp::common.are_you_sure',
                    'data-message'      => 'thrive.module.mailchimp::common.are_you_sure_stop_automation'
                ],
                'enabled'    => function (EntryInterface $entry) {
                    if ($entry->status == 'sent') {
                        return false;
                    }
                    return true;
                },
            ],            
        ]);

    }
}
