<?php

class Admin_Model_MailMessage extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "mail_message";
  protected $_dependentTables = array("Admin_Model_Mail");
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
