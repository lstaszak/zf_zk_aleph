<?php

class Borrower_Model_VOrderPaymentHistory extends AppCms2_Controller_Plugin_TableAbstract
{
  protected $_name = "v_order_payment_history_borrower";
  protected $_primary = "order_payment_id";

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getOne($nOrderPaymentId)
  {
    $oSelect = $this->select();
    $oSelect->where("order_payment_id = ?", $nOrderPaymentId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

  public function getUserPayments($nUserId, $aFilter = array(), $nCount = null, $nOffset = null, $sOrderBy = null)
  {
    if (!isset($sOrderBy))
      $sOrderBy = array("order_payment_id ASC");
    $oSelect = $this->select();
    $oSelect->where("user_id = ?", $nUserId);
    $oSelect->order($sOrderBy);
    if ($nOffset < 0)
      $nOffset = 0;
    $oSelect->group("order_payment_id");
    $oSelect->limit($nCount, $nOffset);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getUserPaymentDetails($nUserId, $nOrderPaymentId, $aFilter = array(), $nCount = null, $nOffset = null, $sOrderBy = null)
  {
    if (!isset($sOrderBy))
      $sOrderBy = array("order_payment_id ASC");
    $oSelect = $this->select();
    $oSelect->where("user_id = ?", $nUserId);
    $oSelect->where("order_payment_id = ?", $nOrderPaymentId);
    $oSelect->order($sOrderBy);
    if ($nOffset < 0)
      $nOffset = 0;
    $oSelect->limit($nCount, $nOffset);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getCartJournals($nOrderCartId)
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("order_journal_id"));
    $oSelect->where("order_payment_id = ?", $nOrderCartId);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }
}

?>
