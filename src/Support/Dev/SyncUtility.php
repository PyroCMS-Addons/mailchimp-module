<?php namespace Thrive\MailchimpModule\Support\Dev;


// Laravel
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Thrive
use Symfony\Component\Console\Output\ConsoleOutput;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Audience\Contract\AudienceInterface;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Dev\SyncAction;
use Thrive\MailchimpModule\Support\Dev\SyncUtility;
use Thrive\MailchimpModule\Support\Integration\Subscriber;
use Thrive\MailchimpModule\Support\Mailchimp;


class SyncUtility
{
    
    /**
     * Check
     *
     * @param  mixed $subscriber
     * @param  mixed $remote
     * @return mixed
     */
    public static function Check(SubscriberInterface $subscriber, $remote) : string
    {
        $date_local       = Carbon::parse($subscriber->status_remote_timestamp);
        $date_remote      = Carbon::parse($remote->last_changed);

        $date_local_2     = Carbon::parse($subscriber->local_timestamp_save);
        $date_remote_2    = Carbon::parse($subscriber->local_timestamp_sync);
      
        // Failed at pre checks
        if(!self::RunPreliminaryChecks())
            return SyncAction::ErrResolveNoSuggestion;


        // Remote Status appears ok, lets check Local Status
        if(($date_local->eq($date_remote)))
        {
            if(($date_local_2->eq($date_remote_2)))
            {
                // ok all set, this subscriber is fuly in sync
                // no changes required
                return SyncAction::NoChange;
            }

            if(($date_local_2->gt($date_remote_2)))
            {
                // Local Changes found
                // Require Push
                return SyncAction::Push;
            }

            if(($date_local_2->lt($date_remote_2)))
            {
                // Remote Changes found
                // Require Push
                return SyncAction::Pull;
            }
        }

        // Remote Status appears to have error
        if(($date_local->gt($date_remote)))
        {
            if(($date_local_2->eq($date_remote_2)))
            {
                // Somehow the remote Timestamp is greater than
                // the service timestamp
                // User Can Pull or Push, no suggestion 
                // provided fom software
                return SyncAction::ErrResolveNoSuggestion;
            }

            if(($date_local_2->gt($date_remote_2)))
            {
                // it appears not only do we have the system
                // out of sync, but we have made changes.
                // We suggest Push, but let user reslve
                return SyncAction::ErrResolveSuggestPush;
            }

            if(($date_local_2->lt($date_remote_2)))
            {
                // System Errors plus local out of sync
                // We suggest Pull, but let user resolve
                return SyncAction::ErrResolveSuggestPull;
            }
        }     
        
        // Changes found on Remote
        if(($date_local->lt($date_remote)))
        {
            if(($date_local_2->eq($date_remote_2)))
            {
                // Changes on Remote Only
                return SyncAction::Pull;
            }

            if(($date_local_2->gt($date_remote_2)))
            {
                // Changes found on BothRemote and Local
                // Do not Suggest, let User decide
                return SyncAction::ErrResolveNoSuggestion;
            }

            if(($date_local_2->lt($date_remote_2)))
            {
                // Changes on Remote
                return SyncAction::Pull;
            }
        }  

        return SyncAction::ErrResolveNoSuggestion;
    }

    /**
     * Ensure both have valid dates
     * and not empty strings etc.
     */
    public static function RunPreliminaryChecks() : bool
    {
        return true;
        
    }

}