<?php namespace Thrive\MailchimpModule;

// Laravel
use Illuminate\Support\Str;
use Illuminate\View\Factory;

// Anomaly
use Anomaly\Streams\Platform\Support\Decorator;
use Anomaly\Streams\Platform\Support\Presenter;
use Anomaly\Streams\Platform\Addon\Plugin\Plugin;

// Thrive
use Thrive\MailchimpModule\Audience\Contract\AudienceRepositoryInterface;
use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormBuilder;
use Thrive\MailchimpModule\Subscriber\Form\SubscriberFormHandler;


/**
 * MailchimpModulePlugin
 *
 * This is the Main plugin class for the Mailchimp Module
 * for PyroCMS 3.x
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
 */
class MailchimpModulePlugin extends Plugin
{

    /**
     * audiences
     *
     * @var mixed
     */
    protected $audiences;



    /**
     * decorator
     *
     * @var mixed
     */
    protected $decorator;



    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
            AudienceRepositoryInterface $audiences,
            Decorator $decorator,
            Factory $view)
            {

        $this->view             = $view;
        $this->audiences        = $audiences;
        $this->decorator        = $decorator;
    }



    /**
     * getFunctions
     *
     * Get a list of available functions
     * for the plugin
     *
     * @return void
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('subscribe',   [$this, 'subscribe'],   ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('unsubscribe', [$this, 'unsubscribe'], ['is_safe' => ['html']]),
        ];
    }



    /**
     * subscribe
     *
     * @param  mixed $str_id
     * @return null|string
     *
     * Currently the plugin works like so;
     *      <code>{{ subscribe('list-id') }}</code>
     *
     * However we want it to work like this..
     * More research is needed.
     *      <code>{{ mailchimp().subscribe('list-id') }}</code>
     */
    public function subscribe($str_id)
    {

        //
        // if its not a valid Audience
        // Then we need to abort
        //
        if (!$audience = $this->audiences->findByStrId($str_id)) {
            // return '[Not Found]';
            return null;
        }

        return $this->decorator->decorate(
            $this
                ->view
                ->make(
                    'thrive.module.mailchimp::public.subscribe',
                    [
                        'strid'             => $audience->getStrid(),
                        'title'             => 'Subscribe',
                        'action'            => 'subscribe',
                        'btn_text'          => 'Subscribe',
                        'handler_url'       => 'mailchimp/handler/subscribe',
                    ]
                )->render()
            );

    }


    /**
     * unsubscribe
     *
     * @param  mixed $str_id
     * @return null|string
     *
     * Currently the plugin works like so;
     *      <code>{{ unsubscribe('list-id') }}</code>
     *
     * However we want it to work like this..
     * More research is needed.
     *      <code>{{ mailchimp().unsubscribe('list-id') }}</code>
     */
    public function unsubscribe($str_id)
    {

        //
        // if its not a valid Audience
        // Then we need to abort
        //
        if (!$audience = $this->audiences->findByStrId($str_id)) {
            // return '[Not Found]';
            return null;
        }

        return $this->decorator->decorate(
            $this
                ->view
                ->make(
                    'thrive.module.mailchimp::public.subscribe',
                    [
                        'strid'             => $audience->getStrid(),
                        'title'             => 'Un Subscribe',
                        'action'            => 'unsubscribe',
                        'btn_text'          => 'Unsubscribe',
                        'handler_url'       => 'mailchimp/handler/subscribe',
                    ]
                )->render()
            );

    }
}
