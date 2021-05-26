<?php namespace Thrive\MailchimpModule\Plugin;

use Anomaly\Streams\Platform\Support\Collection;
use Illuminate\View\Factory;

/**
 * Class RenderNavigation
 *
 */
class RenderPlugin
{

    /**
     * The rendering options.
     *
     * @var Collection
     */
    protected $options;


    /**
     * __construct
     *
     * @param  mixed $options
     * @return void
     */
    public function __construct(Collection $options)
    {
        $this->options = $options;
    }

    /**
     * handle
     *
     * @param  mixed $view
     * @return void
     */
    public function handle(Factory $view)
    {
        $options = $this->options;

        return $view->make(
            $options->get('view', 'thrive.module.mailchimp::public.subscribe'),
            compact('options')
        )->render();
    }
}
