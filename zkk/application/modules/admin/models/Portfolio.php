<?php

class Admin_Model_Portfolio extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "portfolio";
  protected $_dependentTables = array();
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function setOrder($nPortfolioId, $nOrder)
  {
    if (is_numeric($nPortfolioId) && $nPortfolioId > 0 && is_numeric($nOrder)) {
      $oRow = $this->find($nPortfolioId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->order = $nOrder;
        return $oRow->save();
      }
    }
    return null;
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order(array("order ASC", "created_date DESC"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset->toArray();
    return array();
  }

  public function getLatest()
  {
    $oSelect = $this->select();
    $oSelect->order(array("order ASC", "created_date DESC"));
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->toArray();
    return array();
  }

  public function getAllCategoryTag($sLang)
  {
    $oSelect = $this->select();
    if ($sLang === "pl")
      $oSelect->from($this, array("id", "category_tag AS category_tag"));
    if ($sLang === "en")
      $oSelect->from($this, array("id", "category_tag_lang_en AS category_tag"));
    $oSelect->order(array("order ASC", "created_date DESC"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return array();
  }

}

?>
