<?php

class Admin_Model_XSession extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "x_session";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_user_x_session" => array(
      "columns" => array("user_recipient_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
