<?php

class Admin_Model_ImageModule extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "image_gallery";
  protected $_dependentTables = array(
    "Admin_Model_Image"
  );
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
    else
      return null;
  }

}

?>
