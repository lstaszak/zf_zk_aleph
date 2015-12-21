<?php

class User_Model_OrderJournalOrderChangeLog extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_journal_order_change_log";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_order_journal_order_journal_order_change_log" => array(
      "columns" => array("order_journal_id"),
      "refTableClass" => "User_Model_OrderJournal",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_order_change_log_order_journal_order_change_log" => array(
      "columns" => array("order_change_log_id"),
      "refTableClass" => "User_Model_OrderChangeLog",
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
