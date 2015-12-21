<?php

class Admin_Model_Mail extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "mail";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_mail_message_mail" => array(
      "columns" => array("mail_message_id"),
      "refTableClass" => "Admin_Model_MailMessage",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_mail_session_mail" => array(
      "columns" => array("mail_session_id"),
      "refTableClass" => "Admin_Model_MailSession",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getMissingMessagesCount($nMailSessionId, $nLastMessageId)
  {
    if (is_numeric($nMailSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("mail_session_id = ?", $nMailSessionId);
      $oSelect->where("mail_message_id > ?", $nLastMessageId);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset->count();
    }
    return null;
  }

  public function getLastMissingMessage($nMailSessionId, $nLastMessageId)
  {
    if (is_numeric($nMailSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("mail_session_id = ?", $nMailSessionId);
      $oSelect->where("mail_message_id > ?", $nLastMessageId);
      $oSelect->order("mail_message_id DESC");
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->mail_message_id;
    }
    return null;
  }

  public function getMissingMessages($nMailSessionId, $nLastMessageId)
  {
    if (is_numeric($nMailSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("mail_session_id = ?", $nMailSessionId);
      $oSelect->where("mail_message_id > ?", $nLastMessageId);
      $oSelect->order("mail_message_id ASC");
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
    }
    return null;
  }

  public function getMail($nMailSessionId, $sOrder = "ASC")
  {
    if (is_numeric($nMailSessionId)) {
      $oSelect = $this->select();
      $oSelect->where("mail_session_id = ?", $nMailSessionId);
      $oSelect->order("mail_message_id $sOrder");
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
    }
    return null;
  }

}

?>
