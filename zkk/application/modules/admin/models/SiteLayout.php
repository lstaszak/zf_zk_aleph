<?php

class Admin_Model_SiteLayout extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "site_layout";
  protected $_dependentTables = array();
  protected $_referenceMap = array();

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
