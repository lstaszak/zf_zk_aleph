<?php

class Admin_Model_NavigationAction extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "navigation_action";
  protected $_dependentTables = array("Admin_Model_NavigationOption");
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order("value");
    $oRowset = $this->fetchAll($oSelect);

    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    else
      return null;
  }

  public function add($sValue)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->value = $sValue;
      return $oRow->save();
    }
    return null;
  }

  public function check($sValue)
  {
    $oSelect = $this->select();
    $oSelect->where("value = ?", $sValue);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->id;
    else
      return false;
  }

  public function edit($nId, $sValue)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oRow = $this->find($nId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->value = $sValue;
        return $oRow->save();
      }
    }
    return null;
  }

  public function deleteElement($nId)
  {
    if (is_numeric($nId)) {
      return $this->delete($this->_db->quoteInto("id = ?", $nId));
    }
    return false;
  }

}

?>
