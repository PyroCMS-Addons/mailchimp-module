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
        $this->options['use_fname']         = false; 
        $this->options['input_class']       = 'form-control'; 
        $this->options['view']             = 'thrive.module.mailchimp::public.subscribe'; 
        
        // perhaps show a way to add additional fields
        // $this->options['fname']             = '';

        return $this;
    }

}
