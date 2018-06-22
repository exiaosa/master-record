<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/16/2018
 * Time: 10:05 AM
 */

class GridFieldSyncAction implements GridField_ColumnProvider, GridField_ActionProvider
{

    public function augmentColumns($gridField, &$columns)
    {
        if(!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array('class' => 'col-buttons');
    }

    public function getColumnMetadata($gridField, $columnName) {
        if($columnName == 'Actions') {
            return array('title' => '');
        }
    }

    public function getColumnsHandled($gridField) {
        return array('Actions');
    }

    public function getColumnContent($gridField, $record, $columnName)
    {
        if(!$record->canEdit()) return;

        $field = GridField_FormAction::create($gridField,  'SyncData'.$record->ID, 'Sync', "syncdata",
            array('RecordID' => $record->ID,
                'Link' => Controller::join_links($gridField->Link('item'), $record->ID, 'edit')
            )
        );

        return $field->Field();
    }

    public function getActions($gridField)
    {
        return array('syncdata');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if($actionName == 'syncdata') {
            // perform your action here
            $item = $gridField->getList()->byID($arguments['RecordID']);

            $item->write();


            // output a success message to the user
            Controller::curr()->getResponse()->setStatusCode(
                200,
                'Data has been updated.'
            );
        }
    }
}


class GridFieldDeleteDataAction implements GridField_ColumnProvider, GridField_ActionProvider
{

    public function augmentColumns($gridField, &$columns)
    {
        if(!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array('class' => 'col-buttons');
    }

    public function getColumnMetadata($gridField, $columnName) {
        if($columnName == 'Actions') {
            return array('title' => '');
        }
    }

    public function getColumnsHandled($gridField) {
        return array('Actions');
    }

    public function getColumnContent($gridField, $record, $columnName)
    {
        if(!$record->canEdit()) return;

        $field = GridField_FormAction::create($gridField,  'DeleteData'.$record->ID, 'Delete', "deletedata",
            array('RecordID' => $record->ID,
                'Link' => Controller::join_links($gridField->Link('item'), $record->ID, 'edit')
            )
        )->addExtraClass('delete-data');

        return $field->Field();
    }

    public function getActions($gridField)
    {
        return array('deletedata');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if($actionName == 'deletedata') {
            // perform your action here
            $email = $gridField->getList()->byID($arguments['RecordID'])->Email;

            $userData = UserData::get()->filter("Email", $email)->first();

            foreach ($userData->Order() as $item) {
                $item->MemberID = 0;
                $item->IsUnlinked = TRUE;
                $item->write();
            }

            foreach ($userData->ContactMessage() as $item) {
                $item->delete();
            }

            foreach ($userData->NewsletterSubscriber() as $item) {
                $item->delete();
            }


            // output a success message to the user
            Controller::curr()->getResponse()->setStatusCode(
                200,
                'Contact and Subscriber records are deleted..'
            );
        }
    }
}


class GridFieldDownloadAction implements GridField_ColumnProvider, GridField_ActionProvider
{

    public function augmentColumns($gridField, &$columns)
    {
        if(!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array('class' => 'col-buttons');
    }

    public function getColumnMetadata($gridField, $columnName) {
        if($columnName == 'Actions') {
            return array('title' => '');
        }
    }

    public function getColumnsHandled($gridField) {
        return array('Actions');
    }

    public function getColumnContent($gridField, $record, $columnName) {
        // No permission checks, handled through GridFieldDetailForm,
        // which can make the form readonly if no edit permissions are available.

        $email = $gridField->getList()->byID($record->ID)->Email;

        $data = new ArrayData(array(
            'Link' => 'assets/UserData/'.$email.'-info.html'
        ));

        return $data->renderWith('GridFieldDownloadButton');
    }

    public function getActions($gridField)
    {

    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {

    }
}

