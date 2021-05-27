<?php namespace Thrive\MailchimpModule\Support\Integration;

// Laravel
use Illuminate\Support\Facades\Log;

// Thrive
use Thrive\MailchimpModule\Audience\AudienceModel;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Audience\Contract\AudienceInterface;
use Thrive\MailchimpModule\Support\Mailchimp;

/**
 * Audience
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
class Audience
{
    /**
     * Sync
     *
     *
     * @param  mixed $entry
     * @return void
     */
    public static function Sync(AudienceInterface $entry)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            // is there a remote list
            if($remote = $mailchimp->getList($entry->str_id))
            {
                if(self::updateLocalFromRemote($entry, $remote))
                {
                    $entry->update(['thrive_sync_status' => 'thrive.module.mailchimp::common.sync_success']);
                }
            }
            else
            {
                // nope, no remote
                // we could just flag the local status that
                // this list does not exist remotly.
            }

            return true;
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
    public static function SyncAll(AudienceRepository $repository)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            $all_remote_lists = $mailchimp->getAllLists();

            foreach($all_remote_lists as $remote)
            {
                // check if we have a local copy
                if($local_list = $repository->findBy('str_id',$remote->id))
                {
                    self::Sync($local_list);
                }
                else
                {
                    // check if we have a deleted copy loally
                    if($repository->allWithTrashed()->findBy('str_id',$remote->id))
                    {
                        // skip as we have a clash and deleted locally
                        // we can check to see if its deleted remotely
                        // but allList() is returning this for
                        // some reason.
                    }
                    else
                    {
                        // Local List not found, so lets create
                        // one to keep it in sync.
                        if($item = self::createLocalFromRemote($remote))
                        {
                            $item->update(['thrive_sync_status' => 'thrive.module.mailchimp::common.sync_success']);
                        }
                    }

                }

            }

            // Now Check for vagrant lists
            // List that no longer exist
            // on the remote Mailchimp.
            // self::CleanVagrantLists($repository);

            return true;
        }

        return false;

    }
    
    
    /**
     * Push
     *
     * @param  mixed $entry
     * @param  mixed $repository
     * @return void
     */
    public static function Push(AudienceInterface $entry)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            if($list_values = self::PrepareList($entry))
            {
                if($mailchimp->hasList($entry->str_id))
                {
                    return $mailchimp->updateList($entry->str_id, $list_values);
                }
                else
                {
                    if($remote_list = $mailchimp->createList($list_values))
                    {
                        $entry->update(['str_id' => $remote_list->id]);
                        return true;
                    }
                    else
                    {
                        //set local entry status to requires sync_create
                    }
                }
            }
        }
    }
    
    /**
     * PushAll
     *
     * @param  mixed $repository
     * @return void
     */
    public static function PushAll(AudienceRepository $repository)
    {
        foreach($repository->all() as $audience)
        {
            self::Push($audience);
        }
    }

    
    /**
     * CleanVagrantLists
     *
     * @return void
     */
    public static function CleanVagrantLists(AudienceRepository $repository)
    {
        if($mailchimp = Mailchimp::Connect())
        {
            $all_remote_lists = $mailchimp->getAllLists();

            foreach($repository->all() as $local_list)
            {
                $found = false;

                foreach($all_remote_lists as $remote_list)
                {
                    if( $local_list->str_id == $remote_list->id )
                    {
                        $found = true;
                    }
                }

                if(!$found)
                {
                    $local_list->delete();
                }

            }

            return true;
        }

        return false;
    }


    /**
     * PrepareList
     *
     * This prepares the List parameter that will be updated
     * Pass in the Audience entry, and you will get a
     * fully prepared array ready in the correct
     * format for Mailchimp.
     *
     * @param  mixed $entry
     * @return void
     */
    public static function PrepareList(AudienceInterface $entry)
    {
        $status         = true;
        $list_values    = [];

        try
        {
            $list_values =
            [
                "name"                  => $entry->name,
                "permission_reminder"   => $entry->permission_reminder,
                "email_type_option"     => $entry->email_type_option,
                "contact"           =>
                [
                    "company"           => $entry->contact_company_name,
                    "address1"          => $entry->contact_address1,
                    "city"              => $entry->contact_city,
                    "state"             => $entry->contact_state,
                    "zip"               => $entry->contact_zip,
                    "country"           => $entry->contact_country,
                ],
                "campaign_defaults" =>
                [
                    "from_name"         => $entry->campaign_from_name,
                    "from_email"        => $entry->campaign_from_email,
                    "subject"           => $entry->campaign_subject,
                    "language"          => $entry->campaign_language,
                ]
            ];
        }
        catch(\Exception $e)
        {
            $status = false;
        }
        finally
        {
            if($status)
            {
                return $list_values;
            }
        }

        return $status;
    }


    /**
     * updateLocalFromRemote
     *
     * @param  mixed $local
     * @param  mixed $remote
     * @return void
     */
    public static function updateLocalFromRemote($local, $remote)
    {
        try
        {
            $local->name                     = $remote->name;
            $local->str_id                   = $remote->id;
            $local->permission_reminder      = $remote->permission_reminder;
            $local->email_type_option        = $remote->email_type_option;
            $local->contact_company_name     = $remote->contact->company;
            $local->contact_address1         = $remote->contact->address1;
            $local->contact_state            = $remote->contact->state;
            $local->contact_zip              = $remote->contact->zip;
            $local->contact_country          = $remote->contact->country;
            $local->contact_city             = $remote->contact->city;
            $local->campaign_from_name       = $remote->campaign_defaults->from_name;
            $local->campaign_from_email      = $remote->campaign_defaults->from_email;
            $local->campaign_subject         = $remote->campaign_defaults->subject;
            $local->campaign_language        = $remote->campaign_defaults->language;
            $local->save();

            return $local;
        }
        catch(\Exception $e)
        {
            //
        }

        return false;
    }


    /**
     * createLocalFromRemote
     *
     * @param  mixed $remote
     * @return void
     */
    public static function createLocalFromRemote($remote)
    {
        try
        {
            $local = new AudienceModel();

            $local->name                     = $remote->name;
            $local->str_id                   = $remote->id;
            $local->permission_reminder      = $remote->permission_reminder;
            $local->email_type_option        = $remote->email_type_option;
            $local->contact_company_name     = $remote->contact->company;
            $local->contact_address1         = $remote->contact->address1;
            $local->contact_state            = $remote->contact->state;
            $local->contact_zip              = $remote->contact->zip;
            $local->contact_country          = $remote->contact->country;
            $local->contact_city             = $remote->contact->city;
            $local->campaign_from_name       = $remote->campaign_defaults->from_name;
            $local->campaign_from_email      = $remote->campaign_defaults->from_email;
            $local->campaign_subject         = $remote->campaign_defaults->subject;
            $local->campaign_language        = $remote->campaign_defaults->language;

            $local->save();

            return $local;
        }
        catch(\Exception $e)
        {
            //
        }

        return false;
    }

    
    /**
     * LocalHasAudience
     * 
     * 
     * @param  mixed $str_id
     * @return void
     */
    public static function LocalHasAudience($str_id)
    {
        if($a = AudienceModel::where('str_id',$str_id)->first())
        {
            return true;
        }

        return false;
    }
}