<?php

class Admin_Model_HelpdeskUserSender extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "helpdesk_user_sender";
  protected $_dependentTables = array("Admin_Model_HelpdeskSession");
  protected $_referenceMap = array(
    "fk_user_helpdesk_user_sender" => array(
      "columns" => array("user_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getUserParam($nId)
  {
    $oModelUser = new Admin_Model_User();
    $oRow = $this->find($nId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $aRow = $oModelUser->findUser($oRow->user_id);
      return $aRow;
    }
    return null;
  }

  public function getServerAddr($nId)
  {
    $oRow = $this->find($nId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      return $oRow->server_addr;
    }
    return null;
  }

}

?>
