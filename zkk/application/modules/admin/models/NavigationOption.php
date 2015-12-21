<?php

class Admin_Model_NavigationOption extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "navigation_option";
  protected $_dependentTables = array("Admin_Model_NavigationMenu", "Admin_Model_NavigationSubmenu", "Admin_Model_NavigationOptionUserRole");
  protected $_referenceMap = array(
    "fk_navigation_action_navigation_option" => array(
      "columns" => array("navigation_action_id"),
      "refTableClass" => "Admin_Model_NavigationAction",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_navigation_controller_navigation_option" => array(
      "columns" => array("navigation_controller_id"),
      "refTableClass" => "Admin_Model_NavigationController",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_navigation_module_navigation_option" => array(
      "columns" => array("navigation_module_id"),
      "refTableClass" => "Admin_Model_NavigationModule",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_navigation_privilege_navigation_option" => array(
      "columns" => array("navigation_privilege_id"),
      "refTableClass" => "Admin_Model_NavigationPrivilege",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_navigation_resource_navigation_option" => array(
      "columns" => array("navigation_resource_id"),
      "refTableClass" => "Admin_Model_NavigationResource",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    else
      return null;
  }

  public function add($aData)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->setFromArray($aData);
      return $oRow->save();
    }
    return null;
  }

  public function edit($nNavigationOptionId, $aData)
  {
    if (is_numeric($nNavigationOptionId) && $nNavigationOptionId > 0) {
      $oRow = $this->find($nNavigationOptionId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->navigation_module_id = $aData["navigation_module_id"];
        $oRow->navigation_controller_id = $aData["navigation_controller_id"];
        $oRow->navigation_action_id = $aData["navigation_action_id"];
        $oRow->navigation_resource_id = $aData["navigation_resource_id"];
        $oRow->navigation_privilege_id = $aData["navigation_privilege_id"];
        return $oRow->save();
      }
      return null;
    }
  }

  public function deleteOption($nId)
  {
    if (is_numeric($nId)) {
      return $this->delete($this->_db->quoteInto("id = ?", $nId));
    }
    return null;
  }

  public function findModuleId($nNavigatinMenuOptionId)
  {
    if (is_numeric($nNavigatinMenuOptionId) && $nNavigatinMenuOptionId > 0) {
      $oRow = $this->find($nNavigatinMenuOptionId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->navigation_module_id;
      }
    }
    return null;
  }

  public function findControllerId($nNavigatinMenuOptionId)
  {
    if (is_numeric($nNavigatinMenuOptionId) && $nNavigatinMenuOptionId > 0) {
      $oRow = $this->find($nNavigatinMenuOptionId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->navigation_controller_id;
      }
    }
    return null;
  }

}

?>
