<?php namespace Thrive\MailchimpModule\Automation;

use Thrive\MailchimpModule\Automation\Contract\AutomationRepositoryInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;

class AutomationRepository extends EntryRepository implements AutomationRepositoryInterface
{

    /**
     * The entry model.
     *
     * @var AutomationModel
     */
    protected $model;

    /**
     * Create a new AutomationRepository instance.
     *
     * @param AutomationModel $model
     */
    public function __construct(AutomationModel $model)
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
