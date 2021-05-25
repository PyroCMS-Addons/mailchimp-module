<?php namespace Thrive\MailchimpModule\Campaign;

use Thrive\MailchimpModule\Campaign\Contract\CampaignRepositoryInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;

class CampaignRepository extends EntryRepository implements CampaignRepositoryInterface
{

    /**
     * The entry model.
     *
     * @var CampaignModel
     */
    protected $model;

    /**
     * Create a new CampaignRepository instance.
     *
     * @param CampaignModel $model
     */
    public function __construct(CampaignModel $model)
    {
        $this->model = $model;
    }


     /**
     * Find a form by it's slug.
     *
     * @param $slug
     * @return null|FormInterface
     */
    public function findByStrId($str_id)
    {
        return $this->model->where('campaign_str_id', $str_id)->first();
    }


}
