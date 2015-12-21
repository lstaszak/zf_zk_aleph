<?php

class Admin_Model_VUser extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_user";
  protected $_primary = "user_id";
  private $_oAuth;
  private $_nUserId;

  public function __construct($config = array())
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
    parent::__construct($config);
  }

  public function getAllEmailAddress()
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("email_address", "first_name", "last_name"));
    $oSelect->where("is_active = ?", 1);
    $oSelect->order(array("first_name ASC"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getAllUser($aFilter = array(), $nCount = null, $nOffset = null, $sOrderBy = null)
  {
    if ($this->_oAuth->hasIdentity()) {
      if (!isset($sOrderBy))
        $sOrderBy = array("user_id ASC");
      $oSelect = $this->select();
      if (count($aFilter)) {
        foreach ($aFilter as $sKey => $mValue) {
          if ($mValue)
            if ($sKey == "user_name") {
              $oSelect->where("first_name LIKE ?", "%" . $mValue . "%");
              $oSelect->orWhere("last_name LIKE ?", "%" . $mValue . "%");
            } else {
              $oSelect->where("$sKey LIKE ?", "%" . $mValue . "%");
            }
        }
      }
      $oSelect->order($sOrderBy);
      $oSelect->limit($nCount, $nOffset);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
      return null;
    }
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order(array("first_name ASC"));
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getUserParam($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oSelect = $this->select();
      $oSelect->where("user_id = ?", $nUserId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow;
    }
    return null;
  }

  public function getUserImage($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oSelect = $this->select();
      $oSelect->where("user_id = ?", $nUserId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow;
    }
    return null;
  }

  public function getRecipientInfo($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oSelect = $this->select();
      $oSelect->from($this, array("email_address", "first_name", "last_name", "image"));
      $oSelect->where("user_id = ?", $nUserId);
      $oSelect->where("role_id IN (3, 4)");
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->toArray();
    }
    return null;
  }

  public function getAllRecipientInfo()
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("user_id", "email_address", "first_name", "last_name", "image"));
    $oSelect->where("role_id IN (3, 4)");
    $oSelect->order("last_name");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

}

?>
