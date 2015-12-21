<?php

class User_Model_OrderChangeType extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_change_type";
  protected $_dependentTables = array("User_Model_OrderChangeLog");

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getOrderChangeTypeId($nOldOrderStatusId, $nNewOrderStatusId)
  {
    $sIdx = $nOldOrderStatusId . $nNewOrderStatusId;
    $oSelect = $this->select();
    $oSelect->where("idx = ?", $sIdx);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->id;
    return null;
  }

}

?>
