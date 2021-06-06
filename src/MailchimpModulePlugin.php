<?php namespace Thrive\MailchimpModule;

// Laravel
use Anomaly\Streams\Platform\Addon\Plugin\Plugin;
use Anomaly\Streams\Platform\Support\Collection;
use Anomaly\Streams\Platform\Support\Decorator;

// Anomaly
use Anomaly\Streams\Platform\Support\Presenter;
use Illuminate\Support\Str;
use Illuminate\View\Factory;

// Thrive
use Thrive\MailchimpModule\Audience\Contract\AudienceRepositoryInterface;
use Thrive\MailchimpModule\MailchimpModuleCriteria;
use Thrive\MailchimpModule\Plugin\RenderPlugin;
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
            new \Twig_SimpleFunction('mailchimp_version',   [$this, 'mc_version'],   ['is_safe' => ['html']]),
            new \Twig_SimpleFunction(
                'mailchimp',
                function ($root = null) {
                    return (new MailchimpModuleCriteria(
                        'render',
                        function (Collection $options) use ($root) {
                            
                            switch($root)
                            {
                                case 'subscribe': 
                                case 'unsubscribe': 
                                    $options->put('action', $root);
                                    break;
                                default:
                                    $options->put('action', 'subscribe');
                                    break;
                            }

                            return $this->dispatch(new RenderPlugin($options));
                        }
                    ));
                }
            ),            
        ];
    }

    public function mc_version()
    {
        return 'Version';
    }

}
