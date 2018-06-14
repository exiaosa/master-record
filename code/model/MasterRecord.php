<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 2:24 PM
 */

class MasterRecord extends DataObject
{
    private static $db = array(
        'Email' => 'Varchar'
    );

    private static $many_many = array(
        "submissions" => 'MasterRecordSubmission'
    );

    private static $summary_fields=array(
        'Email' => 'Email'
    );
}