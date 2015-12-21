<?php

class Admin_Model_VSiteLayout extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_site_layout";
  protected $_primary = "menu_id";

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getSiteLayoutId($nNavigationMenuId)
  {
    $oSelect = $this->select();
    $oSelect->where("menu_id = ?", $nNavigationMenuId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

}

?>
