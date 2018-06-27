<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 3:58 PM
 */

class UserRequestAdmin extends ModelAdmin
{
    private static $managed_models = array('UserRequest','MasterRecord');
    private static $url_segment = 'user-request';
    private static $menu_title = 'Master Record & User Request';


    public function init(){
        parent::init();

        Requirements::javascript("master-record/js/LeftAndAdmin.js");
        Requirements::css("master-record/css/LeftAndAdmin.css");
    }


    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);


        if($this->modelClass == 'UserRequest'){
            $listField = $form->Fields()->fieldByName($this->modelClass);
            if ($gridField = $listField->getConfig()->getComponentByType('GridFieldDetailForm'))
                $gridField->setItemRequestClass('RequestFieldDetailForm_ItemRequest');

            $listField->getConfig()->addComponent(new GridFieldFilterHeader());
            //$listField->getConfig()->addComponent(new GridFieldDeleteDataAction());
            //$listField->getConfig()->addComponent(new GridFieldDownloadAction());

            $listField->getConfig()->removeComponentsByType("GridFieldAddNewButton");
            //$listField->getConfig()->removeComponentsByType("GridFieldEditButton");
            $listField->getConfig()->removeComponentsByType("GridFieldDeleteAction");

        }else if($this->modelClass == 'MasterRecord') {
            $listField = $form->Fields()->fieldByName($this->modelClass);

            //$listField->getConfig()->addComponent(new GridFieldDeleteDataAction());
            $listField->getConfig()->addComponent(new GridFieldViewAction());
            $listField->getConfig()->addComponent(new GridFieldDownloadAction());

            $listField->getConfig()->removeComponentsByType("GridFieldAddNewButton");
            $listField->getConfig()->removeComponentsByType("GridFieldEditButton");
            $listField->getConfig()->removeComponentsByType("GridFieldDeleteAction");
        }

        return $form;
    }


}

class RequestFieldDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest {

    private static $allowed_actions = array (
        'edit',
        'view',
        'ItemEditForm'
    );

    public function ItemEditForm() {
        $form = parent::ItemEditForm();//var_dump($form);die;
        $formActions = $form->Actions();

        if ($actions = $this->record->getCMSActions())
            foreach ($actions as $action)
                $formActions->push($action);

        return $form;
    }

    /**
     * Gets the top level controller.
     *
     * @return Controller
     * @todo  This had to be directly copied from {@link GridFieldDetailForm_ItemRequest} because it is a protected method and not visible to a decorator!
     */
    protected function getToplevelController() {
        $c = $this->owner->getController();
        while($c && $c instanceof GridFieldDetailForm_ItemRequest) {
            $c = $c->getController();
        }
        return $c;
    }

    /**
     * Gets the back link
     *
     * @return  string
     * @todo  This had to be directly copied from {@link GridFieldDetailForm_ItemRequest} because it is a protected method and not visible to a decorator!
     */
    protected function getBackLink(){
        // TODO Coupling with CMS
        $backlink = '';
        $toplevelController = $this->getToplevelController();
        if($toplevelController && $toplevelController instanceof LeftAndMain) {
            if($toplevelController->hasMethod('Backlink')) {
                $backlink = $toplevelController->Backlink();
            } elseif($this->owner->getController()->hasMethod('Breadcrumbs')) {
                $parents = $this->owner->getController()->Breadcrumbs(false)->items;
                $backlink = array_pop($parents)->Link;
            }
        }
        if(!$backlink) $backlink = $toplevelController->Link();

        return $backlink;
    }


    public function doDeleteRecord($data, $form) {//var_dump($this->getRequest());die;
        if($data['Type'] == "Delete"){
            $request = UserRequest::get()->filter("Email",$data['Email'])->first();
            $request->Type = "Delete";
            $master_record = MasterRecord::get()->filter("Email",$data['Email'])->first();


            foreach($master_record->submissions() as $submission){

                $class = $submission->RecordsClassName;
                $id = $submission->RecordID;
                $item = $class::get()->filter("ID",$id)->first();

                $item->delete();
                $submission->delete();
            }
            $master_record->delete();
            $request->delete();

            $backLink = $this->getBacklink();
            /*Controller::curr()->getResponse()->setStatusCode(
                200,
                'Records are deleted.'
            );*/
            //return Controller::curr()->redirect($this->getBackLink());

            //when an item is deleted, redirect to the parent controller
            $controller = $this->getToplevelController();
            $controller->getRequest()->addHeader('X-Pjax', 'Content'); // Force a content refresh

            return $controller->redirect($backLink, 302); //redirect back to admin section
        }

        Controller::curr()->getResponse()->setStatusCode(
            405,
            'The record can only be delete when it is in DELETE Type.'
        );

        return Controller::curr()->redirect($this->owner->Link());
    }

}