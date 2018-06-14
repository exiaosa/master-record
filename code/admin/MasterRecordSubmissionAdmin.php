<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 3:58 PM
 */

class MasterRecordSubmissionAdmin extends ModelAdmin
{
    private static $managed_models = array('MasterRecordSubmission');
    private static $url_segment = 'master-submission';
    private static $menu_title = 'Manage Master Submission';
}