<?php namespace Thrive\MailchimpModule\Http\Controller\Admin;

use Anomaly\SettingsModule\Setting\Form\SettingFormBuilder;
use Anomaly\Streams\Platform\Http\Controller\AdminController;


class SettingsController extends AdminController
{
    public function edit(SettingFormBuilder $form)
    {
        return $form->render('thrive.module.mailchimp');
    }
}
