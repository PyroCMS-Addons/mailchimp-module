<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Facades\Log;

// Thrive
use Illuminate\Support\Str;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Audience\Contract\AudienceInterface;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Integration\Subscriber;
use Thrive\MailchimpModule\Support\Mailchimp;

/*
 * @todo
 * The Subscriber Integration Class needs more work to get Closer to SOLID principles.
 * Timestamps for the entire system need updating but in particular for the
 * Subscriber class.
 * 
 */


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


	// ??
	const START_COUNT           = 0;
	const MAX_RECORDS           = 20;


	/**
	 * Sync
	 * @todo Needs implementastion
	 *
	 * @param  mixed $subscriber
	 * @return void
	 */
	public static function Sync( SubscriberInterface $subscriber )
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			return true;
		}

		return false;
	}

	/**
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 *
	 * This is a mess, but better than back in the controller class
	 * Needs rework!
	 * Still a wip
	 *
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 */
	public static function SyncAll( AudienceRepository $repository )
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
				$exfields       = null; //'members.email_address,members.vip,full_name,total_items';

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
									if(self::AreSyncChangesRequired($subscriber,$member))
									{
										// update
										if($local_subscriber = self::UpdateLocalSubscriberFromRemote($subscriber, $member, $local_audience))
										{
											self::SyncTimestampsAsCurrent($local_subscriber);
										}
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
	 * Post
	 *
	 * @param  mixed $entry
	 * @return void
	 */
	public static function Post(SubscriberInterface $subscriber)
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
	public static function PostAll( SubscriberRepository $repository )
	{
		if($mailchimp = Mailchimp::Connect())
		{
			Log::debug('--- [ Begin ] ---  Subscriber::PostAll ');

			// this is not an effecient way to iterate
			$local = $repository->all();

			// dd($local);
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
	 * LocalHasSubscriber
	 *
	 * @comment		Need to asses if we really need this function
	 * 				It seems this would be much better served
	 * 				on the Model!
	 *
	 * @param  mixed $email
	 * @param  mixed $audience_id
	 * @return void
	 */
	public static function LocalHasSubscriber($email,$audience_id)
	{
		if($subscriber = SubscriberModel::where('subscriber_email',$email)->where('subscriber_audience_id',$audience_id)->first())
		{
			return $subscriber;
		}

		return false;
	}


	/**
	 * FormatContact
	 * Simialr to PrepareContact however FormatContact will be continue to evolve.
	 * It currently assumes first name if not provided.
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
	 * PrepareContact
	 *
	 * @deprecated -
	 * @see FormatContact.
	 *
	 * @param  mixed $email
	 * @param  mixed $subscribe
	 * @param  mixed $FNAME
	 * @param  mixed $LNAME
	 * @return void
	 */
	public static function PrepareContact($email, $subscribe = true, $FNAME = '', $LNAME = '')
	{
		$subscribe_string = ($subscribe) ? 'subscribed' : 'unsubscribed';

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

		// Log::debug('Date1: ' . $subscriber->status_remote_timestamp);
		// Log::debug('Date2: ' . $subscriber->local_timestamp_sync);
		// Log::debug('Date3: ' . $subscriber->local_timestamp_save);
		// Log::debug('Date3: ' . $remote->last_changed);


		try
		{
			// Is Local TS less than the remote, then Sync
			if($subscriber->status_remote_timestamp != "")
			{
				if( date('c', $subscriber->status_remote_timestamp) < date('c', $remote->last_changed ) )
				{
					return true;
				}
			}

			if( ($subscriber->local_timestamp_sync != "") && ($subscriber->local_timestamp_save != ""))
			{
				if(date('c', $subscriber->local_timestamp_sync) < date('c', $subscriber->local_timestamp_save))
				{
					return true;
				}
			}

		}
		catch(\Exceeption $e)
		{

		}

		return false;
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
	 * SyncTimestampsAsCurrent
	 * 
	 * This will set both sync timestamps to current
	 *
	 * @param  mixed $subscriber
	 * @param  mixed $remote
	 * @return void
	 */
	public static function SyncTimestampsAsCurrent(SubscriberInterface $subscriber)
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
		// Timestamps for keeping in Sync
		$subscriber->local_timestamp_save 	= $ts; // (now) ISO 8601 date (added in PHP 5)

		// 'local_timestamp_sync' 
		// 'local_timestamp_save' 
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