<?php

class User_Model_OrderPayment extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_payment";
  protected $_dependentTables = array("User_Model_OrderJournal", "User_Model_OrderPaymentHistory", "User_Model_Response");
  protected $_referenceMap = array(
    "fk_user_order_payment" => array(
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

  public function getUserPayment($nId)
  {
    $oRow = $this->getRow($nId);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      return $oRow->user_id;
    }
    return null;
  }

  public function findOrderPayment($sOrderId, $sSessionId, $nAmount)
  {
    $oSelect = $this->select();
    $oSelect->where("order_id = ?", $sOrderId);
    $oSelect->where("session_id = ?", $sSessionId);
    $oSelect->where("amount = ?", $nAmount);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

  public function setDateIsStarting($sOrderId, $sSessionId, $nAmount)
  {
    $oSelect = $this->select();
    $oSelect->where("order_id = ?", $sOrderId);
    $oSelect->where("session_id = ?", $sSessionId);
    $oSelect->where("amount = ?", $nAmount);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->is_starting = 1;
      $oRow->date_is_starting = time();
      return $oRow->save();
    }
    return null;
  }

  public function setDateIsEnding($sOrderId, $sSessionId, $nAmount)
  {
    $oSelect = $this->select();
    $oSelect->where("order_id = ?", $sOrderId);
    $oSelect->where("session_id = ?", $sSessionId);
    $oSelect->where("amount = ?", $nAmount);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->is_ending = 1;
      $oRow->date_is_ending = time();
      return $oRow->save();
    }
    throw new Zend_Exception();
  }

  public function getDateIsEnding($sOrderId, $sSessionId, $nAmount)
  {
    $oSelect = $this->select();
    $oSelect->where("order_id = ?", $sOrderId);
    $oSelect->where("session_id = ?", $sSessionId);
    $oSelect->where("amount = ?", $nAmount);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->is_ending;
    throw new Zend_Exception();
  }

}

?>
