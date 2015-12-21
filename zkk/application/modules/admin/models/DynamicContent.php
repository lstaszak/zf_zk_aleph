<?php

class Admin_Model_DynamicContent extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "dynamic_content";
  protected $_dependentTables = array();
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getRow($sVariableName)
  {
    $oSelect = $this->select();
    $oSelect->where("variable_name = ?", $sVariableName);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->content;
    return null;
  }

}

?>
