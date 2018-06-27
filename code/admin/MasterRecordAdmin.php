<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 3:58 PM
 */

/*class MasterRecordAdmin extends ModelAdmin
{
    private static $managed_models = array('MasterRecord');
    private static $url_segment = 'master-record';
    private static $menu_title = 'Master Record History';

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);


        $listField = $form->Fields()->fieldByName($this->modelClass);
        if ($gridField = $listField->getConfig()->getComponentByType('GridFieldDetailForm'))
            $gridField->setItemRequestClass('RequestFieldDetailForm_ItemRequest');

        $listField->getConfig()->addComponent(new GridFieldFilterHeader());
        //$listField->getConfig()->addComponent(new GridFieldDeleteDataAction());
        $listField->getConfig()->addComponent(new GridFieldViewAction());
        $listField->getConfig()->addComponent(new GridFieldDownloadAction());

        $listField->getConfig()->removeComponentsByType("GridFieldAddNewButton");
        $listField->getConfig()->removeComponentsByType("GridFieldEditButton");
        $listField->getConfig()->removeComponentsByType("GridFieldDeleteAction");

        return $form;
    }

}*/