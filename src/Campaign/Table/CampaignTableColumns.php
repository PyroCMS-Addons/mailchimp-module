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
                'campaign_remote_id',
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
                'entry.campaign_status'    => [
                    'wrapper' => function (CampaignInterface $entry) {

                        $status_color = 'info';
                        $status_text  = 'Active';
                
                        switch($entry->campaign_status)
                        {
                            case 'sent':
                                $status_color = 'default';
                                $status_text  = 'Sent';
                                break;
                            case 'sending':
                                $status_color = 'info';
                                $status_text  = 'Sending';
                                break;                                      
                            case 'schedule':
                                $status_color = 'default';
                                $status_text  = 'Schedule';
                                break;                                  
                            case 'paused':
                                $status_color = 'danger';
                                $status_text  = 'Paused';
                                break;                                
                            case 'save':
                                $status_color = 'success';
                                $status_text  = 'Draft';
                                break; 
                            default:                               
                                $status_color = 'success';
                                $status_text  = ucfirst($entry->campaign_status);
                        }

                        return "<span class='tag tag-{$status_color}'>{$status_text}</span>";
                    },
                ],                
            ]
        );
    }
}
