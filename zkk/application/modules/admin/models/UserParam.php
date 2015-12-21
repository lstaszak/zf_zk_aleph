<?php

class Admin_Model_UserParam extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "user_param";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_image_user_param" => array(
      "columns" => array("image_id"),
      "refTableClass" => "Admin_Model_Image",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_user_user_param" => array(
      "columns" => array("user_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_user_category_user_param" => array(
      "columns" => array("user_category_id"),
      "refTableClass" => "Admin_Model_UserCategory",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    )
  );
  private $_oAuth;

  public function __construct($config = array())
  {
    parent::__construct($config);
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
  }

  public function newUserParam($nUserId, $aParam)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->user_id = $nUserId;
      $oRow->first_name = $aParam["first_name"];
      $oRow->last_name = $aParam["last_name"];
      $oRow->phone_number = $aParam["phone_number"];
      $oRow->user_category_id = null;
      $oRow->last_activity = 0;
      return $oRow->save();
    }
    return null;
  }

  public function setActivity($nUserParamId)
  {
    $oRow = $this->find($nUserParamId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->last_activity = time();
      return $oRow->save();
    }
    return null;
  }

  public function editUserParam($nUserId, $aParam)
  {
    $oSelect = $this->select();
    $oSelect->where("user_id = ?", $nUserId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->first_name = $aParam["first_name"];
      $oRow->last_name = $aParam["last_name"];
      $oRow->phone_number = $aParam["phone_number"];
      $oRow->user_category_id = $aParam["user_category_id"];
      return $oRow->save();
    }
    return null;
  }

  public function editImageUserParam($nUserId, $nImageId)
  {
    $oSelect = $this->select();
    $oSelect->where("user_id = ?", $nUserId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->image_id = $nImageId;
      return $oRow->save();
    }
    return null;
  }

  public function getUserId($nId)
  {
    $oSelect = $this->select();
    $oSelect->where("image_id = ?", $nId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->user_id;
    return null;
  }

  public function getUserParam($nUserId)
  {
    if (is_numeric($nUserId)) {
      $oSelect = $this->select();
      $oSelect->where("user_id = ?", $nUserId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow;
      }
    }
    return null;
  }

}

?>
