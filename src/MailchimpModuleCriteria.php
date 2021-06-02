<?php namespace Thrive\MailchimpModule;

use Anomaly\Streams\Platform\Addon\Plugin\PluginCriteria;

class MailchimpModuleCriteria extends PluginCriteria
{
    public function list($list_id)
    {
        $this->options['action']            = 'subscribe'; // subscribe || unsubscribe
        $this->options['list']              = $list_id; // id of the list to subscribe to
        $this->options['tag']               = '';
        $this->options['tags']              = []; // comma seperated list
        $this->options['title']             = ''; // comma seperated list
        $this->options['handler']           = 'mailchimp/handler/subscribe'; 
        $this->options['form_name']         = 'mailchimp'; 
        $this->options['form_name']         = 'mailchimp'; 
        $this->options['button_text']       = 'Submit'; 
        $this->options['input_class']       = 'form-control'; 
        $this->options['email_placeholder'] = 'Enter your@email.com'; 
        $this->options['view']              = 'thrive.module.mailchimp::public.subscribe'; 

        //
        // Experimental, not nessiserially 
        // considered for V1.
        $this->options['use_fname']         = false; 
        $this->options['tag_year']          = false;  // adds the current year to input tags
        $this->options['tag_month']         = false;  // adds the current month to input tags


        //Array of addtional merge fields
        // If available, they will be posted
        $this->options['merge_fields']       = ['FNAME','LNAME','DOB'];

        return $this;
    }

}
