<?php namespace Thrive\MailchimpModule;

// Laravel
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

// Anomaly
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpAudiencesEntryModel;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpCampaignsEntryModel;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpContentsEntryModel;
use Anomaly\Streams\Platform\Model\Mailchimp\MailchimpSubscribersEntryModel;
use Illuminate\Routing\Router;

// Thrive Campaign
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Audience\Contract\AudienceRepositoryInterface;

// Thrive Audience
use Thrive\MailchimpModule\Campaign\CampaignModel;
use Thrive\MailchimpModule\Campaign\CampaignRepository;
use Thrive\MailchimpModule\Campaign\Contract\CampaignRepositoryInterface;

// Thrive Content
use Thrive\MailchimpModule\Content\ContentModel;
use Thrive\MailchimpModule\Content\ContentRepository;
use Thrive\MailchimpModule\Content\Contract\ContentRepositoryInterface;

// Thrive Subscriber
use Thrive\MailchimpModule\MailchimpModulePlugin;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberRepositoryInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;


// Thrive Plugin
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Mailchimp;



/**
 * MailchimpModuleServiceProvider
 *
 * The ServiceProvider for the mailchimp Module
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
class MailchimpModuleServiceProvider extends AddonServiceProvider
{

	/**
	 * List of plugins for MailchimpModule
	 * Needs to be bound to the SP.
	 *
	 * @type array|null
	 */
	protected $plugins = [
		MailchimpModulePlugin::class
	];

	/**
	 * The addon Artisan commands.
	 *
	 * @type array|null
	 */
    protected $commands = [
		\Thrive\MailchimpModule\Tasks\Sync::class,       
		\Thrive\MailchimpModule\Tasks\HouseKeeping::class,       
    ];

	
	/**
	 * The addon's scheduled commands.
	 * 
	 * @see https://laravel.com/docs/8.x/scheduling#preventing-task-overlaps
	 * 
	 * Depending on your needs
	 * ----------------------
	 * everyMinute
	 * everySixHours
	 * monthly
	 * quarterly
	 * weekly
	 * yearly
	 *
	 * @type array|null
	 */
    protected $schedules = [
        'everySixHours' => [ 
            \Thrive\MailchimpModule\Tasks\HouseKeeping::class,
        ],        				     
    ];

	/**
	 * The addon API routes.
	 *
	 * @type array|null
	 */
	protected $api = [];

	/**
	 * Core Mailchimp Module routes.
	 *
	 * @type array|null
	 */
	protected $routes = [
		// Admin Dashboard
		'admin/mailchimp'                           => 'Thrive\MailchimpModule\Http\Controller\Admin\DashboardController@index',
		'admin/mailchimp/dashboard/{option?}'       => 'Thrive\MailchimpModule\Http\Controller\Admin\DashboardController@action',

		// Subscribers
		'admin/mailchimp/subscribers'               => 'Thrive\MailchimpModule\Http\Controller\Admin\SubscribersController@index',
		'admin/mailchimp/subscribers/create'        => 'Thrive\MailchimpModule\Http\Controller\Admin\SubscribersController@create',
		'admin/mailchimp/subscribers/edit/{id}'     => 'Thrive\MailchimpModule\Http\Controller\Admin\SubscribersController@edit',

		// Audiences
		'admin/mailchimp/audiences'                 => 'Thrive\MailchimpModule\Http\Controller\Admin\AudiencesController@index',
		'admin/mailchimp/audiences/create'          => 'Thrive\MailchimpModule\Http\Controller\Admin\AudiencesController@create',
		'admin/mailchimp/audiences/edit/{id}'       => 'Thrive\MailchimpModule\Http\Controller\Admin\AudiencesController@edit',

		// Campaigns
		'admin/mailchimp/campaigns'                 => 'Thrive\MailchimpModule\Http\Controller\Admin\CampaignsController@index',
		'admin/mailchimp/campaigns/create'          => 'Thrive\MailchimpModule\Http\Controller\Admin\CampaignsController@create',
		'admin/mailchimp/campaigns/edit/{id}'       => 'Thrive\MailchimpModule\Http\Controller\Admin\CampaignsController@edit',
		'admin/mailchimp/campaigns/option/{option}/{id}'       => 'Thrive\MailchimpModule\Http\Controller\Admin\CampaignsController@option',

		// Settings
		'admin/mailchimp/settings'                  => 'Thrive\MailchimpModule\Http\Controller\Admin\SettingsController@edit',

		// Public Subscriber Handler
		'mailchimp/handler/subscribe'               => 'Thrive\MailchimpModule\Subscriber\Form\SubscriberFormHandler@handle',

	];


	/**
	 * The addon middleware.
	 *
	 * @type array|null
	 */
	protected $middleware = [
		//Thrive\MailchimpModule\Http\Middleware\ExampleMiddleware::class
	];

	/**
	 * Addon group middleware.
	 *
	 * @var array
	 */
	protected $groupMiddleware = [
		//'web' => [
		//    Thrive\MailchimpModule\Http\Middleware\ExampleMiddleware::class,
		//],
	];

	/**
	 * Addon route middleware.
	 *
	 * @type array|null
	 */
	protected $routeMiddleware = [];

	/**
	 * The addon event listeners.
	 *
	 * @type array|null
	 */
	protected $listeners = [
		//Thrive\MailchimpModule\Event\ExampleEvent::class => [
		//    Thrive\MailchimpModule\Listener\ExampleListener::class,
		//],
	];

	/**
	 * The addon alias bindings.
	 *
	 * @type array|null
	 */
	protected $aliases = [
		//'Example' => Thrive\MailchimpModule\Example::class
	];

	/**
	 * The addon class bindings.
	 *
	 * @type array|null
	 */
	protected $bindings = [
		MailchimpSubscribersEntryModel::class   => SubscriberModel::class,
		MailchimpAudiencesEntryModel::class     => AudienceModel::class,
		MailchimpCampaignsEntryModel::class     => CampaignModel::class,
		MailchimpContentsEntryModel::class 		=> ContentModel::class,
		// Automation
		// Content
	];

	/**
	 * The addon singleton bindings.
	 *
	 * @type array|null
	 */
	protected $singletons = [
		SubscriberRepositoryInterface::class    => SubscriberRepository::class,
		AudienceRepositoryInterface::class      => AudienceRepository::class,
		CampaignRepositoryInterface::class      => CampaignRepository::class,
		ContentRepositoryInterface::class      	=> ContentRepository::class,
	];

	/**
	 * Additional service providers.
	 *
	 * @type array|null
	 */
	protected $providers = [
		//\ExamplePackage\Provider\ExampleProvider::class
	];

	/**
	 * The addon view overrides.
	 *
	 * @type array|null
	 */
	protected $overrides = [
		//'streams::errors/404' => 'module::errors/404',
		//'streams::errors/500' => 'module::errors/500',
	];

	/**
	 * The addon mobile-only view overrides.
	 *
	 * @type array|null
	 */
	protected $mobile = [
		//'streams::errors/404' => 'module::mobile/errors/404',
		//'streams::errors/500' => 'module::mobile/errors/500',
	];

	/**
	 * Register the addon.
	 */
	public function register()
	{
		// Run extra pre-boot registration logic here.
		// Use method injection or commands to bring in services.
	}



	public function boot()
	{
		// binding a class to the service container 
		$this->app->bind(Mailchimp::class, function ($app) {
			return new Mailchimp();
		});

	}

	/**
	 * Map additional addon routes.
	 *
	 * @param Router $router
	 */
	public function map(Router $router)
	{
		// Register dynamic routes here for example.
		// Use method injection or commands to bring in services.
	}

}
