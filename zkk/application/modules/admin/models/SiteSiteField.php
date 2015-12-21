<?php

class Admin_Model_SiteSiteField extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "site_site_field";
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

  public function newSiteSiteField($nSiteId, $nSiteFieldId)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->site_id = $nSiteId;
      $oRow->site_field_id = $nSiteFieldId;
      return $oRow->save();
    }
    return null;
  }

  public function deleteRow($nSiteId)
  {
    if (is_numeric($nSiteId)) {
      $this->delete($this->_db->quoteInto("site_id = ?", $nSiteId));
    }
    return null;
  }

}

?>
