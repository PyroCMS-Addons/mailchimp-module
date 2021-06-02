<?php namespace Thrive\MailchimpModule\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\Environment\Console;
use Thrive\MailchimpModule\Audience\AudienceRepository;
use Thrive\MailchimpModule\Automation\AutomationRepository;
use Thrive\MailchimpModule\Campaign\CampaignRepository;
use Thrive\MailchimpModule\Content\ContentRepository;
use Thrive\MailchimpModule\Subscriber\SubscriberRepository;
use Thrive\MailchimpModule\Support\Integration\Audience;
use Thrive\MailchimpModule\Support\Integration\Automation;
use Thrive\MailchimpModule\Support\Integration\Campaign;
use Thrive\MailchimpModule\Support\Integration\Content;
use Thrive\MailchimpModule\Support\Integration\Subscriber;


/**
 * Get data from eventbrite
 */
class Test extends Command implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailchimp:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Developer tool to run test when needed';

    

    public function __construct(
        AutomationRepository $automationRepository,
        ContentRepository $contentRepository,
        CampaignRepository $campaignRepository,
        SubscriberRepository $subscriberRepository,
        AudienceRepository $audienceRepository)
    {
        $this->automationRepository = $automationRepository;
        $this->audienceRepository = $audienceRepository;
        $this->contentRepository = $contentRepository;
        $this->campaignRepository = $campaignRepository;
        $this->subscriberRepository = $subscriberRepository;

        parent::__construct();
    }


    public function handle()
    {

        $this->processTasks();
            
        Log::info('The command was successfully.');

        $this->info('The command was successfully.');

    }

    private function processTasks()
    {
        Subscriber::TestDates($this->audienceRepository);        
    }


}