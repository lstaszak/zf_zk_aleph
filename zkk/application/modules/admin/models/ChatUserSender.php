<?php

class Admin_Model_ChatUserSender extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "chat_user_sender";
  protected $_dependentTables = array(
    "Admin_Model_ChatSession"
  );
  protected $_referenceMap = array(
    "fk_user_category_chat_user_sender" => array(
      "columns" => array("user_category_id"),
      "refTableClass" => "Admin_Model_UserCategory",
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
    if (is_numeric($nId)) {
      $oSelect = $this->select();
      $oSelect->where("id = ?", $nId);
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow;
      }
    }
    return null;
  }

}

?>
