<?php

class Admin_Model_NavigationMenu extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "navigation_menu";
  protected $_dependentTables = array("Admin_Model_NavigationSubmenu");
  protected $_referenceMap = array(
    "fk_navigation_option_navigation_menu" => array(
      "columns" => array("navigation_option_id"),
      "refTableClass" => "Admin_Model_NavigationOption",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_image_navigation_menu" => array(
      "columns" => array("image_id"),
      "refTableClass" => "Admin_Model_Image",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function add($aData)
  {
    $oModelNavigationResource = new Admin_Model_NavigationResource();
    $oModelNavigationOption = new Admin_Model_NavigationOption();
    $oGenereteSessionId = new AppCms2_GenereteSessionId();
    try {
      $this->_db->beginTransaction();
      $aGenereteSessionId = $oGenereteSessionId->generatePassword();
      $sNavigationResource = "menu_resource_{$aData["navigation_module_id"]}_{$aData["navigation_controller_id"]}_{$aData["navigation_action_id"]}_{$aGenereteSessionId["user_password"]}";
      $nNavigationResourceId = $oModelNavigationResource->add($sNavigationResource);
      if (isset($nNavigationResourceId)) {
        $aData["navigation_resource_id"] = $nNavigationResourceId;
        $nNavigatinOptionId = $oModelNavigationOption->add($aData);
        if (isset($nNavigatinOptionId)) {
          $aData["id"] = (int)$this->getMaxId();
          $aData["navigation_option_id"] = $nNavigatinOptionId;
          $oRow = $this->createRow();
          if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
            $oRow->setFromArray($aData);
            if ($oRow->save()) {
              $this->_db->commit();
              return $nNavigatinOptionId;
            }
          }
        }
      }
      $this->_db->rollBack();
      return null;
    } catch (Zend_Exception $e) {
      $this->_db->rollBack();
      return null;
    }
  }

  public function edit($nNavigationMenuId, $aData)
  {
    $oModelNavigationResource = new Admin_Model_NavigationResource();
    $oModelNavigationOption = new Admin_Model_NavigationOption();
    $oGenereteSessionId = new AppCms2_GenereteSessionId();
    try {
      $this->_db->beginTransaction();
      $oRow = $this->find($nNavigationMenuId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->label = $aData["label"];
        $oRow->desc = $aData["desc"];
        $oRow->image_id = $aData["image_id"];
        $oRow->visible = $aData["visible"];
        if ($oRow->save()) {
          $aGenereteSessionId = $oGenereteSessionId->generatePassword();
          $sNavigationResource = "menu_resource_{$aData["navigation_module_id"]}_{$aData["navigation_controller_id"]}_{$aData["navigation_action_id"]}_{$aGenereteSessionId["user_password"]}";
          $nNavigationResourceId = $oModelNavigationResource->add($sNavigationResource);
          if (isset($nNavigationResourceId)) {
            $aData["navigation_resource_id"] = $nNavigationResourceId;
            $nNavigationOptionId = $oRow->navigation_option_id;
            if ($oModelNavigationOption->edit($nNavigationOptionId, $aData)) {
              $this->_db->commit();
              return $nNavigationOptionId;
            }
          }
        }
      }
      $this->_db->rollBack();
      return null;
    } catch (Zend_Exception $e) {
      $this->_db->rollBack();
      return null;
    }
  }

  public function makeVisible($nNavigationMenuId)
  {
    $oRow = $this->find($nNavigationMenuId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->visible = 1;
      return $oRow->save();
    }
    return null;
  }

  public function findOptionId($nNavigationMenuId)
  {
    if (is_numeric($nNavigationMenuId) && $nNavigationMenuId > 0) {
      $oRow = $this->find($nNavigationMenuId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->navigation_option_id;
      }
    }
    return null;
  }

  public function findMenuId($nNavigationOptionId)
  {
    if (is_numeric($nNavigationOptionId) && $nNavigationOptionId > 0) {
      $oSelect = $this->select();
      $oSelect->where("navigation_option_id = ?", $nNavigationOptionId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->id;
    }
    return null;
  }

  public function setOrder($nNavigationMenuId, $nOrder)
  {
    if (is_numeric($nNavigationMenuId) && $nNavigationMenuId > 0 && is_numeric($nOrder)) {
      $oRow = $this->find($nNavigationMenuId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->order = $nOrder;
        return $oRow->save();
      }
    }
    return null;
  }

  public function getMaxId()
  {
    $oModelNavigationMenu = new Admin_Model_NavigationMenu();
    $oModelNavigationSubmenu = new Admin_Model_NavigationSubmenu();
    $oModelNavigationSubsubmenu = new Admin_Model_NavigationSubsubmenu();
    $aAllIds = array($oModelNavigationMenu->getLastRow()->id, $oModelNavigationSubmenu->getLastRow()->id, $oModelNavigationSubsubmenu->getLastRow()->id);
    return max($aAllIds) + 1;
  }

}

?>
