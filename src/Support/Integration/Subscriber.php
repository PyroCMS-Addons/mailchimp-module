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
			foreach($repository->all() as $local)
			{
				$lists          = $mailchimp->getMembers( $local->audience_remote_id, 'total_items');
				$max_records    = ($lists->total_items) ?? 0;
				$offset         = self::START_COUNT;
				$count          = self::MAX_RECORDS;
				$fields         = null;
				$fields         = 'members.id,members.email_address,members.status,members.merge_fields';
				$exfields       = null; //'members.email_address,members.vip,full_name,total_items';

				for($offset = 0; $offset <= $max_records; $offset = $offset + $count)
				{
					$lists = $mailchimp->getMembers( $local->audience_remote_id, $fields, $exfields, $count, $offset );

					if(isset($lists->members))
					{
						if($lists->members)
						{
							// dd($lists->members);
							foreach($lists->members as $member)
							{
								// Do we have on in our table ?
								if($subscriber = SubscriberModel::where('subscriber_audience_id',$local->audience_remote_id)->where('subscriber_email',$member->email_address)->first())
								{
									self::UpdateLocalSubscriberFromRemote( $subscriber, $member, $local );
								}
								else
								{
									// create
									self::CreateLocalSubscriberFromRemote( $member, $local );
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
	public static function Post(SubscriberInterface $entry)
	{
		// Connect to Mailchimp
		if($mailchimp = Mailchimp::Connect())
		{
			// Check to ensure mailchimp still
			// has this list.
			if($mailchimp->hasList($entry->subscriber_audience_id))
			{
				// update contact
				$fname = ($entry->subscriber_fname != "") ? $entry->subscriber_fname : null ;
				$lname = ($entry->subscriber_lname != "") ? $entry->subscriber_lname : null ;

				//Log::info('Pushing Contact: '. $entry->subscriber_fname);
				return $mailchimp->setListMemberWithMergeFields(
														$entry->subscriber_audience_id,
														$entry->subscriber_email,
														$entry->subscriber_subscribed,
														$fname,
														$lname);
			}
			else
			{
				Log::debug('  » List does not exist on remote. List/Audience Id [ ' . $entry->subscriber_audience_id . ' ]' );
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
	 * LocalhasSubscriber
	 *
	 * @comment		Need to asses if we really need this function
	 * 				It seems this would be much better served
	 * 				on the Model!
	 *
	 * @param  mixed $email
	 * @param  mixed $audience_id
	 * @return void
	 */
	public static function LocalhasSubscriber($email,$audience_id)
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
			$local = new SubscriberModel();
			$local->subscriber_email        	= $remote->email_address;
			$local->subscriber_remote_id   		= $remote->id;
			$local->thrive_contact_synced   	= true;
			$local->subscriber_audience_id     	= $list->audience_remote_id;
			$local->subscriber_subscribed       = ($remote->status == 'subscribed') ? true: false;
			$local->subscriber_status       	= $remote->status;
			$local->subscriber_audience_name 	= $list->audience_name;
			$local->subscriber_fname            = $remote->merge_fields->FNAME;
			$local->subscriber_lname            = $remote->merge_fields->LNAME;
			$local->save();

			return true;
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
			$local->subscriber_email        = $email_address;
			$local->thrive_contact_synced   = false;
			$local->subscriber_status      	= 'subscribed';
			$local->subscriber_audience_id  = $list_id;
			$local->subscriber_subscribed   = true;
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
			$subscriber->thrive_contact_synced   	= true;
			$subscriber->subscriber_remote_id   	= $remote->id;

			$subscriber->subscriber_audience_id     = $list->audience_remote_id;
			$subscriber->subscriber_subscribed      = ($remote->status == 'subscribed') ? true: false ;
			$subscriber->subscriber_status      	= $remote->status;
			
			$subscriber->subscriber_audience_name 	= $list->audience_name;

			$subscriber->subscriber_fname           = $remote->merge_fields->FNAME;
			$subscriber->subscriber_lname           = $remote->merge_fields->LNAME;
			$subscriber->save();

			return true;
		}
		catch(\Exceeption $e)
		{
			//can we delete what we created ?
		}

		return false;
	}
}