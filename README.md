# SilverStripe Master Record Module

A module that add user form and user form record page.

- create a user form to submit request and the email with record page link would be sent to user 
- Users can retrieve and choose to delete their records



1. When install this module, the success message and form submit text can be edited in CMS

2. If you want to customize MasterRecordPage.ss, you can create you own  MasterRecordPage.ss in themes

3. Add the following in your form action to save submission record:

        $this->extend('onAfterSubmit',$data);
    
