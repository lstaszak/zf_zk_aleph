<?php

class Admin_Model_VNavigationMenuUserRole extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_navigation_menu_user_role";
  protected $_primary = "navigation_option_id";

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
    return null;
  }

}

?>
