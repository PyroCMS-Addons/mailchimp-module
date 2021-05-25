<?php namespace Thrive\MailchimpModule\Campaign;

use Thrive\MailchimpModule\Campaign\Contract\CampaignInterface;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpCampaignsEntryModel;

class CampaignModel extends MailchimpCampaignsEntryModel implements CampaignInterface
{

    public function getStrId()
    {
        return $this->campaign_str_id;
    }


}
