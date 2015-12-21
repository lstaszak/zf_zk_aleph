<?php

class Borrower_Model_OrderJournalOrderCart extends AppCms2_Controller_Plugin_TableAbstract
{
  protected $_name = "order_journal_order_cart";
  protected $_referenceMap = array(
    "fk_order_cart_order_journal_order_cart" => array(
      "columns" => array("order_cart_id"),
      "refTableClass" => "Borrower_Model_OrderCart",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_order_journal_order_journal_order_cart" => array(
      "columns" => array("order_journal_id"),
      "refTableClass" => "User_Model_OrderJournal",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function addOrderJournalOrderCart($aData)
  {
    $oSelect = $this->select();
    $oSelect->where("order_journal_id = ?", $aData["order_journal_id"]);
    $oSelect->where("order_cart_id = ?", $aData["order_cart_id"]);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    else
      return $this->addRow($aData);
    return null;
  }

  public function getCartJournals($nOrderCartId)
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("order_journal_id"));
    $oSelect->where("order_cart_id = ?", $nOrderCartId);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function findOrderJournal($nOrderJournalId, $nOrderCartId)
  {
    $oSelect = $this->select();
    $oSelect->where("order_journal_id = ?", $nOrderJournalId);
    $oSelect->where("order_cart_id = ?", $nOrderCartId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return true;
    return null;
  }

  public function deleteCartJournals($nOrderCartId)
  {
    if (is_numeric($nOrderCartId)) {
      return $this->delete($this->_db->quoteInto("order_cart_id = ?", $nOrderCartId));
    }
    return null;
  }
}

?>
