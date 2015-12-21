<?php

class Borrower_Model_OrderCart extends AppCms2_Controller_Plugin_TableAbstract
{
  protected $_name = "order_cart";
  protected $_dependentTables = array("Borrower_Model_OrderJournalOrderCart");
  protected $_referenceMap = array(
    "User" => array(
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

  public function addOrderCart($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oSelect = $this->select();
      $oSelect->where("user_id = ?", $nUserId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->id;
      else
        return $this->addRow(array("user_id" => $nUserId));
    }
    return null;
  }

  public function getOrderCartId($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oSelect = $this->select();
      $oSelect->where("user_id = ?", $nUserId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->id;
    }
    return null;
  }
}

?>
