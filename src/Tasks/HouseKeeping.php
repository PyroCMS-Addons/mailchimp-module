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
use Thrive\MailchimpModule\Support\Integration\Audience;
use Thrive\MailchimpModule\Support\Integration\Automation;


/**
 * Get data from eventbrite
 */
class Housekeeping extends Command implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailchimp:tidy {option?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Some basic house keeping for the modules data';


    /**
     * The number of times the job may 
     * be attempted.
     *
     * @var int
     */
    public $tries = 5;


    /**
     * The number of seconds the job can run 
     * before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * The maximum number of exceptions to 
     * allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;



    public function __construct(
        AutomationRepository $automationRepository,
        AudienceRepository $audienceRepository)
    {
        $this->automationRepository = $automationRepository;
        $this->audienceRepository = $audienceRepository;

        parent::__construct();
    }


    public function handle()
    {
        $option = $this->argument('option');

        if($option == "" || $option == NULL)
        {
            $option = "Default";
        }

        $this->processTasks();

        Log::info('The command was successful with the option of: '. $option);

        $this->info('The command was successful with the option of: '. $option);

    }

    private function processTasks()
    {
        if(Audience::SyncAll($this->audienceRepository))
        {
            Log::info('Audience Now Synchronised.');

            $this->info('Audiences have been Synchronised.');
        }
        
        if(Automation::Sync($this->automationRepository))
        {
            Log::info('Automation Now Synchronised.');

            $this->info('Automations have been Synchronised.');
        }

    }

}