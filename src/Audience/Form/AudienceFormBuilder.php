<?php namespace Thrive\MailchimpModule\Audience\Form;

use Anomaly\Streams\Platform\Message\MessageBag;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Thrive\MailchimpModule\Support\Integration\Audience;

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
        'audience_remote_id' => [
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
        // 'somefield'
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
                        'audience_name',
                        'audience_remote_id',
                    ],
                ],   
                'tab2' => [
                    'title'  => 'Fields',
                    'fields' => [
                        'audience_permission_reminder',
                        'audience_email_type_option',
                    ],
                ],  
                'tab3' => [
                    'title'  => 'Contact',
                    'fields' => [
                        'audience_contact_company_name',
                        'audience_contact_address1',
                        'audience_contact_city',
                        'audience_contact_state',
                        'audience_contact_zip',
                        'audience_contact_country'
                    ],
                ],  
                'tab4' => [
                    'title'  => 'Campaign',
                    'fields' => [
                        'audience_campaign_from_name',
                        'audience_campaign_from_email',
                        'audience_campaign_subject',
                        'audience_campaign_language',
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

        $audience_name                  = $this->getRequestValue('audience_name');
        $audience_list                  = $this->getRequestValue('audience_remote_id');

        $this->can_post_to_mailchimp    = true;

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
            if(Audience::Post($entry))
            {
                $messages->info('Successfully POSTED to Mailchimp');
            }
            else 
            {
                $messages->error('Failed to POST to Mailchimp');
            }
        }
        //$this->setFormResponse(redirect('admin/mailchimp/audiences'));
    }
}
