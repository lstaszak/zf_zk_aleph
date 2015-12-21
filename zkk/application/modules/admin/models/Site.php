<?php

class Admin_Model_Site extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "site";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_image_gallery_site" => array(
      "columns" => array("image_gallery_id"),
      "refTableClass" => "Admin_Model_ImageGallery",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_image_slider_site" => array(
      "columns" => array("image_slider_id"),
      "refTableClass" => "Admin_Model_ImageSlider",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_site_layout_site" => array(
      "columns" => array("site_layout_id"),
      "refTableClass" => "Admin_Model_SiteLayout",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

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

  public function findLayoutTemplate($nMenuId)
  {
    $oSelect = $this->select();
    $oSelect->from(array("s" => "site"), array("s.*"));
    $oSelect->join(array("sl" => "site_layout"), "s.site_layout_id = sl.id", array("sl.name AS layout_name"));
    $oSelect->setIntegrityCheck(false);
    if (is_numeric($nMenuId) && $nMenuId > 0)
      $oSelect->where("s.menu_id = ?", $nMenuId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

  public function findDefaultMenuId()
  {
    $oSelect = $this->select();
    $oSelect->where("site_layout_id = ?", 1);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->menu_id;
    return null;
  }

  public function findSearcherMenuId()
  {
    $oSelect = $this->select();
    $oSelect->where("site_layout_id = ?", 6);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->menu_id;
    return null;
  }

  public function findSiteMapMenuId()
  {
    $oSelect = $this->select();
    $oSelect->where("site_layout_id = ?", 13);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->menu_id;
    return null;
  }

  public function findContactMenuId()
  {
    $oSelect = $this->select();
    $oSelect->where("site_layout_id = ?", 4);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->menu_id;
    return null;
  }

  public function findSiteId($nMenuId)
  {
    if (is_numeric($nMenuId) && $nMenuId > 0) {
      $oSelect = $this->select();
      $oSelect->where("menu_id = ?", $nMenuId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow;
    }
    return null;
  }

  public function newSite($aData, $aSiteFileds)
  {
    $oSiteField = new Admin_Model_SiteField();
    $oSiteSiteField = new Admin_Model_SiteSiteField();
    $oNavigationMenu = new Admin_Model_NavigationMenu();
    $oNavigationSubmenu = new Admin_Model_NavigationSubmenu();
    $oNavigationSubsubmenu = new Admin_Model_NavigationSubsubmenu();
    if (is_array($aData) && count($aData)) {
      try {
        $this->_db->beginTransaction();
        $nMenuId = (int)$aData["menu_id"];
        $nSiteLayoutId = (int)$aData["site_layout_id"];
        $nSiteId = $this->findSiteId($nMenuId)->id;
        $oSiteSiteField->deleteRow($nSiteId);
        $this->deleteRow($nSiteId);
        $oRow = $this->createRow();
        if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
          $oRow->menu_id = $nMenuId;
          $oRow->site_layout_id = $nSiteLayoutId;
          $nSiteId = $oRow->save();
          if ($nSiteId) {
            foreach ($aSiteFileds as $sFieldName) {
              $nSiteFieldId = $oSiteField->newSiteField($sFieldName);
              if ($nSiteFieldId) {
                $oSiteSiteField->newSiteSiteField($nSiteId, $nSiteFieldId);
              }
            }
          }
          $this->_db->commit();
        }
      } catch (Zend_Exception $e) {
        $this->_db->rollBack();
        return null;
      }
    }
    return null;
  }

  public function saveSiteGallery($nId, $nImageGalleryId)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oSelect = $this->select();
      $oSelect->where("id = ?", $nId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->image_gallery_id = $nImageGalleryId;
        return $oRow->save();
      }
    }
    return null;
  }

  public function saveSiteSlider($nId, $nImageSliderId)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oSelect = $this->select();
      $oSelect->where("id = ?", $nId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->image_slider_id = $nImageSliderId;
        return $oRow->save();
      }
    }
    return null;
  }

}

?>
