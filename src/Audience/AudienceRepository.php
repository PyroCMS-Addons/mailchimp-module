<?php namespace Thrive\MailchimpModule\Audience;

use Thrive\MailchimpModule\Audience\Contract\AudienceRepositoryInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;

/**
 * AudienceRepository
 */
class AudienceRepository extends EntryRepository implements AudienceRepositoryInterface
{

    /**
     * The entry model.
     *
     * @var AudienceModel
     */
    protected $model;

    /**
     * Create a new AudienceRepository instance.
     *
     * @param AudienceModel $model
     */
    public function __construct(AudienceModel $model)
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
        return $this->model->where('str_id', $str_id)->first();
    }


}
