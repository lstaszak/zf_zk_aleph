<?php

class User_Model_OrderStatus extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_status";
  protected $_dependentTables = array("User_Model_OrderJournal", "User_Model_OrderEmailNotification");

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
