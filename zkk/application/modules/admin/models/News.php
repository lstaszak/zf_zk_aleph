<?php

class Admin_Model_News extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "news";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_image_news" => array(
      "columns" => array("image_id"),
      "refTableClass" => "Admin_Model_Image",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_user_news" => array(
      "columns" => array("user_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function setOrder($nNewsId, $nOrder)
  {
    if (is_numeric($nNewsId) && $nNewsId > 0 && is_numeric($nOrder)) {
      $oRow = $this->find($nNewsId)->current();
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
