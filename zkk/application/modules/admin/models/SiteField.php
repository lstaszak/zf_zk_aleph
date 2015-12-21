<?php

class Admin_Model_SiteField extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "site_field";
  protected $_dependentTables = array(
    "Admin_Model_Site"
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
    return null;
  }

  public function newSiteField($sFieldName)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->name = $sFieldName;
      return $oRow->save();
    }
    return null;
  }

  public function getSiteField($nSiteFieldId)
  {
    if (is_numeric($nSiteFieldId) && $nSiteFieldId > 0) {
      $oRow = $this->find($nSiteFieldId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow;
      }
    }
    return null;
  }

  public function realEditSiteField($sSiteField, $sContent)
  {
    if (is_string($sSiteField)) {
      $oSelect = $this->select();
      $oSelect->where("name = ?", $sSiteField);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->content = $sContent;
        return $oRow->save();
      }
    }
    return null;
  }

  public function addContent($nSiteFieldId, $sContent, $sLang = null)
  {
    if (is_numeric($nSiteFieldId) && $nSiteFieldId > 0) {
      $oRow = $this->find($nSiteFieldId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        if ($sLang == "lang_pl")
          $oRow->content_lang_pl = $sContent;
        else if ($sLang == "lang_en")
          $oRow->content_lang_en = $sContent;
        return $oRow->save();
      }
    }
    return null;
  }

}

?>
