<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use app;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

// Thrive
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Webhook\Contract\WebhookInterface;
use Thrive\MailchimpModule\Webhook\WebhookModel;
use Thrive\MailchimpModule\Webhook\WebhookRepository;


/**
 * Webhook
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
class Webhook
{

	
	public static function DeleteFromRemote( WebhookInterface $webhook ) : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			if($mailchimp->deleteWebhook($webhook->webhook_list_id, $webhook->webhook_id))
			{
				return true;
			}
		}

		return false;
	}

	public static function SetCallbackUrl(WebhookInterface $webhook)
	{
		$webhook->webhook_url = self::GetCallbackUrl($webhook->webhook_list_id);
						
		$webhook->save();
	}


	public static function GetCallbackUrl($list_id)
	{
		$settings = app(\Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface::class);

		$secure = $settings->value('thrive.module.mailchimp::mailchimp_http_secure','http');
		
		$url = env('THRIVE_MAILCHIMP_CALLBACK_URL_HTTP') ?? url('mailchimp/webhooks/' . $list_id);

		if($secure == 'https')
		{
			$url = env('THRIVE_MAILCHIMP_CALLBACK_URL_HTTPS') ?? secure_url('mailchimp/webhooks/' . $list_id);
		}
						
		return $url;
	}

	/**
	 * Sync
	 * @todo - Need to implement Sync check
	 * 			As of now, it always pulls down info
	 *
	 * @param  mixed $webhook
	 * @return bool
	 */
	public static function Sync( WebhookInterface $webhook , $default_action = 'Push') : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			if($webhook->webhook_id =='')
			{
				return self::PostCreate($webhook);
			}


			// get the remote webhook
			if($remote = $mailchimp->getWebhook($webhook->webhook_list_id, $webhook->webhook_id))
			{
				// Log::debug(print_r($remote,true));

				if(self::CompareWithRemote($webhook, $remote))
				{
					//they are the same
					// no need to update
					return true;
				}
				else
				{
					if($default_action=='Pull')
					{
						self::Pull($webhook);
					}
					else
					{
						self::Post($webhook);
					}
				}

				return true;
			}

		}

		return false;
	}

	/**
	 * SyncAll
	 *
	 *
	 * @param  mixed $repository
	 * @return void
	 */
	public static function SyncAll( WebhookRepository $repository ) : bool
	{
		$output = new ConsoleOutput();

		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			$audiences = AudienceModel::all();

			foreach($repository->all() as $webhook)
			{
				self::Sync( $webhook );
			}

			// Download where we dont have already
			foreach($audiences as $audience)
			{
				if(!$repository->findBy('webhook_list_id', $audience->audience_remote_id ))
				{
					self::PullAll();
				}
			}

			return true;
		}

		return false;
	}


	/**
	 * Pull
	 *
	 * @param  mixed $webhook
	 * @return bool
	 */
	public static function Pull( WebhookInterface $webhook ) : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			if($remote = $mailchimp->getWebhook($webhook->webhook_list_id, $webhook->webhook_id))
			{
				if(self::CreateOrUpdateLocalWebhook($remote, $webhook))
				{
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * PullAll
	 *
	 * @param  mixed $repository
	 * @return void
	 */
	public static function PullAll( AudienceRepository $repository = null ) : bool
	{
		if($repository == null )
		{
			$repository = app(AudienceRepository::class);
		}

		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			foreach($repository->all() as $audience)
			{
				$lists = $mailchimp->getAllWebhooks( $audience->audience_remote_id );

				if(isset($lists->webhooks))
				{
					foreach($lists->webhooks as $remote_webhook)
					{
						//Log::debug(print_r($remote_webhook,true));

						self::CreateOrUpdateLocalWebhook($remote_webhook);
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Post
	 *
	 * @param  mixed $entry
	 * @return void
	 */
	public static function Post(WebhookInterface $webhook) : bool
	{
		//Log::debug('Post Webhook');

		if($mailchimp = Mailchimp::Connect())
		{
			$values = self::FormatWebhook($webhook);

			if($remote = $mailchimp->getWebhook($webhook->webhook_list_id, $webhook->webhook_id))
			{
				if($remote->url == $webhook->webhook_url)
				{
					unset($values['url']);
				}

				// compare
				if(self::CompareWithRemote($webhook, $remote))
				{
					// no action required
					// they are the same
					return true;
				}
				else
				{
					// Log::debug('Attempt to Update Webhook');
					// Log::debug('    Audience ID : ' . $webhook->webhook_list_id);
					// Log::debug('    WebHook  ID : ' . $webhook->webhook_id);
					// Log::debug('    Values : ');
					// Log::debug(print_r($values,true));

					if($mailchimp->updateWebhook($webhook->webhook_list_id, $webhook->webhook_id, $values))
					{
						Log::debug('Webhook Updated');
						return true;
					}
				}
			}
			else
			{
				//add to Mailchimp
				//Log::debug('Attempt to Add Webhook');

				if($mailchimp->addWebhook($webhook->webhook_list_id, $values))
				{
					return true;
				}		
			}
		}

		return false;
	}

	public static function PostCreate(WebhookInterface $webhook) : bool
	{
		if($mailchimp = Mailchimp::Connect())
		{
			$values = self::FormatWebhook($webhook);

			//Log::debug(' --- FormatValues');
			//Log::debug(print_r($values,true));

			//add to Mailchimp
			if($id = $mailchimp->addWebhook($webhook->webhook_list_id, $values))
			{
				$webhook->webhook_id = $id;
				$webhook->save();
				return true;
			}		
		}

		return false;
	}


	/**
	 * PostAll
	 *
	 * @return void
	 */
	public static function PostAll( WebhookRepository $repository ) : bool
	{
		if($mailchimp = Mailchimp::Connect())
		{
			$local = $repository->all();

			foreach($local as $webhook)
			{
				self::Post($webhook);
			}

			return true;
		}

		return false;
	}


	/**
	 * FormatWebhook
	 * NB: API will be forced to FALSE.
	 * @param  mixed $webhook
	 * @return void
	 */
	public static function FormatWebhook(WebhookInterface $webhook)
	{
		$webhook =
		[
			"url"     			=> self::GetCallbackUrl($webhook->webhook_list_id),
			"events"      =>
			[
				"subscribe" 	=> $webhook->webhook_events_subscribe,
				"unsubscribe" 	=> $webhook->webhook_events_unsubscribe,
				"profile" 		=> $webhook->webhook_events_profile,
				"cleaned" 		=> $webhook->webhook_events_cleaned,
				"upemail" 		=> $webhook->webhook_events_upemail,
				"campaign" 		=> $webhook->webhook_events_campaign,
			],
			"sources"      =>
			[
				"user" 			=> $webhook->webhook_sources_user,
				"admin" 		=> $webhook->webhook_sources_admin,
				"api" 			=> false, //$webhook->webhook_sources_api,
			]
		];

		return $webhook;
	}




	/**
	 * CreateOrUpdateLocalWebhook
	 *
	 * @param  mixed $remote_webhook
	 * @param  mixed $webhook
	 * @return void
	 */
	public static function CreateOrUpdateLocalWebhook( $remote_webhook, WebhookInterface $webhook = null )
	{
		try
		{
			//first try to see if we have on system
			if($webhook == null)
			{
				$webhook = WebhookModel::where('webhook_list_id',$remote_webhook->list_id)->where('webhook_id',$remote_webhook->id)->first();
			}

			// Second check, if still null, lets create
			if($webhook == null)
			{
				$webhook = new WebhookModel();
			}

			$webhook->webhook_name        			= 'Webhook [' . $remote_webhook->id . ']';
			$webhook->webhook_id  					= $remote_webhook->id;
			$webhook->webhook_list_id  				= $remote_webhook->list_id;
			$webhook->webhook_url  					= self::GetCallbackUrl($remote_webhook->list_id);
			$webhook->webhook_events_subscribe 		= $remote_webhook->events->subscribe;
			$webhook->webhook_events_unsubscribe	= $remote_webhook->events->unsubscribe;
			$webhook->webhook_events_profile 		= $remote_webhook->events->profile;
			$webhook->webhook_events_upemail 		= $remote_webhook->events->upemail;
			$webhook->webhook_events_cleaned 		= $remote_webhook->events->cleaned;
			$webhook->webhook_events_campaign 		= $remote_webhook->events->campaign;
			$webhook->webhook_sources_user 			= $remote_webhook->sources->user;
			$webhook->webhook_sources_admin 		= $remote_webhook->sources->admin;
			$webhook->webhook_sources_api 			= $remote_webhook->sources->api;

			$webhook->save();

			return $webhook;

		}
		catch(\Exceeption $e)
		{
			//can we delete what we created ?
		}

		return false;
	}

	
	/**
	 * CompareWithRemote
	 *
	 * @param  mixed $webhook
	 * @param  mixed $remote_webhook
	 * @return void
	 */
	public static function CompareWithRemote(WebhookInterface $webhook, $remote_webhook)
	{
		$same = true;

		$same = ($webhook->webhook_id == $remote_webhook->id) ? true : false;
		//Log::debug( 'webhook_id :'. $webhook->webhook_id . ' - ' . $remote_webhook->id);
		$same = ($webhook->webhook_list_id == $remote_webhook->list_id) ? $same : false;
		//Log::debug( 'webhook_list_id :'. $webhook->webhook_list_id . ' - ' . $remote_webhook->list_id);
		$same = ($webhook->webhook_url == $remote_webhook->url) ? $same : false;
		//Log::debug( 'webhook_url :'. $webhook->webhook_url . ' - ' . $remote_webhook->url);
		$same = ($webhook->webhook_events_subscribe == $remote_webhook->events->subscribe) ? $same : false;
		//Log::debug( 'webhook_events_subscribe :'. $webhook->webhook_events_subscribe . ' - ' . $remote_webhook->events->subscribe);
		$same = ($webhook->webhook_events_unsubscribe == $remote_webhook->events->unsubscribe) ? $same : false;
		//Log::debug( 'webhook_events_unsubscribe :'. $webhook->webhook_events_unsubscribe . ' - ' . $remote_webhook->events->unsubscribe);
		$same = ($webhook->webhook_events_profile == $remote_webhook->events->profile) ? $same : false;
		//Log::debug( 'webhook_events_profile :'. $webhook->webhook_events_profile . ' - ' . $remote_webhook->events->profile);
		$same = ($webhook->webhook_events_upemail == $remote_webhook->events->upemail) ? $same : false;
		//Log::debug( 'webhook_events_upemail :'. $webhook->webhook_events_upemail . ' - ' . $remote_webhook->events->upemail);
		$same = ($webhook->webhook_events_cleaned == $remote_webhook->events->cleaned) ? $same : false;
		//Log::debug( 'webhook_events_cleaned :'. $webhook->webhook_events_cleaned . ' - ' . $remote_webhook->events->cleaned);
		$same = ($webhook->webhook_events_campaign == $remote_webhook->events->campaign) ? $same : false;
		//Log::debug( 'webhook_events_campaign :'. $webhook->webhook_events_campaign . ' - ' . $remote_webhook->events->campaign);
		$same = ($webhook->webhook_sources_user == $remote_webhook->sources->user) ? $same : false;
		//Log::debug( 'webhook_sources_user :'. $webhook->webhook_sources_user . ' - ' . $remote_webhook->sources->user);
		$same = ($webhook->webhook_sources_admin == $remote_webhook->sources->admin) ? $same : false;
		//Log::debug( 'webhook_sources_admin :'. $webhook->webhook_sources_admin . ' - ' . $remote_webhook->sources->admin);
		$same = ($webhook->webhook_sources_api == $remote_webhook->sources->api) ? $same : false;
		//Log::debug( 'webhook_sources_api :'. $webhook->webhook_sources_api . ' - ' . $remote_webhook->sources->api);

		$same_human = ($same)?'The Same':'Not the Same';

		//Log::debug('Compared A with B and thevresults are :' . $same_human);
		if($same)
			return true;

		return false;

	}
}