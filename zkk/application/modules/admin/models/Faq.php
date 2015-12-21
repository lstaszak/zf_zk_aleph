<?php

class Admin_Model_Faq extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "faq";
  protected $_dependentTables = array();
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function setOrder($nFaqId, $nOrder)
  {
    if (is_numeric($nFaqId) && $nFaqId > 0 && is_numeric($nOrder)) {
      $oRow = $this->find($nFaqId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->order = $nOrder;
        return $oRow->save();
      }
    }
    return null;
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order(array("order"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

}

?>
