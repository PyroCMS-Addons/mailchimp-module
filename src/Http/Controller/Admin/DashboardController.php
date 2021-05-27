<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;
use Redirect;
use Thrive\MailchimpModule\Support\Mailchimp;

/**
 * DashboardController
 *
 * Admin Dashboard Controller
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
class DashboardController extends AdminController
{

    protected $mailchimp;
    
    /**
     * __construct
     *
     * @param  mixed $mailchimp
     * @return void
     */
    public function __construct(Mailchimp $mailchimp)
    {
        $this->mailchimp = $mailchimp;

        parent::__construct();
    }
    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $list = $this->mailchimp->getDefaultList();

        $stats = [];
        $has_stats = false;


        // get the total count
        $stats = $this->mailchimp->getList( $list->id); 

        if(isset($stats->stats))
        {
            $has_stats = true;

            $stats = $stats->stats;
        }

        // dd($stats);

        return view('module::admin.dashboard')->with(compact('stats','has_stats'));
    }   
    
    
    /**
     * These are only Supportive Functions for Development
     */
    public function action($option, MessageBag $messages)
    {
        if($option == "dda")
        {
            if($list = $this->mailchimp->getDefaultList())
            {
                if($response = $this->mailchimp->deleteList($list->id))
                {
                    $messages->info('Audience Removed');
                }
            }
        }

        return redirect()->back();
    }   
}
