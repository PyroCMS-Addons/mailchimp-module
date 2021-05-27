<?php namespace Thrive\MailchimpModule\Campaign\Table;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Campaign\Table\CampaignTableBuilder;

class CampaignTableButtons extends TableBuilder
{    
    /**
     * handle
     *
     * @param  mixed $builder
     * @return void
     */
    public function handle(CampaignTableBuilder $builder)
    {
        $builder->setButtons([
            'send' =>
            [
                'type' => 'info',
                'attributes' => [
                    'data-toggle'  => 'confirm',
                    'data-message' => 'Are you sure ?'
                ],
                'enabled'    => function (EntryInterface $entry) {
                    if ($entry->status == 'sent') {
                        return false;
                    }
                    return true;
                },
            ],
            // 'send_test' =>
            // [
            //     'type' => 'info',
            //     'attributes' => [
            //         'data-toggle'  => 'confirm',
            //         'data-message' => 'Are you sure ?'
            //     ],
            //     'enabled'    => function (EntryInterface $entry) {
            //         if ($entry->status == 'sent') {
            //             return false;
            //         }
            //         return true;
            //     },
            // ],
            'edit' =>
            [
                'type'      => 'success',
                'disabled'    => function (EntryInterface $entry) {
                    if ($entry->status == 'sent') {
                        return true;
                    }
                    return false;
                },
            ],
            'actions' =>
            [
                'text'    => 'Actions',
                'data-toggle' => 'modal',
                'data-target' => '#modal',
                'href'        => 'admin/mailchimp/campaigns/option/edit/{entry.id}',                
                'type'       => 'info',
                'enabled'    => function (EntryInterface $entry) {
                    if ($entry->status == 'save') {
                        return true;
                    }
                    return false;
                },
            ],    
            // 'preview' =>
            // [
            //     'text'    => 'Preview',
            //     'data-toggle' => 'modal',
            //     'data-target' => '#modal',
            //     'href'        => 'admin/mailchimp/campaigns/option/edit/{entry.id}',                
            //     'type'       => 'info',
            //     'enabled'    => function (EntryInterface $entry) {
            //         if ($entry->status == 'save') {
            //             return true;
            //         }
            //         return false;
            //     },
            // ],                       
            'copy' =>
            [
                'type' => 'primary',
                'attributes' => [
                    'data-toggle'  => 'confirm',
                    'data-message' => 'Are you sure ?'
                ]
            ],

        ]);

    }
}
