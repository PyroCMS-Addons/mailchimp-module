<?php namespace Thrive\MailchimpModule\Content;

use Thrive\MailchimpModule\Content\Contract\ContentRepositoryInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;

/**
 * ContentRepository
 *
 *
 * @package    	Thrive\MailchimpModule
 * @author 		Sam McDonald <s.mcdonald@outlook.com.au>
 * @author 		Thrive
 * @copyright  	2000-2021 Thrive Developement
 * @license    	https://mit-license.org/
 * @license    	https://opensource.org/licenses/MIT
 * @version    	Release: 1.0.0
 * @link       	https://github.com/PyroCMS-Addons/mailchimp-module
 * @since      	Class available since Release 1.0.0
 *
 */
class ContentRepository extends EntryRepository implements ContentRepositoryInterface
{

    /**
     * The entry model.
     *
     * @var ContentModel
     */
    protected $model;

    /**
     * Create a new ContentRepository instance.
     *
     * @param ContentModel $model
     */
    public function __construct(ContentModel $model)
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
