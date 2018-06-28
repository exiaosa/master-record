<?php

class MasterRecordConfig extends DataObject implements PermissionProvider
{
    private static $db = array(
        'ButtonText' => 'Varchar',
        'SubmitSuccessMessage' => 'HTMLText',
        'EmailTitle' => 'Varchar',
        'EmailFrom' => 'Varchar',
        'EmailBodyContent'=> 'HTMLText',
        'DeleteSuccesseMessage'=> 'HTMLText',
        'DeleteNote'=> 'HTMLText',
        'DisableDisplayMessage' => 'HTMLText',
        'Terms'=> 'HTMLText'

    );

    private static $many_many = array(

    );

    private static $many_many_extraFields = array(

    );

    public function getCMSFields()
    {
        $fields = new FieldList(
            new TabSet("Root",
                new Tab('Main',
                    HtmlEditorField::create('Terms','GDPR Terms & Conditions')->setRows(10)
                ),
                new Tab('Email',
                    TextField::create('EmailTitle','Email Subject'),
                    TextField::create('EmailFrom','Email From'),
                    HtmlEditorField::create('EmailBodyContent', 'User Email Body Content')->setRows(10),
                    TextField::create('ButtonText','Submit Button Text'),
                    HtmlEditorField::create('SubmitSuccessMessage', 'Submit Success Message')->setRows(10)
                ),
                new Tab('User Request',
                    HtmlEditorField::create('DeleteNote', 'User Delete Action Note')->setRows(10),
                    HtmlEditorField::create('DeleteSuccesseMessage', 'User Delete Success Message')->setRows(10),
                    HtmlEditorField::create('DisableDisplayMessage', 'User Disable Display Message')->setRows(10)
                )
            )
        );

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public static function current_config()
    {
        $config = MasterRecordConfig::get()->first();
        if (!$config) {
            $config = MasterRecordConfig::create();
            $config->write();
        }
        return $config;
    }

    public function providePermissions()
    {
        return array(
            'EDIT_SITECONFIG' => array(
                'name' => _t('SiteConfig.EDIT_PERMISSION', 'Manage site configuration'),
                'category' => _t('Permissions.PERMISSIONS_CATEGORY', 'Roles and access permissions'),
                'help' => _t('SiteConfig.EDIT_PERMISSION_HELP', 'Ability to edit global access settings/top-level page permissions.'),
                'sort' => 400
            )
        );
    }

    public function getCMSActions()
    {
        if (Permission::check('ADMIN') || Permission::check('EDIT_SITECONFIG')) {
            $actions = new FieldList(
                FormAction::create('save_siteconfig', _t('CMSMain.SAVE', 'Save'))->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
            );
        } else {
            $actions = new FieldList();
        }

        $this->extend('updateCMSActions', $actions);
        return $actions;
    }
}