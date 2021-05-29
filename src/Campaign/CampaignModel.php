<?php namespace Thrive\MailchimpModule\Campaign;

use Thrive\MailchimpModule\Campaign\Contract\CampaignInterface;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpCampaignsEntryModel;

class CampaignModel extends MailchimpCampaignsEntryModel implements CampaignInterface
{
    
    /**
     * getStrId
     *
     * @return void
     */
    public function getStrId()
    {
        return $this->campaign_remote_id;
    }
    
    /**
     * canEdit
     *
     * @return void
     */
    public function canEdit()
    {
        if($this->campaign_status == 'sent')
        {
            return false;
        }

        return true;
    }


}
