<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 2:24 PM
 */

class UserRequest extends DataObject
{
    private static $db = array(
        'Email' => 'Varchar',
        'Name' => 'Varchar',
        'Type' => "Enum('Retrieve,Delete','Retrieve')",
        "Encode"=> 'Text',
        "IsViewed" => 'Boolean'
    );

    private static $many_many = array(

    );

    private static $summary_fields=array(
        'Name'=> 'Name',
        "Email" => "Email",
        "Type" => "Type"
    );




    public function getCMSActions() {
        $actions = parent::getCMSActions();

        $deleteAction = new FormAction ('doDeleteRecord', 'Delete Master Record');
        $deleteAction->addExtraClass('ss-ui-action-constructive');
        $actions->push($deleteAction);

        $actions->push(LiteralField::create(
            'DownloadLink',
            '<a href="assets/UserData/' . $this->Email . '-info.html" target="_blank" class="ss-ui-button" download>Download the file</a>'
        ));

        return $actions;
    }
}