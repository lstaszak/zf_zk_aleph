<?php

class User_Model_OrderPaymentHistory extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_payment_history";
  protected $_referenceMap = array(
    "fk_order_journal_order_payment_history" => array(
      "columns" => array("order_journal_id"),
      "refTableClass" => "User_Model_OrderJournal",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_order_payment_order_payment_history" => array(
      "columns" => array("order_payment_id"),
      "refTableClass" => "User_Model_OrderPayment",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function setOrderPaymentHistory($oCart, $nOrderPaymentId)
  {
    if (isset($oCart) && is_numeric($nOrderPaymentId)) {
      try {
        $this->_db->beginTransaction();
        foreach ($oCart as $oCartJournal) {
          $oRow = $this->createRow();
          if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
            $oRow->order_journal_id = $oCartJournal->order_journal_id;
            $oRow->order_payment_id = $nOrderPaymentId;
            $oRow->date = time();
            $oRow->save();
          }
        }
        $this->_db->commit();
      } catch (Zend_Exception $e) {
        $this->_db->rollBack();
        return null;
      }
    }
    return null;
  }

}

?>
