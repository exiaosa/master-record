<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 11:04 AM
 */

class UserRecordSubmitForm extends Form
{

    private static $allowed_actions = array(
        'UserRecordSubmitForm',
        'doSubmission',
        'success'
    );

    public function __construct($controller, $fields = null, $actions = null, $required = null)
    {
        $siteConfig = MasterRecordConfig::current_config();

        $fields = new FieldList();
        $fields = $this->addFields($fields);


        $actions = new FieldList(new FormAction('doSubmission', $siteConfig->ButtonText));

        $required = new RequiredFields('Name', 'Email');

        parent::__construct($controller, 'UserRecordSubmitForm', $fields, $actions, $required);
    }

    public function addFields(FieldList $fields)
    {

        $fields->push(TextField::create('Name', 'Name'));
        $fields->push(EmailField::create('Email','Email'));
        $fields->push(LiteralField::create('Terms',MasterRecordConfig::current_config()->Terms));

        return $fields;
    }




}