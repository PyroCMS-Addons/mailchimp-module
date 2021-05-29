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
     * findByAudienceId
     *
     * @param  mixed $audience_id
     * @return void
     */
    public function findByAudienceId($audience_id)
    {
        return $this->model->where('audience_remote_id', $audience_id)->first();
    }

}
