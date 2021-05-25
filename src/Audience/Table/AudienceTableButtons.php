<?php namespace Thrive\MailchimpModule\Audience\Table;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Audience\Table\AudienceTableBuilder;

/**
 * AudienceTableButtons
 */
class AudienceTableButtons extends TableBuilder
{
    public function handle(AudienceTableBuilder $builder)
    {

        $builder->setButtons([  
            'edit' => 
            [
                'type' => 'success',
            ],
            'sync' => 
            [
                'type' => 'primary',
                'attributes' => [
                    'data-toggle'  => 'confirm',
                    'data-message' => 'Are you sure ?'
                ],
            ],   
            // 'push' => 
            // [
            //     'type' => 'danger',
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
        ]);



    }
}
