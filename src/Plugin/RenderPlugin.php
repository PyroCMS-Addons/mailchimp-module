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

        $this->handle_tags();

        $this->set_handler();

        $this->set_redirect();

        return $view->make(
            $options->get('view', 'thrive.module.mailchimp::public.plugin'),
            compact('options')
        )->render();
    }
    
    /**
     * handle_tags
     * 
     * Process tags for use in macro
     *
     * @return void
     */
    private function handle_tags()
    {
        $options = $this->options;

        $tag    = $options->get('tag');
        $tags   = $options->get('tags');

        // get the default tag
        if($tag != '')
        {
            $tags[] = $tag;
        }

        // dynamic tags
        if( ($options->get('tag_year') == true) || ($options->get('tag_month') == true) )
        {
            if($options->get('tag_year') == true)
            {
                $tags[] = date("Y");
            }
    
            if($options->get('tag_month') == true)
            {
                $tags[] = date("M");
            }
        }

        $options->put('tags', $tags );


        return true;


    }


    private function set_handler()
    {
        $options = $this->options;

        $action    = $options->get('action');

        // get the default tag
        if($action == 'subscribe')
        {
            $handler = 'mailchimp/handler/subscribe';
        }
        else
        {
            $handler = 'mailchimp/handler/unsubscribe';
        }
   
        $options->put('handler', $handler );
        
        return true;

    }

    private function set_redirect()
    {
        $options    = $this->options;

        $url        = $options->get('redirect_url');

        if($url == 'false')
        {
            $options->put('redirect_url', 'thrive.module.mailchimp.redirect.back' );
        }
        else
        {
            $options->put('redirect_url', $url );
        }

                
        return true;

    }
}
