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
use Thrive\MailchimpModule\Support\Integration\Webhook;
use Thrive\MailchimpModule\Webhook\WebhookRepository;


/**
 * Get data from eventbrite
 */
class Sync extends Command implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailchimp:sync {option?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command only sync, will sync all streams with remote.';

    

    public function __construct(
        AutomationRepository $automationRepository,
        ContentRepository $contentRepository,
        CampaignRepository $campaignRepository,
        SubscriberRepository $subscriberRepository,
        AudienceRepository $audienceRepository,
        WebhookRepository $webhookRepository)
    {
        $this->automationRepository = $automationRepository;
        $this->audienceRepository = $audienceRepository;
        $this->contentRepository = $contentRepository;
        $this->campaignRepository = $campaignRepository;
        $this->subscriberRepository = $subscriberRepository;
        $this->webhookRepository = $webhookRepository;

        parent::__construct();
    }


    public function handle()
    {
        $option = $this->argument('option');

        if($option == "" || $option == NULL)
        {
            $option = "Default";
        }

        switch(strtolower($option))
        {
            case 'clean':
                $this->clean_lists();
                break;
            case 'audiences':
                $this->syncAudience();
                break;                
            case 'automations':
                $this->syncAutomations();
                break;
            case 'subscribers':
                $this->syncSubscribers();
                break;            
            case 'webhooks':
                $this->syncWebhooks();
                break;                       
            // case 'reset':
            //     $this->delete_all_data();
            //     break;
            case 'all':
            default:
                $this->syncAll();
                break;

        }
            
        Log::info('The command was successful with the option of: '. $option);

        $this->info('The command was successful with the option of: '. $option);

    }


    
    /**
     * syncAll
     *
     * @return void
     */
    private function syncAll()
    {
        // Sync Audiences
        $this->syncAudience();

        // Sync Automations
        $this->syncAutomations();

        // Sync Campaigns
        $this->syncCampaigns();

        // Now Sync Subs
        $this->syncSubscribers();

        // and webhooks
        $this->syncWebhooks();

    }
     
        
    /**
     * syncAudience
     *
     * @return void
     */
    private function syncAudience()
    {
        if(Audience::SyncAll($this->audienceRepository))
        {
            Log::info('Audience Now Synchronised.');

            $this->info('Audiences have been Synchronised.');
        }
    }

    /**
     * syncAutomations
     *
     * @return void
     */
    private function syncAutomations()
    {
        if(Automation::SyncAll($this->automationRepository))
        {
            Log::info('Automation Now Synchronised.');

            $this->info('Automations have been Synchronised.');
        }
    }


    /**
     * syncCampaigns
     *
     * @return void
     */
    private function syncCampaigns()
    {
        if(Campaign::SyncAll($this->campaignRepository))
        {
            Log::info('Campaigns Now Synchronised.');

            $this->info('Campaigns have been Synchronised.');
        }
    }

    /**
     * syncSubscribers
     *
     * @return void
     */
    private function syncSubscribers()
    {

        if(Subscriber::SyncAll($this->subscriberRepository))
        {
            Log::info('Subscribers Now Synchronised.');

            $this->info('Subscribers have been Synchronised.');
        }

    }


    private function syncWebhooks()
    {
        if(Webhook::SyncAll($this->webhookRepository))
        {
            Log::info('Webhooks Now Synchronised.');

            $this->info('Webhooks have been Synchronised.');
        }

    }

    private function clean_lists()
    {
        if(Audience::CleanVagrantLists($this->audienceRepository))
        {
            Log::info('Removed Unwanted Audiences.');

            $this->info('Removed Unwanted Audiences.');
        }
    }




    private function delete_all_data()
    {
        $this->audienceRepository->truncate();
        $this->campaignRepository->truncate();
        $this->automationRepository->truncate();
        $this->subscriberRepository->truncate();
    }

}