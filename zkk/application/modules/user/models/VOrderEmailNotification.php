<?php

class User_Model_VOrderEmailNotification extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_order_email_notification";
  protected $_primary = "id";
  protected $_dependentTables = array();
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getOrderEmailNotification($nOldOrderStatusId, $nNewOrderStatusId)
  {
    $oSelect = $this->select();
    $oSelect->where("order_status_id_old = ?", $nOldOrderStatusId);
    $oSelect->where("order_status_id_new = ?", $nNewOrderStatusId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

  public function getAll($sOrder = null)
  {
    $oSelect = $this->select();
    $oSelect->order(array("order_status_id_old ASC", "order_status_id_new ASC"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

}

?>
