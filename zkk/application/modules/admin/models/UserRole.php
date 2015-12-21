<?php

class Admin_Model_UserRole extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "user_role";
  protected $_dependentTables = array(
    "Admin_Model_User",
    "Admin_Model_NavigationOptionUserRole"
  );
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order("role_name");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function newRole($sRoleName)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->role_name = $sRoleName;
      return $oRow->save();
    }
    return null;
  }

  public function edit($nRoleId, $sRoleName)
  {
    $oRow = $this->find($nRoleId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->role_name = $sRoleName;
      return $oRow->save();
    }
    return null;
  }

  public function check($sValue)
  {
    $oSelect = $this->select();
    $oSelect->where("role_name = ?", $sValue);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->id;
    return null;
  }

  public function deleteUserRole($nRoleId)
  {
    if (is_numeric($nRoleId)) {
      return $this->delete($this->_db->quoteInto("id = ?", $nRoleId));
    }
    return null;
  }

  public function deleteRow($nRoleId)
  {
    if (is_numeric($nRoleId)) {
      return $this->delete($this->_db->quoteInto("id = ?", $nRoleId));
    }
    return null;
  }

}

?>
