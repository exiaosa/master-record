<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 2:21 PM
 */

class MasterRecordSubmission extends DataObject
{
    private static $db = array(
        'RecordsClassName' => 'Varchar',
        'RecordID' => 'Int',
    );

    private static $belongs_many_many = [
        "MasterRecords" => "MasterRecord",
    ];

}