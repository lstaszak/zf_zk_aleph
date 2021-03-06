<?php

class Admin_Model_VNavigationMenu extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_navigation_menu";
  protected $_primary = "id";

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order(array("navigation_module asc", "order asc"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    else
      return null;
  }

  public function getConfig($sModule)
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("id", "label", "desc", "image_id", "navigation_module AS module", "navigation_controller AS controller", "navigation_action AS action", "navigation_resource AS resource", "navigation_privilege AS privilege", "visible", "order"));
    $oSelect->where("navigation_module = ?", $sModule);
    $oSelect->order("order");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getSiteMenu($sModule)
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("id", "label"));
    $oSelect->where("navigation_module = ?", $sModule);
    $oSelect->order("order");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getResource()
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("navigation_resource"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getLabel($nId)
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("label"));
    $oSelect->where("id = ?", $nId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

}

?>
