<?php

class Admin_Model_ChatMessage extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "chat_message";
  protected $_dependentTables = array(
    "Admin_Model_Chat"
  );
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
