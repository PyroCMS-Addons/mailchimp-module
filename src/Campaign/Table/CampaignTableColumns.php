<?php namespace Thrive\MailchimpModule\Campaign\Table;

// use Anomaly\Streams\Platform\Entry\EntryModel;
use Thrive\MailchimpModule\Campaign\Contract\CampaignInterface;
use Thrive\MailchimpModule\Campaign\Table\CampaignTableBuilder;


/**
 * Class CampaignTableColumns
 *
 */
class CampaignTableColumns
{

    /**
     * Handle the columns.
     *
     * @param CampaignTableBuilder $builder
     */
    public function handle(CampaignTableBuilder $builder)
    {
        $builder->setColumns( 
            [
                'campaign_name',
                'entry.campaign_type'    => [
                    'wrapper' => function (CampaignInterface $entry) {

                        $status_color = 'info';
                
                        switch($entry->campaign_type)
                        {
                            case 'regular':
                                $status_color = 'default';
                                break;
                            default:                               
                                $status_color = 'info';
                        }

                        return "<span class='small text-success'>".ucfirst($entry->campaign_type)."</span>";
                    },
                ],                                  
                'entry.status'    => [
                    'wrapper' => function (CampaignInterface $entry) {

                        $status_color = 'info';
                        $status_text  = 'Active';
                
                        switch($entry->status)
                        {
                            case 'sent':
                                $status_color = 'danger';
                                $status_text  = 'Completed';

                                break;
                            case 'saved':
                                $status_color = 'success';
                                break; 
                            default:                               
                                $status_color = 'info';
                        }

                        return "<span class='tag tag-{$status_color}'>{$status_text}</span>";
                    },
                ],                
            ]
        );
    }
}
