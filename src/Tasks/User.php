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





        $this->processTasks();
            
        Log::info('The command was successfully.');

        $this->info('The command was successfully.');

    }

    private function processTasks()
    {
        // if(Subscriber::TestDates($this->audienceRepository))
        // {
        //     Log::info('Audience Now Synchronised.');

        //     $this->info('Audiences have been Synchronised.');
        // }
        
    }

    private function displayUser(SubscriberInterface $subscriber)
    {
        $this->info('--------------------------------------------------');

        $this->info('User Model     : ' . $subscriber->id);
        $this->info('     Email     : ' . $subscriber->subscriber_email);
        $this->info('     List ID   : ' . $subscriber->subscriber_audience_id);
        $this->info('     RMTE TS   : ' . Carbon::parse($subscriber->status_remote_timestamp));
        $this->info('     SYNC TS   : ' . Carbon::parse($subscriber->local_timestamp_sync));
        $this->info('     SAVE TS   : ' . Carbon::parse($subscriber->local_timestamp_save));
    }

    protected function getOptions()
    {
        return [
            ['list', null, InputOption::VALUE_OPTIONAL, 'The List ID for the UserEmail'],
        ];
    }
}