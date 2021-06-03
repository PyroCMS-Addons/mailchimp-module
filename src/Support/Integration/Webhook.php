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
	
	/**
	 * Sync
	 * @todo - Need to implement Sync check
	 * 			As of now, it always pulls down info
	 *
	 * @param  mixed $webhook
	 * @return bool
	 */
	public static function Sync( WebhookInterface $webhook ) : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			// get the remote webhook
			if($remote = $mailchimp->getWebhook($webhook->webhook_list_id, $webhook->webhook_id))
			{
				//$sync_action = SyncUtility::CheckWebhook($webhook, $remote);
				//self::ExecuteSyncAction($subscriber, $sync_action);
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
				return self::CreateOrUpdateLocalWebhook($remote, $webhook);
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
		if($mailchimp = Mailchimp::Connect())
		{
			if($mailchimp->getWebhook($webhook->webhook_list_id, $webhook->webhook_id))
			{
				$values = self::FormatWebhook($webhook);

				if($mailchimp->updateWebhook($webhook->webhook_list_id, $webhook->webhook_id, $values))
				{
					return true;
				}
			}
			else
			{
				return false;
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
			Log::debug('--- [ Begin ] ---  Webhook::PostAll ');

			// this is not an effecient way to iterate
			$local = $repository->all();

			// dd($local);
			foreach($local as $webhook)
			{
				Log::debug('  » 00 Pushing Webhook     : ' . $webhook->webhook_name . ', id: '. $webhook->webhook_id);

				self::PostWebhookToMailchimp($webhook);
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
			//"url"     			=> '{url}/mailchimp/webhooks/{dyn}/',
			"url"     			=> 'mailchimp/webhooks',
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
			if(!$webhook == null)
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
			$webhook->webhook_url  					= $remote_webhook->url;
			$webhook->webhook_events_subscribe 		= $remote_webhook->events->subscribe;
			$webhook->webhook_events_unsubscribe	= $remote_webhook->events->unsubscribe;
			$webhook->webhook_events_profile 		= $remote_webhook->events->profile;
			$webhook->webhook_events_upemail 		= $remote_webhook->events->upemail;
			$webhook->webhook_events_cleaned 		= $remote_webhook->events->cleaned;
			$webhook->webhook_events_campaign 		= $remote_webhook->events->campaign;
			$webhook->webhook_sources_user 			= $remote_webhook->sources->user;
			$webhook->webhook_sources_admin 		= $remote_webhook->sources->admin;
			$webhook->webhook_sources_api 			= $remote_webhook->sources->api;
			$webhook->webhook_enabled  				= true;

			$webhook->save();

			return $webhook;

		}
		catch(\Exceeption $e)
		{
			//can we delete what we created ?
		}

		return false;
	}
}