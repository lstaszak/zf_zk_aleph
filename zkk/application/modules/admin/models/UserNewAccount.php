<?php

class Admin_Model_UserNewAccount extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "user_new_account";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_user_user_new_account" => array(
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

  public function addConfirmCode($nUserId, $sConfirmCode)
  {
    if (is_numeric($nUserId) && $nUserId > 0 && strlen($sConfirmCode) == 32) {
      $oRow = $this->createRow();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->user_id = (int)$nUserId;
        $oRow->activating_code = $sConfirmCode;
        $oRow->creation_date = time();
        return $oRow->save();
      }
    }
    return null;
  }

  public function confirmNewAccount($sActivatingCode)
  {
    $oSelect = $this->select();
    $oSelect->from($this, array('user_id'));
    $oSelect->where('activating_code = ?', $sActivatingCode);
    $oSelect->where('activation_date = ?', 0);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      return $oRow->user_id;
    }
    return null;
  }

  public function deleteConfirmCode($sActivatingCode)
  {
    $oSelect = $this->select();
    $oSelect->where('activating_code = ?', $sActivatingCode);
    $oSelect->where('activation_date = ?', 0);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->activation_date = time();
      return $oRow->save();
    }
    return null;
  }

}

?>
