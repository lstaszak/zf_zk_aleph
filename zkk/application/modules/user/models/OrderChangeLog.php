<?php

class User_Model_OrderChangeLog extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_change_log";
  protected $_dependentTables = array("User_Model_OrderJournalOrderChangeLog");
  protected $_referenceMap = array(
    "fk_user_order_change_log" => array(
      "columns" => array("user_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_order_change_type_order_change_log" => array(
      "columns" => array("order_change_type_id"),
      "refTableClass" => "User_Model_OrderChangeType",
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
