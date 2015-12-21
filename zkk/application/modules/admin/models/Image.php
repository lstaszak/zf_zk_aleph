<?php

class Admin_Model_Image extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "image";
  protected $_dependentTables = array(
    "Admin_Model_NavigationMenu",
    "Admin_Model_UserParam"
  );
  protected $_referenceMap = array(
    "fk_image_gallery_image" => array(
      "columns" => array("image_gallery_id"),
      "refTableClass" => "Admin_Model_ImageGallery",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_image_module_image" => array(
      "columns" => array("image_module_id"),
      "refTableClass" => "Admin_Model_ImageModule",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_image_slider_image" => array(
      "columns" => array("image_slider_id"),
      "refTableClass" => "Admin_Model_ImageSlider",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_image_type_image" => array(
      "columns" => array("image_type_id"),
      "refTableClass" => "Admin_Model_ImageType",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_user_image" => array(
      "columns" => array("user_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );
  private $_oAuth;
  private $_nUserId;

  public function __construct($config = array())
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
    parent::__construct($config);
  }

  public function saveImage($nTypeId, $nModuleId, $sGenName, $sUserName, $sDescr = null)
  {
    if ($this->_oAuth->hasIdentity()) {
      if (!isset($nTypeId)) {
        $nTypeId = 1;
      }
      $oRow = $this->createRow();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->image_type_id = $nTypeId;
        $oRow->image_module_id = $nModuleId;
        $oRow->user_id = $this->_nUserId;
        $oRow->name = $sGenName;
        $oRow->user_name = $sUserName;
        $oRow->descr = $sDescr;
        $oRow->created_date = time();
        return $oRow->save();
      } else {
        throw new Zend_Exception();
      }
    } else
      throw new Zend_Exception();
  }

  public function getAll($nModuleId = null, $aFilter = array(), $nCount = null, $nOffset = null, $sOrderBy = null)
  {
    if (!isset($sOrderBy))
      $sOrderBy = array("i.created_date ASC");
    $oSelect = $this->select();
    $oSelect->from(array("i" => "image"), array("i.*", "from_unixtime(created_date) user_created_date"));
    $oSelect->join(array("it" => "image_type"), "i.image_type_id = it.id", array("type_name" => "name"));
    $oSelect->joinLeft(array("im" => "image_module"), "i.image_module_id = im.id", array("module_name" => "name"));
    $oSelect->joinLeft(array("ig" => "image_gallery"), "i.image_gallery_id = ig.id", array("gallery_name" => "name"));
    $oSelect->joinLeft(array("is" => "image_slider"), "i.image_slider_id = is.id", array("slider_name" => "name"));
    $oSelect->joinLeft(array("up" => "user_param"), "i.id = up.image_id", array("user_image_id" => "user_id"));
    $oSelect->setIntegrityCheck(false);
    if (is_numeric($nModuleId) && $nModuleId > 0)
      $oSelect->where("i.image_module_id = ?", $nModuleId);
    if (count($aFilter)) {
      foreach ($aFilter as $sKey => $mValue) {
        if ($sKey != "created_date") {
          $oSelect->where("i." . "$sKey LIKE ?", "%$mValue%");
        }
      }
    }
    if (isset($aFilter["created_date"])) {
      $oSelect->where("i.created_date >= ?", $aFilter["created_date"]);
      $oSelect->where("i.created_date <= ?", $aFilter["created_date"] + 86400);
    }
    $oSelect->order($sOrderBy);
    if ($nOffset < 10) {
      $nOffset = 0;
    }
    $oSelect->limit($nCount, $nOffset);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getOne($nId)
  {
    if ($this->_oAuth->hasIdentity()) {
      $oSelect = $this->select();
      $oSelect->where("id = ?", $nId);
      $oSelect->where("user_id = ?", $this->_nUserId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow;
      else
        return null;
    }
    return null;
  }

  public function getFileName($nId)
  {
    if ($this->_oAuth->hasIdentity()) {
      $oSelect = $this->select();
      $oSelect->where("id = ?", $nId);
      $oSelect->where("user_id = ?", $this->_nUserId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->name;
      else
        return null;
    }
    return null;
  }

  public function getGallery($nImageGalleryId)
  {
    $oSelect = $this->select();
    $oSelect->where("image_gallery_id = ?", $nImageGalleryId);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getSlider($nImageSliderId)
  {
    $oSelect = $this->select();
    $oSelect->where("image_slider_id = ?", $nImageSliderId);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function saveSettings($nId, $aImageSettings)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oRow = $this->find($nId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->user_name = $aImageSettings["user_name"];
        $oRow->descr = $aImageSettings["descr"];
        return $oRow->save();
      } else {
        throw new Zend_Exception();
      }
    }
  }

  public function saveAddTo($nImageId, $sColumnName, $nElementId)
  {
    if (is_numeric($nImageId) && $nImageId > 0) {
      $oRow = $this->find($nImageId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->$sColumnName = $nElementId;
        if ($oRow->save())
          return true;
      } else {
        throw new Zend_Exception();
      }
    }
  }

  public function deleteImage($nId)
  {
    if (is_numeric($nId)) {
      return $this->delete($this->_db->quoteInto("id = ?", $nId));
    }
    return null;
  }

  public function truncate()
  {
    $this->_db->query("TRUNCATE image");
  }

  public function getUserId($nId)
  {
    if ($this->_oAuth->hasIdentity()) {
      $oSelect = $this->select();
      $oSelect->where("id = ?", $nId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->user_id;
      else
        return null;
    }
    return null;
  }

  public function deleteUnused()
  {
    $sQuery = "DELETE FROM image WHERE id NOT IN (SELECT image_id FROM user_param WHERE image_id IS NOT NULL) AND image_type_id = 3";
    $this->_db->fetchAll($sQuery);
  }

}

?>
