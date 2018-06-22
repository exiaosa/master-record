<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/22/2018
 * Time: 3:03 PM
 */

class GridFieldSyncButton implements GridField_HTMLProvider, GridField_ActionProvider, GridField_URLHandler {

    protected $targetFragment;

    /**
     * @param string $targetFragment The HTML fragment to write the button into
     * @param array $syncColumns The columns to include in the sync view
     */
    public function __construct($targetFragment = "before", $syncColumns = null) {
        $this->targetFragment = $targetFragment;

    }

    public function augmentColumns($field, &$cols) {
        if(!in_array('Actions', $cols)) $cols[] = 'Actions';
    }

    public function getColumnsHandled($field) {
        return array('Actions');
    }


    /**
     * Sync is an action button.
     *
     * @param GridField
     *
     * @return array
     */
    public function getActions($gridField) {
        return array('sync');
    }


    /**
     * Handle the sync action.
     *
     * @param GridField
     * @param string
     * @param array
     * @param array
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if($actionName == 'sync') {
            return $this->handleSync($gridField);
        }
    }

    /**
     * Sync is accessible via the url
     *
     * @param GridField
     * @return array
     */
    public function getURLHandlers($gridField) {
        return array(
            'sync' => 'handleSync',
        );
    }

    /**
     * Handle the sync, for both the action button and the URL
     */
    public function handleSync($gridField, $request = null) {

        /* $list = UserData::get();
         foreach ($list as $item){
             $item->write();
         }*/

        $emails = [];
        //array_push($emails,'lastharris@xtra.co.nz');

        $contacts = ContactMessage::get();
        foreach ($contacts as $item){
            array_push($emails,$item->Email);
        }

        $members = Member::get();
        foreach ($members as $item){
            array_push($emails,$item->Email);
        }

        $orders = Order::get();
        foreach ($orders as $item){
            array_push($emails,$item->Email);
        }

        $contacts = NewsletterSubscriber::get();
        foreach ($contacts as $item){
            array_push($emails,$item->Email);
        }

        $emails = array_unique($emails);
        $datas = UserData::get();
        foreach ($datas as $data){
            $data->delete();
        }

        foreach (array_filter($emails) as $email){
            $userdata = new UserData();
            $userdata->Email = $email;
            $userdata->write();
        }
    }

    public function getHTMLFragments($gridField) {
        $button = new GridField_FormAction(
            $gridField,
            'sync',
            'Sync All Records',
            'sync',
            null
        );


        return array(
            $this->targetFragment => '<p>' . $button->Field() . '</p>',
        );
    }

    public function getColumnAttributes($field, $record, $col) {
        return array('class' => 'col-buttons');
    }

    public function getColumnMetadata($gridField, $col) {
        return array('title' => null);
    }
}
