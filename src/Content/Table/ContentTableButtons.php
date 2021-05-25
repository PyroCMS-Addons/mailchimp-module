<?php namespace Thrive\MailchimpModule\Content\Table;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Thrive\MailchimpModule\Content\Table\ContentTableBuilder;

/**
 * ContentTableButtons
 */
class ContentTableButtons extends TableBuilder
{    
    /**
     * handle
     *
     * @param  mixed $builder
     * @return void
     */
    public function handle(ContentTableBuilder $builder)
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
                ]
            ],
        ]);

    }
}
