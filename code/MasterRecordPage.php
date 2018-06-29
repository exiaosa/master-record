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
        Requirements::css('master-record/css/main.css');
        Requirements::javascript('master-record/js/main.js');

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
        $user = '';

        $URI = "/master-record-page";
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$URI";
        $encode = base64_encode(date("Y-m-d").'/'.$address);

        if(UserRequest::get()->filter('Email',$address)->count() == 0){
            $data['Encode'] = $encode;
            $data['IsViewed'] = FALSE;
            $user = UserRequest::create();
            $user->update($data);
            $user->write();

        }else{
            $user = UserRequest::get()->filter('Email',$address)->first();
            $user->Encode = $encode;
            $user->IsViewed = FALSE;
            $user->write();
        }


        $this->extend('onAfterSubmit',$user);

        $email = Email::create();
        $email->setTo(trim($address));
        $email->setFrom($siteConfig->EmailFrom);
        $email->setSubject($siteConfig->EmailTitle);
        $email->setBody($siteConfig->EmailBodyContent. 'You can Click <a href="'.$actual_link.'/?view='.$encode.'">Here</a> to view your records.');

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

        if($request){
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

        return $this->customise(array(
            'ShowRecord' => FALSE,
            'Error' => 'The info does not exist anymore!'
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
        $pages->setPageLength(18);

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
     * Function is get html file for downloading
     * @return SS_HTTPResponse
     */
    public function fileRecord(){
        $vars = $this->request->requestVars();
        $email = $vars['Email'];


        $file = $email.'-info';


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

    /**
     * Handles returning a JSON response, makes sure Content-Type header is set
     *
     * @param array $array
     * @param bool $isJson Is the passed string already a json string
     * @return SS_HTTPResponse
     */
    public function jsonResponse($array, $isJson = false)
    {
        $json = $array;
        if (!$isJson) {
            $json = Convert::raw2json($array);
        }

        $response = new SS_HTTPResponse($json);
        $response->addHeader('Content-Type', 'application/json');
        $response->addHeader('Vary', 'Accept');

        return $response;
    }

}