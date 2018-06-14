<?php


class MasterRecordExtension extends Extension
{
    private static $url_handlers = array(

    );

    private static $allowed_actions = array(

    );

    public function updateEmailData($emailData, $attachments){//var_dump($this->owner->Link());die;

        $items = $emailData['Fields']->items;
        $email = '';

        foreach ($items as $item) {
            if ($item->Name == 'Email') {
                $email = $item->Value;

                if (MasterRecord::get()->filter('Email', $email)->count() == 0) {
                    $masterRecord = new MasterRecord();
                } else {
                    $masterRecord = MasterRecord::get()->filter('Email', $email)->first();
                }
            }
        }

        foreach ($items as $item){
            $id = $item->ID;
            $record_class = $item->RecordClassName;

            $submission = new MasterRecordSubmission();
            $submission->RecordsClassName = $record_class;
            $submission->RecordID = $id;
            $submission->write();

            $masterRecord->submissions()->add($submission);
            $masterRecord->Email = $email;
            $masterRecord->write();
        }

    }

    public function onAfterSubmit($data){

        /*$record_class = 'Page';
        $id = 736;
        $email = "jenny@webtorque.co.nz";*/
        $record_class = $data->ClassName;
        $id = $data->ID;
        $email = $data->Email;

        if(MasterRecord::get()->filter('Email',$email)->count() == 0){
            $masterRecord = new MasterRecord();
        }else{
            $masterRecord= MasterRecord::get()->filter('Email',$email)->first();
        }



        $submission = new MasterRecordSubmission();
        $submission->RecordsClassName = $record_class;
        $submission->RecordID = $id;
        $submission->write();

        $masterRecord->submissions()->add($submission);
        $masterRecord->Email = $email;
        $masterRecord->write();
    }


}