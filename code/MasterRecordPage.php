<?php
/**
 * Created by PhpStorm.
 * User: jenny
 * Date: 6/7/2018
 * Time: 12:51 PM
 */

class MasterRecordPage extends Page
{
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        if(!MasterRecordPage::get()->first()){
            $masterRecordPage = MasterRecordPage::create();
            $masterRecordPage->Title = 'Master Record Page';
            $masterRecordPage->write();
            $masterRecordPage->doPublish();
        }
    }

    private static $db = array(
        'Description'=>'HTMLText'
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields(); // TODO: Change the autogenerated stub

        $fields->addFieldsToTab('Root.Main',array(
            HtmlEditorField::create('Description','Description')
        ));

        return $fields;
    }
}

class MasterRecordPage_Controller extends Page_Controller
{
    private static $allowed_actions = array(
        'UserRecordSubmitForm',
        'doSubmission',
        'success',
        'view',
        'deleteRecord',
        'fileRecord',
        'fileClean'
    );

    public function init()
    {
        parent::init();
        Requirements::css('master-records/css/main.css');
        Requirements::javascript('master-records/js/main.js');

    }

    public function index()
    {

        return $this->render();
    }

    public function UserRecordSubmitForm()
    {
        $form = UserRecordSubmitForm::create($this);

        return $form;
    }


    public function doSubmission($data, $form){
        $name = $data['Name'];
        $address = $data['Email'];
        $siteConfig = MasterRecordConfig::current_config();

        $URI = "/master-record-page";
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$URI";
        $encode = base64_encode(date("Y-m-d").'/'.$address);

        if(UserRequest::get()->filter('Email',$address)->count() == 0){
            $data['Encode'] = $encode;
            $data['IsViewed'] = FALSE;
            $user = UserRequest::create();
            $user->update($data);
            $user->write();

            $this->extend('onAfterSubmit',$user);
        }else{
            $user = UserRequest::get()->filter('Email',$address)->first();
            $user->Encode = $encode;
            $user->IsViewed = FALSE;
            $user->write();
        }


        $email = Email::create();
        $email->setTo(trim($address));
        $email->setFrom($siteConfig->EmailFrom);
        $email->setSubject($siteConfig->EmailTitle);
        $email->setBody($siteConfig->EmailBodyContent. 'You can view this at <a href="'.$actual_link.'/?view='.$encode.'"</a>');

        $email->send();

        return $this->redirect($this->Link() . '?success=1');
    }

    public function IsSuccess(){

        if(array_key_exists("success",$this->getRequest()->getVars())){
            return $this->getRequest()->getVars()["success"];
        }

    }

    public function IsRecord(){

        if(array_key_exists("view",$this->getRequest()->getVars())){
            Return TRUE;
        }

    }

    public function getUserInfo(){

        $code = $this->getRequest()->getVars()["view"];
        $decode = base64_decode($code);//var_dump(base64_encode(date("Y-m-d").'/'.$email));
        $email = substr($decode, strpos($decode, "/") + 1);

        $request = UserRequest::get()->filter('Email',$email)->first();

        if(Session::get('Validview') !== NULL){
            $request->IsViewed = FALSE;
            //$request->write();
        }

        if($request->IsViewed == 1 ){

            if(Session::get('Validview') == NULL){
                //$request->IsViewed = FALSE;
                $siteConfig = MasterRecordConfig::current_config();
                return $this->customise(array(
                    'ShowRecord' => FALSE,
                    'Error' => $siteConfig->DisableDisplayMessage
                ));
            }

        }
        Session::set('Validview','view');
        $request->IsViewed = TRUE;
        $request->write();

        return $this->customise(array(
            'Email' => $email,
            'ShowRecord' => TRUE
        ));


    }

    /**
     * Function is to get user record config
     * @return DataObject|static
     */
    public function getMasterRecordConfig(){
        return $siteConfig = MasterRecordConfig::current_config();

    }

