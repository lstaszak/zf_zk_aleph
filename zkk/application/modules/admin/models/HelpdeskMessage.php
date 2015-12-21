<?php

class Admin_Model_HelpdeskMessage extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "helpdesk_message";
  protected $_dependentTables = array("Admin_Model_Helpdesk");
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
