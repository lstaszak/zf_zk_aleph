<?php

class Admin_Model_ChatSession extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "chat_session";
  protected $_dependentTables = array(
    "Admin_Model_Chat"
  );
  protected $_referenceMap = array(
    "fk_chat_user_sender_chat_session" => array(
      "columns" => array("chat_user_sender_id"),
      "refTableClass" => "Admin_Model_ChatUserSender",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_user_chat_session" => array(
      "columns" => array("chat_user_recipient_id"),
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

  public function getChatSession($nId)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oSelect = $this->select();
      $oSelect->where("id = ?", $nId);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
    }
    return null;
  }

  public function getActiveChat($nUserId)
  {
    $oSelect = $this->select();
    $oSelect->where("chat_user_recipient_id = ?", $nUserId);
    $oSelect->where("init_date >= ?", time() - (3 * 60 * 60));
    $oSelect->where("last_update_date >= ?", time() - (60 * 60));
    $oSelect->where("finish_date IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function checkActiveChat($nId, $nChatUserRecipientId)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oRow = $this->find($nId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        if (($oRow->chat_user_recipient_id == $nChatUserRecipientId) && ($oRow->init_date >= time() - (3 * 60 * 60)) && ($oRow->last_update_date >= time() - (60 * 60)) && ($oRow->finish_date != null))
          return true;
      }
    }
    return null;
  }

  public function getIsWriting($nId)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oRow = $this->find($nId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return array("us" => $oRow->is_writing_user_sender, "ur" => $oRow->is_writing_user_recipient);
      }
    }
    return null;
  }

  public function getWholeTime($nWhichMonth, $aFilter = null)
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("SUM(ROUND((finish_date - init_date) / 60)) AS whole_time"));
    $oSelect->where("finish_date IS NOT NULL");
    $oSelect->where("init_date >= ?", mktime(0, 0, 0, ($nWhichMonth), 1, date("Y")));
    $oSelect->where("init_date <= ?", mktime(0, 0, 0, ($nWhichMonth + 1), 1, date("Y")));
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->whole_time;
    return null;
  }

}

?>