    /**
     * Function is to get user record by email
     * @param $email
     * @return PaginatedList
     */
    public function getUserRecords($email){
        $record = MasterRecord::get()->filter("Email",$email)->first();

        $pages = new PaginatedList($record->submissions(), $this->getRequest());
        $pages->setPageLength(12);

        return $pages;
    }


    /**
     * Function is to save the DELETE request
     * @return SS_HTTPResponse
     * @throws ValidationException
     */
    public function deleteRecord(){
        $vars = $this->request->requestVars();
        $email = $vars['Email'];
        $siteConfig = MasterRecordConfig::current_config();

        $request = UserRequest::get()->filter('Email',$email)->first();
        $request->Type = "Delete";
        $request->write();

        if ($this->request->isAjax()) {
            return $this->jsonResponse(array(
                'Status' => 1,
                'Message'=> $siteConfig->DeleteSuccesseMessage
            ));
        }
    }


    /**
     * Function is to create the user html file for downloading
     * @return SS_HTTPResponse
     */
    public function fileRecord(){
        $vars = $this->request->requestVars();
        $email = $vars['Email'];
        $siteConfig = MasterRecordConfig::current_config();

        $record = MasterRecord::get()->filter("Email",$email)->first();
        $content = "";

        $file = date("Ymdhisa").'-'.$email;


        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/assets/UserData/".$file.".html","wb");
        $head = '<!DOCTYPE html> <html> <head> <title>Personal info </title> '.
        '<style type="text/css"> body{ background:#e9ebee; font-family: Arial; } .header{ margin: 0 auto; width: 598px; background: #fff; border-radius: 3px; padding: 15px; color: #1d2129; font-size: 16px; margin-bottom:30px; font-weight: bold; } .item{ margin: 0 auto; width: 598px; background: #fff; border-radius: 3px;padding: 15px; margin-bottom:20px; color: #1d2129; font-size: 15px; } .title{ display: block; width:100%; color: #8d949e; font-size: 13px; line-height: 16px; padding:10px 0; border-top:1px solid #8d949e; } .title:first-child{ color:#1d2129;display: block; width:100%; font-weight: bold; padding-bottom:15px;padding-top:0;border-top: none; }.footer{clear: both; color: #7f7f7f; font-size: 14px; margin-bottom: 20px; margin-top: 10px; text-align: center;} </style>'.
        '</head> <body><div class="header">The personal info of user'. $email.'</div>';
        $foot = '<div class="footer">Generate on '.$record->Created.'</div></body></html>';
        fwrite($fp,$head);

        foreach ($record->submissions() as $submission){
            $class = $submission->RecordsClassName;
            $id = $submission->RecordID;
            $item = $class::get()->filter("ID",$id)->first();

            $item_head ='<div class="item">';
            fwrite($fp,$item_head);
            foreach($item->toMap() as $info){

                $content = '<div class="title">'.$info.'</div>';


                fwrite($fp,$content);
            }

            /*$content = '<div class="item">'.
                            '<div class="title">Origin:'.$item->ClassName.'</div>'.
                            '<div class="date">Date:'.$submission->Created.'</div>'.
                       '</div>';*/

            //fwrite($fp,$content);

           $item_foot ='</div>';
           fwrite($fp,$item_foot);
        }
        fwrite($fp,$foot);
        fclose($fp);




        $fileName = '../assets/UserData/'.$file.'.zip';

        $files_to_zip = [dirname(__DIR__.'/../../assets/UserData/'.$file.'.html').'/'.$file.'.html'];

        $result = $this->createZip($files_to_zip, $fileName);

        if($result){
            unlink(dirname(__DIR__.'/../../assets/UserData/'.$file.'.html').'/'.$file.'.html');

        }

        if ($this->request->isAjax()) {
            return $this->jsonResponse(array(
                'Status' => 1,
                'FilePath'=> $file.".zip"
            ));
        }
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


            foreach($validFiles as $file) {//var_dump($file);die;

                $zip->addFile($file,$file);

            }

            $zip->close();

            return file_exists($destination);
        }else{
            return false;
        }
    }

}