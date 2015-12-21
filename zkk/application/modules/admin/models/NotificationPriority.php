<?php

class Admin_Model_NotificationPriority extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "notification_priority";
  protected $_dependentTables = array("Admin_Model_Notification");
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
