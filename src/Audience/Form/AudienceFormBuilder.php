<?php namespace Thrive\MailchimpModule\Audience\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Thrive\MailchimpModule\Support\Harmony;

/**
 * AudienceFormBuilder
 */
class AudienceFormBuilder extends FormBuilder
{

    /**
     * The form fields.
     *
     * @var array|string
     */
    protected $fields = [
        '*',
        'str_id' => [
            'disabled' => 'edit',
        ],
    ];

    /**
     * Additional validation rules.
     *
     * @var array|string
     */
    protected $rules = [];

    /**
     * Fields to skip.
     *
     * @var array|string
     */
    protected $skips = [
        'thrive_sync_status'
    ];

    /**
     * The form actions.
     *
     * @var array|string
     */
    protected $actions = [];

    /**
     * The form buttons.
     *
     * @var array|string
     */
    protected $buttons = [
        'cancel',
    ];

    /**
     * The form options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The form sections.
     *
     * @var array
     */
    protected $sections = [
        'metafield'   => [
            'stacked' => false,
            'tabs' => [
                'general' => [
                    'title'  => 'Audience',
                    'fields' => [
                        'name',
                        'str_id',
                    ],
                ],   
                'tab2' => [
                    'title'  => 'Fields',
                    'fields' => [
                        'permission_reminder',
                        'email_type_option',
                    ],
                ],  
                'tab3' => [
                    'title'  => 'Contact',
                    'fields' => [
                        'contact_company_name',
                        'contact_address1',
                        'contact_city',
                        'contact_state',
                        'contact_zip',
                        'contact_country'
                    ],
                ],  
                'tab4' => [
                    'title'  => 'Campaign',
                    'fields' => [
                        'campaign_from_name',
                        'campaign_from_email',
                        'campaign_subject',
                        'campaign_language',
                    ],
                ],                                                                
            ],
        ],        
    ];


    /**
     * The form assets.
     *
     * @var array
     */
    protected $assets = [];

    /**
     * @bool    flag tyo see if we can post to 
     *          mailchimp during the 
     *          saving process.
     */
    protected $can_post_to_mailchimp;



    /**
     * Check if all fields are valid
     * @todo - get newly posted values and validate them with MC
     *          If validation passes then continue to save locally
     */
    public function onSaving(MessageBag $messages)
    {

        $entry = $this->getFormEntry();

        $new_name                   = $this->getRequestValue('name');
        $new_str_id                 = $this->getRequestValue('str_id');

        $this->can_post_to_mailchimp = true;

    }
    
    /**
     * onSaved
     *
     * @param  mixed $messages
     * @return void
     */
    public function onSaved(MessageBag $messages)
    {
        $entry = $this->getFormEntry();

        if($this->can_post_to_mailchimp)
        {
            if($item = Harmony::updateOnMailchimp($entry))
            {
                $messages->info('Successfully POSTED to Mailchimp');
                $entry->update(['thrive_sync_status' => 'Updated']);
            }
            else 
            {
                $messages->error('Failed to POST to Mailchimp');
                $entry->update(['thrive_sync_status' => 'Post Failed']);

            }
        }

        //$this->setFormResponse(redirect('admin/mailchimp/audiences'));
   
    }
}
