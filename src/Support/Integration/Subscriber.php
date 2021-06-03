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
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Sync\SyncAction;
use Thrive\MailchimpModule\Support\Sync\SyncUtility;
use Thrive\MailchimpModule\Support\Integration\Subscriber;
use Thrive\MailchimpModule\Support\Mailchimp;


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
		//Log::debug('Sync User : ' . $subscriber->subscriber_web_id);
		//Log::debug('    Email : ' . $subscriber->subscriber_email);

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
		$output = new ConsoleOutput();

		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			foreach($repository->all() as $subscriber)
			{
				self::Sync( $subscriber );
			}

			// Retrieve any remote user that
			// is not already downloaded.
			self::PullAllNotImported();

			return true;
		}

		$output->writeln('');
		$output->writeln('End of Program <--');

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
				// We need a Local Audience before we can add the subscriebr
				//So find the local Audience first
				if($local_audience = AudienceModel::where('audience_remote_id',$subscriber->subscriber_audience_id)->first())
				{
					if(self::UpdateLocalSubscriberFromRemote($subscriber, $remote, $local_audience))
					{
						return self::SyncTimestampsAsCurrent($subscriber);
					}
				}
				else
				{
					// if we dont have the local Audience
					// best we sync/pull to get it and
					// using recursion try again.
					if(Audience::PullByAudienceId($subscriber->subscriber_audience_id))
					{
						Log::debug('Created Audience AdHoc - so subscribers could be imported.');
						return self::Pull($subscriber);
					}
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
		// Error Count while importing/sync
		$total_errors = 0;

		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			foreach($repository->all() as $local_audience)
			{
				$lists          = $mailchimp->getMembers( $local_audience->audience_remote_id, 'total_items');
				$max_records    = ($lists->total_items) ?? 0;
				$offset         = self::START_COUNT;
				$count          = self::MAX_RECORDS;
				$fields         = null;
				$fields         = 'members.id,members.email_address,members.status,members.merge_fields,members.last_changed';
				$exfields       = null; //'members.vip,full_name,total_items';

				for($offset = 0; $offset <= $max_records; $offset = $offset + $count)
				{
					$lists = $mailchimp->getMembers( $local_audience->audience_remote_id, $fields, $exfields, $count, $offset );

					if(isset($lists->members))
					{
						if($lists->members)
						{
							// dd($lists->members);
							foreach($lists->members as $member)
							{
								// Do we have on in our table ?
								if($subscriber = SubscriberModel::where('subscriber_audience_id',$local_audience->audience_remote_id)->where('subscriber_email',$member->email_address)->first())
								{
									// update
									if($local_subscriber = self::UpdateLocalSubscriberFromRemote($subscriber, $member, $local_audience))
									{
										self::SyncTimestampsAsCurrent($local_subscriber);
									}
								}
								else
								{
									// create
									if($local_subscriber = self::CreateLocalSubscriberFromRemote( $member, $local_audience))
									{
										self::SyncTimestampsAsCurrent($local_subscriber);
									}
								}
							}
						}
						else
						{
							//oops we did not expect this.
							//report issue and rediurect away
						}
					}
					else
					{
						//oops we did not expect this.
						//report issue and rediurect away
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

		// Error Count while importing/sync
		$total_errors = 0;

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
				$fields         = 'members.id,members.web_id,members.email_address,members.status,members.merge_fields,members.last_changed';
				$exfields       = null;

				for($offset = 0; $offset <= $max_records; $offset = $offset + $count)
				{
					$lists = $mailchimp->getMembers( $local_audience->audience_remote_id, $fields, $exfields, $count, $offset );

					if(isset($lists->members))
					{
						if($lists->members)
						{
							// dd($lists->members);
							foreach($lists->members as $member)
							{
								// Do we have on in our table ?
								if(!$subscriber = SubscriberModel::where('subscriber_audience_id',$local_audience->audience_remote_id)->where('subscriber_email',$member->email_address)->first())
								{
									// create
									if($local_subscriber = self::CreateLocalSubscriberFromRemote( $member, $local_audience))
									{
										self::SyncTimestampsAsCurrent($local_subscriber);
									}
								}
							}
						}
						else
						{
							//oops we did not expect this.
						}
					}
					else
					{
						//oops we did not expect this.
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
	public static function Post(SubscriberInterface $subscriber) : bool
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			// Check to ensure mailchimp still
			// has this list.
			if($mailchimp->hasList($subscriber->subscriber_audience_id))
			{
				// update contact
				$fname = ($subscriber->subscriber_fname != "") ? $subscriber->subscriber_fname : null ;
				$lname = ($subscriber->subscriber_lname != "") ? $subscriber->subscriber_lname : null ;


				// we may haveready updated the sync ts,
				// however on a succesful Post() we need to update again
				// to maintain the sync.
				if($mailchimp->setListMemberWithMergeFields(
														$subscriber->subscriber_audience_id,
														$subscriber->subscriber_email,
														$subscriber->subscriber_subscribed,
														$fname,
														$lname))
				{
					// ok, now keep timestamps in sync
					return self::SyncTimestampsAsCurrent($subscriber);
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

			// this is not an effecient way to iterate
			$local = $repository->all();

			foreach($local as $subscriber)
			{
				Log::debug('  » 00 Pushing User        : ' . $subscriber->subscriber_email);

				self::PostSubscriberToMailchimp($subscriber);
			}

			return true;
		}

		return false;
	}


	/**
	 * IsSubscriberLocallyRecorded
	 *
	 * @comment		Need to asses if we really need this function
	 * 				It seems this would be much better served
	 * 				on the Model!
	 *
	 * @param  mixed $email
	 * @param  mixed $audience_id
	 * @return void
	 */
	public static function IsSubscriberLocallyRecorded($email, $audience_id) : bool
	{
		if($subscriber = SubscriberModel::where('subscriber_email',$email)->where('subscriber_audience_id',$audience_id)->first())
		{
			return true;
		}

		return false;
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
			"merge_fields"      =>
			[
				"FNAME" => $FNAME,
				"LNAME" => $LNAME
			]
		];

		return $contact;
	}


	/**
	 * CreateOrUpdateLocalSubscriber
	 *
	 * @param  mixed $email_address
	 * @param  mixed $list_id
	 * @param  mixed $status
	 * @return void
	 */
	public static function CreateOrUpdateLocalSubscriber( $email_address, $list_id, $status = 'subscribed' )
	{
		try
		{
			$subscriber = null;

			if($subscriber = SubscriberModel::where('subscriber_audience_id',$list_id)->where('subscriber_email',$email_address)->first())
			{
				// Do not alter or create remote TS
			}
			else
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

	/**
	 * CreateLocalSubscriber
	 *
	 * @param  mixed $email_address
	 * @param  mixed $list_id
	 * @return void
	 */
	public static function CreateLocalSubscriber( $email_address, $list_id )
	{
		try
		{
			if(SubscriberModel::where('subscriber_audience_id',$list_id)->where('subscriber_email',$email_address)->first())
			{
				Log::debug('Subscriber Already exist in Mailchimp');
				return false;
			}

			// create
			$local = new SubscriberModel();
			$local->subscriber_email        	= $email_address;
			$local->subscriber_status      		= 'subscribed';
			$local->subscriber_audience_id  	= $list_id;
			$local->subscriber_subscribed   	= true;

			$local->save();

			return $local;
		}
		catch(\Exceeption $e)
		{
			//can we delete what we created ?
		}

		return false;
	}



	/**
	 * CreateLocalSubscriberFromRemote
	 *
	 * @param  mixed $remote
	 * @param  mixed $list
	 * @return void
	 */
	public static function CreateLocalSubscriberFromRemote( $remote, AudienceInterface $list )
	{
		try
		{
			// create
			$subscriber = new SubscriberModel();
			$subscriber->subscriber_email        		= $remote->email_address;
			$subscriber->subscriber_web_id        		= $remote->web_id;		
			$subscriber->subscriber_remote_id   		= $remote->id;
			$subscriber->subscriber_audience_id     	= $list->audience_remote_id;
			$subscriber->subscriber_subscribed       	= ($remote->status == 'subscribed') ? true: false;
			$subscriber->subscriber_status       		= $remote->status;
			$subscriber->subscriber_audience_name 		= $list->audience_name;
			$subscriber->subscriber_fname            	= $remote->merge_fields->FNAME;
			$subscriber->subscriber_lname            	= $remote->merge_fields->LNAME;

			// Timestamps for keeping in Sync
			$subscriber->status_remote_timestamp 	= $remote->last_changed;

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
	 * AreSyncChangesRequired
	 *
	 * We dont check each field, we check the following;
	 * 		1. Check remote TS for changes
	 * 		2. Check local-TS-Save compared to local-TS-Sync
	 *
	 * @param  mixed $subscriber
	 * @param  mixed $remote
	 * @return void
	 */
	public static function AreSyncChangesRequired(SubscriberInterface $subscriber, $remote) : bool
	{
		// until bugs fixed this will always return true;
		return true;
	}


	/**
	 * UpdateLocalSubscriberFromRemote
	 *
	 * @param  mixed $subscriber
	 * @param  mixed $remote
	 * @param  mixed $list
	 * @return void
	 */
	public static function UpdateLocalSubscriberFromRemote( SubscriberInterface $subscriber, $remote, AudienceInterface $list )
	{
		try
		{
			// update
			$subscriber->subscriber_email        	= $remote->email_address;
			$subscriber->subscriber_remote_id   	= $remote->id;
			$subscriber->subscriber_web_id        	= $remote->web_id;


			$subscriber->subscriber_audience_id     = $list->audience_remote_id;
			$subscriber->subscriber_subscribed      = ($remote->status == 'subscribed') ? true: false ;
			$subscriber->subscriber_status      	= $remote->status;

			$subscriber->subscriber_audience_name 	= $list->audience_name;

			$subscriber->subscriber_fname           = $remote->merge_fields->FNAME;
			$subscriber->subscriber_lname           = $remote->merge_fields->LNAME;

			// Timestamps for keeping in Sync
			$subscriber->status_remote_timestamp 	= $remote->last_changed;

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
	 * SyncTimestampsAsCurrent
	 *
	 * This will set both sync timestamps to current
	 *
	 * @param  mixed $subscriber
	 * @param  mixed $remote
	 * @return void
	 */
	public static function SyncTimestampsAsCurrent(SubscriberInterface $subscriber, string $message = '')
	{
		$ts = date("c");
		Log::debug('SET TS: SyncTimestampsAsCurrent : ' . $ts);

		// Timestamps for keeping in Sync
		$subscriber->local_timestamp_sync 	= $ts;
		$subscriber->local_timestamp_save   = $ts;

		$subscriber->save();

		return true;
	}


	/**
	 * SetCurrentSaveTimestamp
	 * This will only update the local save timestamp
	 *
	 * @param  mixed $subscriber
	 * @return void
	 */
	public static function SetCurrentSaveTimestamp(SubscriberInterface $subscriber)
	{
		$ts = date("c");
		Log::debug('SET TS: SetCurrentSaveTimestamp : ' . $ts);
		$subscriber->local_timestamp_save 	= $ts;
		$subscriber->status_sync_messages 	= "Subscriber Updated Locally on :" . Carbon::now() . "\r\n\r\n-------------------\r\n" . $subscriber->status_sync_messages;
		$subscriber->save();
	}


	/**
	 * @todo - Not required
	 */
	public static function GetCurrentSaveTimestamp()
	{
		// Timestamps for keeping in Sync
		return date("c");
		// (now) ISO 8601 date (added in PHP 5)
	}
}