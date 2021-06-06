<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Thrive
use Symfony\Component\Console\Output\ConsoleOutput;
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Audience\Contract\AudienceInterface;
use Thrive\MailchimpModule\Http\Requests\SubscribeRequest;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Integration\Subscriber;
use Thrive\MailchimpModule\Support\Integration\Subscriber_Base;
use Thrive\MailchimpModule\Support\Mailchimp;
use Thrive\MailchimpModule\Support\Sync\SyncAction;
use Thrive\MailchimpModule\Support\Sync\SyncUtility;


/**
 * Subscriber
 *
 * Business Logic Connecter to the api.
 *
 * The Business Logic classes handle errros,
 * messages, functionality and integrating
 * the system to the api.
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
class Subscriber
{

	const MEMBER_SUBSCRIBED     = 'subscribed';
	const MEMBER_UNSUBSCRIBED   = 'unsubscribed';

	const START_COUNT           = 0;
	const MAX_RECORDS           = 20;

	const MEMBERS_FIELDS 		= 'members.id,members.web_id,members.email_address,members.list_id,members.status,members.merge_fields,members.last_changed';


	public static function CleanSubscriber( $email, $list_id)
	{
		if($subscriber = SubscriberModel::where('subscriber_email',$email)->where('subscriber_audience_id',$list_id)->first())
		{
			$subscriber->subscriber_subscribed = false;
			$subscriber->subscriber_status = 'cleaned';
			$subscriber->save();
		}
	}


	/**
	 * WebhookUnSubscribe
	 * WIP:in dev
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public static function SyncUserByWebId($webid)
	{
		if($subscriber = SubscriberModel::where('subscriber_web_id',$webid)->first())
		{
			self::Sync($subscriber);
		}
	}


	/**
	 * Sync
	 * @todo Needs implementastion
	 *
	 * @param  mixed $subscriber
	 * @return void
	 */
	public static function Sync( SubscriberInterface $subscriber ) : bool
	{
		// Connect to Mailchimp
		$output = new ConsoleOutput();

		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			// get the remote users
			if($remote = $mailchimp->getListMember($subscriber->subscriber_audience_id, $subscriber->subscriber_email))
			{
				$sync_action = SyncUtility::Check($subscriber, $remote);

				self::ExecuteSyncAction($subscriber, $sync_action);

				$output->writeln('Subscriber');
				$output->writeln('   Email         : ' . $subscriber->subscriber_email);
				$output->writeln('   Audience Id   : ' . $subscriber->subscriber_audience_id);
				$output->writeln('   Action        : ' . $sync_action);
				return true;
			}
			else
			{
				// unable to find online
				$output->writeln('   === Subscriber      : ' . $subscriber->subscriber_email . '  ===');
				$output->writeln('            Status     : !!! Unable to locate remotely');
			}
		}

		return false;
	}

	/**
	 * SyncAll
	 *
	 * Sync all subscribers, negate what action to take
	 *
	 * @param  mixed $repository
	 * @return void
	 */
	public static function SyncAll( SubscriberRepository $repository ) : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			foreach($repository->all() as $subscriber)
			{
				self::Sync( $subscriber );
			}

			self::PullAllNotImported();

			return true;
		}

		return false;
	}


	/**
	 * Pull
	 *
	 * @param  mixed $subscriber
	 * @return bool
	 */
	public static function Pull( SubscriberInterface $subscriber ) : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			// update
			if($remote = $mailchimp->getListMember($subscriber->subscriber_audience_id, $subscriber->subscriber_email))
			{
				if(self::CreateOrUpdateLocalSubscriberFromRemote($remote, $subscriber))
				{
					return self::UpdateSubscriberTimestampsToInSync($subscriber);
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
	public static function PullAll( AudienceRepository $repository ) : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			foreach($repository->all() as $local_audience)
			{
				$lists          = $mailchimp->getMembers( $local_audience->audience_remote_id, 'total_items');
				$max_records    = ($lists->total_items) ?? 0;
				$offset         = self::START_COUNT;
				$count          = self::MAX_RECORDS;
				//$fields         = null;
				
				$fields         = self::MEMBERS_FIELDS; 
				$exfields       = null; 

				for($offset = 0; $offset <= $max_records; $offset = $offset + $count)
				{
					if($lists = $mailchimp->getMembers( $local_audience->audience_remote_id, $fields, $exfields, $count, $offset ))
					{
						foreach($lists->members as $remote)
						{
							if($subscriber = self::CreateOrUpdateLocalSubscriberFromRemote($remote))
							{
								self::UpdateSubscriberTimestampsToInSync($subscriber);
							}
						}
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * PullAllNotImported\
	 *
	 * Gets all Subscribers/Members that have not already been downloaded
	 *
	 * @param  mixed $repository
	 * @return void
	 */
	public static function PullAllNotImported() : bool
	{
		$audiences = AudienceModel::all();

		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			foreach($audiences as $local_audience)
			{
				$lists          = $mailchimp->getMembers( $local_audience->audience_remote_id, 'total_items');
				$max_records    = ($lists->total_items) ?? 0;
				$offset         = self::START_COUNT;
				$count          = self::MAX_RECORDS;
				$fields         = null;
				$fields         = self::MEMBERS_FIELDS;
				$exfields       = null;

				for($offset = 0; $offset <= $max_records; $offset = $offset + $count)
				{
					$lists = $mailchimp->getMembers( $local_audience->audience_remote_id, $fields, $exfields, $count, $offset );

					foreach($lists->members as $remote)
					{
						if($subscriber = self::CreateOrUpdateLocalSubscriberFromRemote($remote))
						{
							self::UpdateSubscriberTimestampsToInSync($subscriber);
						}
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
	public static function Post(SubscriberInterface $subscriber, $tags = []) : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{

			if($mailchimp->hasList($subscriber->subscriber_audience_id))
			{
				// update contact
				$fname = ($subscriber->subscriber_fname != "") ? $subscriber->subscriber_fname : null ;
				$lname = ($subscriber->subscriber_lname != "") ? $subscriber->subscriber_lname : null ;
				
				Log::debug('  » 00 Pushing User        : ' . $subscriber->subscriber_email);

				//$post_values = self::FormatSubscriber($subscriber);

				if($mailchimp->setListMemberWithMergeFields(
														$subscriber->subscriber_audience_id,
														$subscriber->subscriber_email,
														$subscriber->subscriber_subscribed,
														$fname,
														$lname))
				{
					// ok, now keep timestamps in sync
					return self::UpdateSubscriberTimestampsToInSync($subscriber);
				}
			}
			else
			{
				Log::debug('  » List does not exist on remote. List/Audience Id [ ' . $subscriber->subscriber_audience_id . ' ]' );
			}
		}

		return false;
	}


	/**
	 * PostAll
	 *
	 * @return void
	 */
	public static function PostAll( SubscriberRepository $repository ) : bool
	{
		if($mailchimp = Mailchimp::Connect())
		{
			Log::debug('--- [ Begin ] ---  Subscriber::PostAll ');

			$local = $repository->all();

			foreach($local as $subscriber)
			{
				self::PostSubscriberToMailchimp($subscriber);
			}

			return true;
		}

		return false;
	}


	public static function GetLocalSubscriber($email, $audience_id) 
	{
		if($subscriber = SubscriberModel::where('subscriber_email',$email)->where('subscriber_audience_id',$audience_id)->first())
		{
			return $subscriber;
		}

		return false;
	}


	public static function FormatSubscriber(SubscriberInterface $subscriber)
	{
		return self::FormatContact(
			$subscriber->subscriber_email, 
			$subscriber->subscriber_subscribed,
			$subscriber->subscriber_fname,
			$subscriber->subscriber_fname);
	}

	/**
	 * FormatContact
	 *
	 * @param  mixed $email
	 * @param  mixed $subscribe
	 * @param  mixed $FNAME
	 * @param  mixed $LNAME
	 * @return void
	 */
	public static function FormatContact($email, $subscribe = true, $FNAME = null, $LNAME = null)
	{
		if($FNAME == null)
		{
			$FNAME = Str::substr($email, 0,  strpos($email, '@') );
		}

		$subscribe_string = ($subscribe) ? self::MEMBER_SUBSCRIBED : self::MEMBER_UNSUBSCRIBED;

		$contact =
		[
			"email_address"     => $email,
			"status"            => $subscribe_string,
			"status_if_new"     => $subscribe_string,
			"merge_fields"      =>
			[
				"FNAME" => $FNAME,
				"LNAME" => $LNAME
			]
		];

		if($contact['merge_fields']['FNAME'] == null)
		{
			unset($contact['merge_fields']['FNAME']);
		}
		if($contact['merge_fields']['LNAME'] == null)
		{
			unset($contact['merge_fields']['LNAME']);
		}

		return $contact;
	}


	/**
	 * ExecuteSyncAction
	 *
	 * @param  mixed $subscriber
	 * @param  mixed $sync_action
	 * @return void
	 */
	public static function ExecuteSyncAction(SubscriberInterface $subscriber, $sync_action)
	{
		$subscriber->status_sync_last_action 	= $sync_action;

		switch($sync_action)
		{
			case SyncAction::Pull:
				self::Pull($subscriber);
				$subscriber->status_sync_messages 		= "Last Sync Check: " . Carbon::now() . "\r\n\r\nStatus: Pull\r\n\r\nMessage: Subscriber details have been PULLED from Mailchimp.";
				$subscriber->status_sync_err_flag 		= false;
				$subscriber->save();
				// Pull
				break;
			case SyncAction::Push:
				self::Post($subscriber);
				$subscriber->status_sync_messages 		= "Last Sync Check: " . Carbon::now() . "\r\n\r\nStatus: Push\r\n\r\nMessage: Subscriber details have been PUSHED to Mailchimp.";
				$subscriber->status_sync_err_flag 		= false;
				$subscriber->save();
				// Push
				break;
			case SyncAction::ErrResolveSuggestPull:
				$subscriber->status_sync_messages 		= "Last Sync Check: " . Carbon::now() . "\r\n\r\nStatus: ErrResolveSuggestPull\r\n\r\nMessage: Subscriber is out of Sync \r\n\r\nRecomended Action: Sync(PULL).";
				$subscriber->status_sync_err_flag 		= true;
				$subscriber->save();
				// ErrResolveSuggestPull
				break;
			case SyncAction::ErrResolveSuggestPush:
				$subscriber->status_sync_messages 		= "Last Sync Check: " . Carbon::now() . "\r\n\r\nStatus: ErrResolveSuggestPush\r\n\r\nMessage: Subscriber is out of Sync \r\n\r\nRecomended Action: Sync(PUSH).";
				$subscriber->status_sync_err_flag 		= true;
				$subscriber->save();
				// ErrResolveSuggestPush
				break;
			case SyncAction::ErrResolveNoSuggestion:
				$subscriber->status_sync_messages 		= "Last Sync Check: " . Carbon::now() . "\r\n\r\nStatus: ErrResolveNoSuggestion\r\n\r\nMessage: Subscriber is out of Sync \r\n\r\nRecomended Action: You can either Sync(PULL) or Sync(PUSH).";
				$subscriber->status_sync_err_flag 		= true;
				$subscriber->save();
				// ErrResolveNoSuggestion
				break;
			case SyncAction::NoChange:
			default:
				$subscriber->status_sync_messages 		= "Last Sync Check: " . Carbon::now() . "\r\n\r\nStatus: OK\r\n\r\nMessage: Subscriber is in Sync. \r\n";
				$subscriber->status_sync_err_flag 		= false;
				$subscriber->save();
				// NoChange
				break;
		}

		return true;
	}


	/**
	 * UpdateSubscriberTimestampsToInSync
	 *
	 * 
	 * 
	 *
	 * This will set both sync timestamps to current
	 *
	 * @param  mixed $subscriber
	 * @param  mixed $remote
	 * @return void
	 */
	public static function UpdateSubscriberTimestampsToInSync(SubscriberInterface $subscriber, string $message = '')
	{
		$ts = date("c");
		Log::debug('SET TS: UpdateSubscriberTimestampsToInSync : ' . $ts);

		// Timestamps for keeping in Sync
		$subscriber->local_timestamp_sync 	= $ts;
		$subscriber->local_timestamp_save   = $ts;

		$subscriber->save();

		return true;
	}


	/**
	 * UpdateSubscriberTimestamp
	 * This will only update the local save timestamp
	 * 
	 *
	 * @param  mixed $subscriber
	 * @return void
	 */
	public static function UpdateSubscriberTimestamp(SubscriberInterface $subscriber)
	{
		$subscriber->local_timestamp_save 	= date("c");
		$subscriber->status_sync_messages 	= "Subscriber Updated Locally on :" . Carbon::now() . "\r\n\r\n-------------------\r\n" . $subscriber->status_sync_messages;
		$subscriber->save();

		Log::debug('Updated Subscriber TS, Subscriber : ' . $subscriber_email);
		Log::debug('Updated Subscriber TS, TimeStamp  : ' . $subscriber->local_timestamp_save);

	}


	public static function AddOrUpdateSubscriberToRemote(SubscriberInterface $subscriber) : bool
	{
		if($mailchimp = Mailchimp::Connect())
		{
			Log::debug('--- [ Begin ] ---  AddOrUpdateSubscriberToRemote ');

			if($remote = $mailchimp->getListMember($subscriber->subscriber_audience_id, $subscriber->subscriber_email))
			{
				if($mailchimp->setListMemberWithMergeFields($subscriber->subscriber_audience_id, $subscriber->subscriber_email, true))
				{
					self::UpdateSubscriberTimestampsToInSync($subscriber);
					return true;
				}
			}
			else
			{
				$post_values = self::FormatSubscriber($subscriber);
				if($mailchimp->addContactToList($subscriber->subscriber_audience_id, $post_values))  //, $tags = []
				{
					self::UpdateSubscriberTimestampsToInSync($subscriber);
					return true;
				}
			}

		}

		return false;

	}


	public static function CreateOrUpdateSubscriebrFromRequest(SubscribeRequest $request)
	{
		Log::debug('--- [ Begin ] ---  Subscriber::CreateOrUpdateSubscriebrFromRequest() ');


	
		$email_address 	= $request->input('subscriber_email');
		$list_id 		= $request->input('audience_id');

		Log::debug('                   Email  : '.$email_address);
		Log::debug('                   ListID : '.$list_id);

		return self::CreateOrUpdateLocalSubscriber( $email_address, $list_id, 'subscribed');
	}

	/**
	 * CreateOrUpdateLocalSubscriberFromRemote
	 *
	 * @param  mixed $remote
	 * @param  mixed $subscriber
	 * @return void
	 */
	public static function CreateOrUpdateLocalSubscriberFromRemote( $remote, SubscriberInterface $subscriber = null )
	{
		Log::debug('--- [ Begin ] ---  Subscriber::CreateOrUpdateLocalSubscriberFromRemote() ');

		try
		{
			if($subscriber == null)
			{
				if(!$subscriber = self::GetLocalSubscriber($remote->email_address, $remote->list_id))
				{
					$subscriber = new SubscriberModel();
				}
			}

			$subscriber->subscriber_web_id        		= $remote->web_id;		
			$subscriber->subscriber_email        		= $remote->email_address;
			$subscriber->subscriber_audience_id  		= $remote->list_id;
			$subscriber->subscriber_status       		= $remote->status;
			$subscriber->subscriber_subscribed   		= ($remote->status == 'subscribed') ? true: false ;
			$subscriber->subscriber_remote_id   		= $remote->id;
			$subscriber->subscriber_fname            	= $remote->merge_fields->FNAME;
			$subscriber->subscriber_lname            	= $remote->merge_fields->LNAME;
			$subscriber->status_remote_timestamp 		= $remote->last_changed;
			$subscriber->save();

			return $subscriber;

		}
		catch(\Exceeption $e)
		{
			//can we delete what we created ?
		}

		return false;
	}


	/**
	 * CreateOrUpdateLocalSubscriber
	 *
	 * @param  mixed $email_address
	 * @param  mixed $list_id
	 * @param  mixed $status
	 * @return void
	 */
	public static function CreateOrUpdateLocalSubscriber( $email_address, $list_id, $status = 'subscribed')
	{

		Log::debug('--- [ Begin ] ---  Subscriber::CreateOrUpdateLocalSubscriber() ');

		try
		{
			$subscriber = null;

			if(!$subscriber = SubscriberModel::where('subscriber_audience_id',$list_id)->where('subscriber_email',$email_address)->first())
			{
				$subscriber = new SubscriberModel();
			}

			$subscriber->subscriber_email        	= $email_address;
			$subscriber->subscriber_audience_id  	= $list_id;
			$subscriber->subscriber_status       	= $status;
			$subscriber->subscriber_subscribed   	= ($status == 'subscribed') ? true: false ;;
			$subscriber->save();

			return $subscriber;
		}
		catch(\Exceeption $e)
		{
			//can we delete what we created ?
		}

		return false;
	}	

}