<?php


class MasterRecordExtension extends Extension
{
    private static $url_handlers = array(

    );

    private static $allowed_actions = array(

    );

    //hook master record function for User Defined Form
    public function updateEmailData($emailData, $attachments){

        $items = $emailData['Fields']->items;
        $email = '';
        $masterRecord='';
        $count = 0;

        foreach ($items as $item) {
            if (strpos($item->Name, 'Email') !== false) {
                if($item->Value !== NULL) {
                    $email = $item->Value;

                    $count++;

                    if (MasterRecord::get()->filter('Email', $email)->count() == 0) {
                        $masterRecord = new MasterRecord();
                    } else {
                        $masterRecord = MasterRecord::get()->filter('Email', $email)->first();
                    }
                }
            }
        }


        if($count == 0){
            return false;
        }else{
            foreach ($items as $item){//Debug::dump($item);die;
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

            $this->fileRecord($email);
        }

    }

    public function onAfterSubmit($data){

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

        $this->fileRecord($email);
    }

    /**
     * Function is to create the user html file for downloading
     * @return SS_HTTPResponse
     */
    public function fileRecord($email){
        /*$vars = $this->request->requestVars();
        $email = $vars['Email'];
        $siteConfig = MasterRecordConfig::current_config();*/

        $record = MasterRecord::get()->filter("Email",$email)->first();
        $content = "";

        $file = $email.'-info';

        if (!file_exists('../assets/UserData')) {
            mkdir('../assets/UserData', 0777, true);
        }

        //START generate the html file
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/assets/UserData/".$file.".html","wb");
        $head = '<!DOCTYPE html> <html> <head> <title>Personal info </title> '.
            '<style type="text/css"> body{ background:#e9ebee; font-family: Arial; } .header{ margin: 0 auto; width: 598px; background: #fff; border-radius: 3px; padding: 15px; color: #1d2129; font-size: 16px; margin-bottom:30px; font-weight: bold; } .item{ margin: 0 auto; width: 598px; background: #fff; border-radius: 3px;padding: 15px; margin-bottom:20px; color: #1d2129; font-size: 15px; } .title{ display: block; width:100%; color: #8d949e; font-size: 13px; line-height: 16px; padding:10px 0; border-top:1px solid #8d949e; } .title:first-child{ color:#1d2129;display: block; width:100%; font-weight: bold; padding-bottom:15px;padding-top:0;border-top: none; }.footer{clear: both; color: #7f7f7f; font-size: 14px; margin-bottom: 20px; margin-top: 10px; text-align: center;} </style>'.
            '</head> <body><div class="header">The personal info of user '. $email.'</div>';
        $foot = '<div class="footer">Generate on '.$record->Created.'</div></body></html>';
        fwrite($fp,$head);

        foreach ($record->submissions() as $submission){
            $class = $submission->RecordsClassName;
            $id = $submission->RecordID;
            $item = $class::get()->filter("ID",$id)->first();

            $item_head ='<div class="item">';
            fwrite($fp,$item_head);
            foreach($item->toMap() as $key=>$value){
                if($key !== 'ClassName' && $key !== 'RecordClassName'){
                    $content = '<div class="title">'.$value.'</div>';
                    fwrite($fp,$content);
                }
                //fwrite($fp,$content);
            }

            $item_foot ='</div>';
            fwrite($fp,$item_foot);
        }
        fwrite($fp,$foot);
        fclose($fp);

        //Zip File
        /*$fileName = '../assets/UserData/'.$file.'.zip';
        $files_to_zip = [dirname(__DIR__.'/../../assets/UserData/'.$file.'.html').'/'.$file.'.html'];
        $result = $this->createZip($files_to_zip, $fileName);

        if($result){
            unlink(dirname(__DIR__.'/../../assets/UserData/'.$file.'.html').'/'.$file.'.html');

        }*/
        $path ='assets/UserData/'. $file.'.html';

        return $path;

    }


    /**
     * Function is to zip the file
     * @param array $files
     * @param string $destination
     * @param bool $overwrite
     * @return bool
     */
    function createZip($files = array(), $destination = '', $overwrite = false) {

        if(file_exists($destination) && !$overwrite) { return false; }


        $validFiles = [];
        if(is_array($files)) {
            foreach($files as $file) {
                if(file_exists($file)) {
                    $validFiles[] = $file;
                }
            }
        }


        if(count($validFiles)) {
            $zip = new ZipArchive();
            if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }


            foreach($validFiles as $file) {

                $zip->addFile($file,$file);

            }

            $zip->close();

            return file_exists($destination);
        }else{
            return false;
        }
    }
}