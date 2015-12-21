<?php

class Admin_Model_SiteSeoKeywords extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "site_seo_keywords";
  protected $_dependentTables = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order("value ASC");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

}

?>
