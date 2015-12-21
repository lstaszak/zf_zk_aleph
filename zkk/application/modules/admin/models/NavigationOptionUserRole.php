<?php

class Admin_Model_NavigationOptionUserRole extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "navigation_option_user_role";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_navigation_option_user_role_navigation_option" => array(
      "columns" => array("navigation_option_id"),
      "refTableClass" => "Admin_Model_NavigationOption",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_user_role_navigation_option_user_role" => array(
      "columns" => array("user_role_id"),
      "refTableClass" => "Admin_Model_UserRole",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll($nNavigationOptionId = null)
  {
    $oSelect = $this->select();
    if (isset($nNavigationOptionId)) {
      $oSelect->where("navigation_option_id = ?", $nNavigationOptionId);
    }
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    else
      return null;
  }

  public function add($nNavigationOptionId, $nRoleId)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->navigation_option_id = $nNavigationOptionId;
      $oRow->user_role_id = $nRoleId;
      return $oRow->save();
    }
    return null;
  }

  public function deleteUserRole($nNavigationOptionId)
  {
    if (is_numeric($nNavigationOptionId)) {
      return $this->delete($this->_db->quoteInto("navigation_option_id = ?", $nNavigationOptionId));
    }

    return null;
  }

}

?>
