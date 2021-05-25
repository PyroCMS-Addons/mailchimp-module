<?php namespace Thrive\MailchimpModule\Campaign\Table;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Campaign\Table\CampaignTableBuilder;

class CampaignTableButtons extends TableBuilder
{
    public function handle(CampaignTableBuilder $builder)
    {
           
        // $buttons = [
        //     'copy',
        //     'edit',
        // ];

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
            'edit' => 
            [
                'type'      => 'success',            
            ],
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
