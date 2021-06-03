<?php namespace Thrive\MailchimpModule\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Console\Input\InputOption;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Subscriber\Contract\SubscriberInterface;
use Thrive\MailchimpModule\Subscriber\SubscriberModel;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Integration\Audience;
use Thrive\MailchimpModule\Support\Integration\Subscriber;
use Thrive\MailchimpModule\Support\Mailchimp;


/**
 * Get data from eventbrite
 */
class User extends Command implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailchimp:user {email} {list?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get User details';

    
    public function __construct(
        SubscriberRepository $subscriberRepository,
        AudienceRepository $audienceRepository)
    {
        $this->audienceRepository = $audienceRepository;
        $this->subscriberRepository = $subscriberRepository;

        parent::__construct();
    }


    public function handle()
    {
        $email = $this->argument('email');

        if (!$listid = $this->argument('list')) {

            $this->info('-- No list provided: Showing All results for ' . $email);

            $users = SubscriberModel::where('subscriber_email',$email)->get();
            foreach($users as $user)
            {
                $this->displayUser($user);
            }
        }
        else
        {
            if($user = SubscriberModel::where('subscriber_email',$email)->where('subscriber_audience_id',$listid)->first())
            {
                $this->displayUser($user);
            }
        }
            
        Log::info('The command was successfully.');

        $this->info('The command was successfully.');

    }

    private function displayUser(SubscriberInterface $subscriber)
    {
        $remote = null;
        $remote_data = 'Unable to Get Remote Record';

		if($mailchimp = Mailchimp::Connect())
		{
			// update
			if($remote = $mailchimp->getListMember($subscriber->subscriber_audience_id, $subscriber->subscriber_email))
			{
                
            }
        }

        if($remote)
        {
            $remote_data = Carbon::parse($remote->last_changed);
        }

        $this->info('--------------------------------------------------');
        $this->info('');

        $this->info('User Model           : ' . $subscriber->id);
        $this->info('   Email             : ' . $subscriber->subscriber_email);
        $this->info('   Remote ID (hash)  : ' . $subscriber->subscriber_remote_id);
        $this->info('   Web ID            : ' . $subscriber->subscriber_web_id);
        $this->info('   List ID           : ' . $subscriber->subscriber_audience_id);
        $this->info('   LST CHANGED (L)   : ' . Carbon::parse($subscriber->status_remote_timestamp));

        if($remote)
        {
            $this->info('   LST CHANGED (R)   : ' . $remote_data);
        }
        else
        {
            $this->info('   LST CHANGED (R)   : ' . 'Unable to Get Remote Record');

        }
        $this->info('   SYNC TS           : ' . Carbon::parse($subscriber->local_timestamp_sync));
        $this->info('   SAVE TS           : ' . Carbon::parse($subscriber->local_timestamp_save));
        $this->info('');
        $this->info('   Last Action       : ' . $subscriber->status_sync_last_action);
        //$this->info('     Messages          : ' . $subscriber->status_sync_messages);
        $this->info('');

        $this->info('---- end ---');
        $this->info('');

        
    }

    protected function getOptions()
    {
        return [
            ['list', null, InputOption::VALUE_OPTIONAL, 'The List ID for the UserEmail'],
        ];
    }
}