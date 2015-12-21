<?php

class User_Model_VOrderJournal extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_order_journal_user";
  protected $_primary = "id";

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

  public function getOne($nOrderId)
  {
    $oSelect = $this->select();
    $oSelect->where("id = ?", $nOrderId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

  public function getUserEmailAddress($nOrderId)
  {
    $oRow = $this->getOne($nOrderId);
    if (isset($oRow)) {
      return $oRow->user_email_address;
    }
    return null;
  }

  public function getUserCount($nUserId, $nOrderStatus)
  {
    $oSelect = $this->select();
    if (isset($nUserId))
      $oSelect->where("user_id = ?", $nUserId);
    $oSelect->where("order_status_id = ?", $nOrderStatus);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset->count();
    return 0;
  }

  public function getUserOrders($nUserId = null, $aFilter = array(), $nCount = null, $nOffset = null, $sOrderBy = null)
  {
    if (!isset($sOrderBy))
      $sOrderBy = array("modified_date ASC");
    $oSelect = $this->select();
    if (isset($nUserId))
      $oSelect->where("user_id = ?", $nUserId);
    if (count($aFilter)) {
      foreach ($aFilter as $sKey => $mValue) {
        if (!in_array($sKey, array("order_status_id", "modified_date"))) {
          $oSelect->where("$sKey LIKE ?", "%$mValue%");
        }
      }
    }
    if (isset($aFilter["id"])) {
      $oSelect->where("id = ?", $aFilter["id"]);
    }
    if (isset($aFilter["order_status_id"])) {
      $oSelect->where("order_status_id = ?", $aFilter["order_status_id"]);
    }
    if (isset($aFilter["amount"])) {
      $oSelect->where("amount = ?", $aFilter["amount"]);
    }
    if (isset($aFilter["modified_date"])) {
      $oSelect->where("modified_date >= ?", $aFilter["modified_date"]);
      $oSelect->where("modified_date <= ?", $aFilter["modified_date"] + 86400);
    }
    $oSelect->order($sOrderBy);
    if ($nOffset < 0)
      $nOffset = 0;
    $oSelect->limit($nCount, $nOffset);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getOrderRelationships($nOrderId, $nItemId)
  {
    $oSelect = $this->select();
    $oSelect->where("item_id = ?", $nItemId);
    $oSelect->where("id <> ?", $nOrderId);
    $oSelect->order(array("order_status_id DESC", "id DESC"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getOrderLastMonitExpiration()
  {
    $nExpirationDate = mktime(23, 59, 59, date("m"), date("d") + 1, date("Y"));
    $oSelect = $this->select();
    $oSelect->where("expiration_date = ?", $nExpirationDate);
    $oSelect->where("order_status_id IN (?)", array(3, 5, 6));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getOrderExpiration()
  {
    $nExpirationDate1 = mktime(23, 59, 59, date("m"), date("d") - 10, date("Y"));
	$nExpirationDate2 = mktime(23, 59, 59, date("m"), date("d") - 1, date("Y"));
    $oSelect = $this->select();
    $oSelect->where("expiration_date <= ?", $nExpirationDate1);
    $oSelect->where("order_status_id IN (?)", array(3, 5, 6));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

}

?>
