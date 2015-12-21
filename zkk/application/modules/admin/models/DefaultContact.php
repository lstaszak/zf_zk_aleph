<?php

class Admin_Model_DefaultContact extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "default_contact";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_user_default_contact" => array(
      "columns" => array("user_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
