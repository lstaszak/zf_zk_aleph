<?php

class Admin_Model_Chat extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "chat";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_chat_message_chat" => array(
      "columns" => array("chat_message_id"),
      "refTableClass" => "Admin_Model_ChatMessage",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_chat_session_chat" => array(
      "columns" => array("chat_session_id"),
      "refTableClass" => "Admin_Model_ChatSession",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getMissingMessagesCount($nChatSessionId, $nLastMessageId)
  {
    if (is_numeric($nChatSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("chat_session_id = ?", $nChatSessionId);
      $oSelect->where("chat_message_id > ?", $nLastMessageId);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset->count();
    }
    return null;
  }

  public function getMissingMessages($nChatSessionId, $nLastMessageId)
  {
    if (is_numeric($nChatSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("chat_session_id = ?", $nChatSessionId);
      $oSelect->where("chat_message_id > ?", $nLastMessageId);
      $oSelect->order("chat_message_id ASC");
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
    }
    return null;
  }

  public function getLastMissingMessage($nChatSessionId, $nLastMessageId)
  {
    if (is_numeric($nChatSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("chat_session_id = ?", $nChatSessionId);
      $oSelect->where("chat_message_id > ?", $nLastMessageId);
      $oSelect->order("chat_message_id DESC");
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->chat_message_id;
    }
    return null;
  }

  public function getChat($nChatSessionId)
  {
    if (is_numeric($nChatSessionId)) {
      $oSelect = $this->select();
      $oSelect->where("chat_session_id = ?", $nChatSessionId);
      $oSelect->order("chat_message_id ASC");
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
    }
    return null;
  }

}

?>
