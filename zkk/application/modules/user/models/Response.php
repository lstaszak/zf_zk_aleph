<?php

class User_Model_Response extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "response";
  protected $_referenceMap = array(
    "fk_order_payment_response" => array(
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

  public function setResponse($nOrderPaymentId, $aData)
  {
    if (is_numeric($nOrderPaymentId)) {
      $aData["order_payment_id"] = $nOrderPaymentId;
      return $this->addRow($aData);
    }
    return null;
  }

  public function getStatus($sOrderId, $sSessionId)
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("status"));
    $oSelect->where("order_id LIKE ?", $sOrderId);
    $oSelect->where("session_id LIKE ?", $sSessionId);
    $oSelect->order(array("ts DESC"));
    $oRow = $this->fetchRow($oSelect);
    return $oRow->status;
  }

  public function getFinish()
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("create"));
    $oSelect->where("status = ?", 99);
    $oRowset = $this->fetchAll($oSelect);
    return $oRowset;
  }

}

?>
