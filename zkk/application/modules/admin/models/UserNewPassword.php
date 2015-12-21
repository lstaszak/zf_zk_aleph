<?php

class Admin_Model_UserNewPassword extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "user_new_password";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_user_user_new_password" => array(
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

  public function addPassword($nUserId, $aParam)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oRow = $this->createRow();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->user_id = (int)$nUserId;
        $oRow->new_password = md5(md5($aParam["hash"]) . $aParam["salt"]);
        $oRow->activating_code = $aParam["activating_code"];
        $oRow->creation_date = time();
        return $oRow->save();
      }
    }
    return null;
  }

  public function confirmNewPassword($sActivatingCode)
  {
    $oSelect = $this->select();
    $oSelect->where("activating_code = ?", $sActivatingCode);
    $oSelect->where("activation_date = ?", 0);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow;
    return null;
  }

  public function deleteConfirmCode($sActivatingCode)
  {
    $oSelect = $this->select();
    $oSelect->where("activating_code = ?", $sActivatingCode);
    $oSelect->where("activation_date = ?", 0);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->activation_date = time();
      return $oRow->save();
    }
    return null;
  }

  public function findNewPassword($nUserId)
  {
    $oRow = $this->find($nUserId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->email_address;
    return null;
  }

}

?>
