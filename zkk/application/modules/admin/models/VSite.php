<?php

class Admin_Model_VSite extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_site";
  protected $_primary = "site_id";

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

  public function findSiteFieldId($sLayoutName, $sFieldName)
  {
    $oSelect = $this->select();
    $oSelect->where("layout_name = ?", $sLayoutName);
    $oSelect->where("field_name = ?", $sFieldName);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->site_field_id;
    return null;
  }

  public function findSiteField($nMenuId, $sFieldName)
  {
    $oSelect = $this->select();
    $oSelect->where("menu_id = ?", $nMenuId);
    $oSelect->where("field_name = ?", $sFieldName);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

  public function searchContent($sSearchedText, $sLang)
  {
    $oSelect = $this->select();
    $oSelect->where("content_lang_$sLang LIKE ?", "%" . $sSearchedText . "%");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

}

?>
