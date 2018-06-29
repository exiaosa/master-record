# SilverStripe Master Record Module

A module that add user form and user form record page.

- create a user form to submit request and the email with record page link would be sent to user 
- Users can retrieve and choose to delete their records
- All the master records are based on the email, so the form must contain "Email" Field when applying this module 



1. When install this module, the success message and form submit text can be edited in CMS Master Record Setting

2. If you want to customize MasterRecordPage.ss, you can create you own  MasterRecordPage.ss in themes

3. Add the following code in your form action to save submission record:

        $this->extend('onAfterSubmit',$data); //$data is the saved dataobejct
       
4. If you want to apply this module to User Defined Form, the form must contains the Email field (Edit the Field Name as "Email" or "Email_NUM")    
5. All the user can receive an email with a link after sending request, the link can only be viewed once for secure reason     

6. The consent terms can be edited in CMS. 
   In your form action, add following code:

        LiteralField::create("Terms",MasterRecordConfig::current_config()->dbObject('Terms')->forTemplate())
   In UserForm, add a new Field called "Form Terms"    
        
    
