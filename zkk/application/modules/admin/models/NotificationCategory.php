<?php

class Admin_Model_NotificationCategory extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "notification_category";
  protected $_dependentTables = array("Admin_Model_Notification");
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order("name");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function newNotificationCategory($sName)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->name = $sName;
      return $oRow->save();
    }
    return null;
  }

  public function edit($nId, $sName)
  {
    $oRow = $this->find($nId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->name = $sName;
      return $oRow->save();
    }
    return null;
  }

  public function check($sName)
  {
    $oSelect = $this->select();
    $oSelect->where("name = ?", $sName);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->id;
    return null;
  }

  public function deleteNotificationCategory($nId)
  {
    if (is_numeric($nId)) {
      return $this->delete($this->_db->quoteInto("id = ?", $nId));
    }
    return null;
  }

  public function deleteRow($nId)
  {
    if (is_numeric($nId)) {
      return $this->delete($this->_db->quoteInto("id = ?", $nId));
    }
    return null;
  }

}

?>
