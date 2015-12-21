<?php

class User_Model_OrderEmailNotification extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_email_notification";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_order_status_order_email_notification_old" => array(
      "columns" => array("order_status_id_old"),
      "refTableClass" => "User_Model_OrderStatus",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_order_status_order_email_notification_new" => array(
      "columns" => array("order_status_id_new"),
      "refTableClass" => "User_Model_OrderStatus",
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
