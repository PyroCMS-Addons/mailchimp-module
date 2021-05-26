<?php namespace Thrive\MailchimpModule\Automation\Table;

// Anomaly
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;

// Thrive
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
                'type'      => 'success',
                'disabled'  => 'disabled', // while not developed lets keep it disabled
            ],
            'start' =>
            [
                'type' => 'info',
                'icon' => 'fa fa-play',
                'attributes' => [
                    'data-toggle'       => 'confirm',
                    'data-title'        => 'thrive.module.mailchimp::common.are_you_sure',
                    'data-message'      => 'thrive.module.mailchimp::common.are_you_sure_start_automation'
                ],
                'enabled'    => function (EntryInterface $entry) {
                    // true if
                        // paused| save
                    if (($entry->automation_status == 'save') || ($entry->automation_status == 'paused')) {
                        return true;
                    }
                    return false;
                },
            ],
            'pause' =>
            [
                'type' => 'warning',
                'icon' => 'fa fa-pause',
                'attributes' => [
                    'data-toggle'       => 'confirm',
                    'data-title'        => 'thrive.module.mailchimp::common.are_you_sure',
                    'data-message'      => 'thrive.module.mailchimp::common.are_you_sure_start_automation'
                ],
                'enabled'    => function (EntryInterface $entry) {
                    // true if
                        // sending
                    if (($entry->automation_status == 'sending')) {
                        return true;
                    }
                    return false;
                },
            ],
            // 'delete' =>
            // [
            //     'type' => 'danger',
            //     'attributes' => [
            //         'data-toggle'       => 'confirm',
            //         'data-title'        => 'thrive.module.mailchimp::common.are_you_sure',
            //         'data-message'      => 'thrive.module.mailchimp::common.are_you_sure_stop_automation'
            //     ],
            //     'enabled'    => function (EntryInterface $entry) {
            //         if (($entry->automation_status == 'paused') || ($entry->automation_status == 'save')) {
            //             return true;
            //         }
            //         return false;
            //     },
            // ],
        ]);

    }
}
