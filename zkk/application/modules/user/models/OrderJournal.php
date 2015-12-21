<?php

class User_Model_OrderJournal extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_journal";
  protected $_dependentTables = array("Borrower_Model_OrderJournalOrderCart", "User_Model_OrderPaymentHistory", "User_Model_OrderJournalOrderChangeLog");
  protected $_referenceMap = array(
    "fk_user_order_journal" => array(
      "columns" => array("user_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_order_status_order_journal" => array(
      "columns" => array("order_status_id"),
      "refTableClass" => "User_Model_OrderStatus",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_order_payment_order_journal" => array(
      "columns" => array("order_payment_id"),
      "refTableClass" => "User_Model_OrderPayment",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::SET_NULL
    ),
    "fk_order_file_order_journal" => array(
      "columns" => array("order_file_id"),
      "refTableClass" => "User_Model_OrderFile",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::SET_NULL
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getOrderStatus($nOrderId)
  {
    if (is_numeric($nOrderId)) {
      $oRow = $this->find($nOrderId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->order_status_id;
      }
    }
    return null;
  }

  public function changeStatus($nOrderId, $nNewOrderStatusId, $nOrderStatusIdIsFinish = null)
  {
    if (is_numeric($nOrderId)) {
      $oRow = $this->find($nOrderId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $nOldOrderStatusId = $oRow->order_status_id;
        if ($nOldOrderStatusId === 1 && $nNewOrderStatusId === 2) {
          $nExpirationDate = null;
        } else if ($nOldOrderStatusId === 2 && $nNewOrderStatusId === 3) {
          $nExpirationDate = mktime(23, 59, 59, date("m"), date("d") + 7, date("Y"));
        } else if ($nOldOrderStatusId === 3 && $nNewOrderStatusId === 4) {
          $nExpirationDate = null;
        } else if ($nOldOrderStatusId === 4 && $nNewOrderStatusId === 5) {
          $nExpirationDate = mktime(23, 59, 59, date("m"), date("d") + 5, date("Y"));
        } else if ($nOldOrderStatusId === 5 && $nNewOrderStatusId === 6) {
          $nExpirationDate = mktime(23, 59, 59, date("m"), date("d") + 10, date("Y"));
        } else if ($nOldOrderStatusId === 6 && $nNewOrderStatusId === 7) {
          $nExpirationDate = null;
        } else if ($nOldOrderStatusId === 2 && $nNewOrderStatusId === 2) {
          $nExpirationDate = null;
        }
        $oRow->order_status_id = $nNewOrderStatusId;
        $oRow->order_status_id_is_finish = $nOrderStatusIdIsFinish;
        $oRow->modified_date = time();
        $oRow->expiration_date = $nExpirationDate;
        $nOrderId = $oRow->save();
        $oMail = new AppCms2_Controller_Plugin_Mail();
        $oMail->sendBorrowerOrderStatusInfo($nOrderId, $nOldOrderStatusId);
        return $nOrderId;
      }
    }
    return null;
  }

  public function changeStatusCancel($nOrderId, $nNewOrderStatusId, $nOrderStatusIdIsFinish = null)
  {
    if (is_numeric($nOrderId)) {
      $oRow = $this->find($nOrderId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oMail = new AppCms2_Controller_Plugin_Mail();
        $nOldOrderStatusId = $oRow->order_status_id;
        //$oRow->amount = null;
        $oRow->order_status_id = $nNewOrderStatusId;
        $oRow->order_status_id_is_finish = $nOrderStatusIdIsFinish;
        $oRow->modified_date = time();
        $nOrderId = $oRow->save();
        $oMail->sendBorrowerOrderStatusInfo($nOrderId, $nOldOrderStatusId);
        return $nOrderId;
      }
    }
    return null;
  }

  public function setOrderPaymentId($oCart, $nOrderPaymentId)
  {
    if (isset($oCart) && is_numeric($nOrderPaymentId)) {
      try {
        $this->_db->beginTransaction();
        foreach ($oCart as $oCartJournal) {
          $oRow = $this->find($oCartJournal->order_journal_id)->current();
          $oRow->order_payment_id = $nOrderPaymentId;
          $oRow->save();
        }
        $this->_db->commit();
      } catch (Zend_Exception $e) {
        $this->_db->rollBack();
        return null;
      }
    }
    return null;
  }

  public function setOrderPaymentSuccess($nOrderJournalId)
  {
    $nTime = time();
    $nExpirationDate = mktime(23, 59, 59, date("m"), date("d") + 10, date("Y"));
    if (is_numeric($nOrderJournalId)) {
      return $this->update(array("order_status_id" => 6, "order_status_id_is_finish" => 1, "modified_date" => $nTime, "execution_date" => $nTime, "expiration_date" => $nExpirationDate), "id = " . $nOrderJournalId);
    }
    return null;
  }

  public function getOrderFileId($nOrderPaymentId)
  {
    if (is_numeric($nOrderPaymentId)) {
      $oSelect = $this->select();
      $oSelect->from($this, array("id", "user_id", "order_file_id"));
      $oSelect->where("order_payment_id = ?", $nOrderPaymentId);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
    }
    return null;
  }

  public function getOrderUserId($nOrderId)
  {
    if (is_numeric($nOrderId)) {
      $oRow = $this->find($nOrderId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->user_id;
      }
    }
    return null;
  }

  public function saveNewOrder($aData)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->setFromArray($aData);
      return $oRow->save();
    }
    return null;
  }

  public function saveOrder($nOrderId, $aData)
  {
    if (is_numeric($nOrderId)) {
      $oRow = $this->find($nOrderId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->setFromArray($aData);
        if ($oRow->save())
          return true;
      }
    }
    return null;
  }

  public function getOrderAmount($nOrderId)
  {
    if (is_numeric($nOrderId)) {
      $oRow = $this->find($nOrderId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->amount;
      }
    }
    return null;
  }

  public function getOrderItemId($nOrderId)
  {
    if (is_numeric($nOrderId)) {
      $oRow = $this->find($nOrderId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->item_id;
      }
    }
    return null;
  }

}

?>
