<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 3:58 PM
 */

class MasterRecordAdmin extends ModelAdmin
{
    private static $managed_models = array('MasterRecord');
    private static $url_segment = 'master-record';
    private static $menu_title = 'Manage Master Record';
}