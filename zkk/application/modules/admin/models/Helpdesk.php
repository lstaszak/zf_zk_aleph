<?php

class Admin_Model_Helpdesk extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "helpdesk";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_helpdesk_message_helpdesk" => array(
      "columns" => array("helpdesk_message_id"),
      "refTableClass" => "Admin_Model_HelpdeskMessage",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_helpdesk_session_helpdesk" => array(
      "columns" => array("helpdesk_session_id"),
      "refTableClass" => "Admin_Model_HelpdeskSession",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getMissingMessagesCount($nHelpdeskSessionId, $nLastMessageId)
  {
    if (is_numeric($nHelpdeskSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("helpdesk_session_id = ?", $nHelpdeskSessionId);
      $oSelect->where("helpdesk_message_id > ?", $nLastMessageId);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset->count();
    }
    return null;
  }

  public function getLastMissingMessage($nHelpdeskSessionId, $nLastMessageId)
  {
    if (is_numeric($nHelpdeskSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("helpdesk_session_id = ?", $nHelpdeskSessionId);
      $oSelect->where("helpdesk_message_id > ?", $nLastMessageId);
      $oSelect->order("helpdesk_message_id DESC");
      $oRow = $this->fetchRow($oSelect);
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->helpdesk_message_id;
    }
    return null;
  }

  public function getMissingMessages($nHelpdeskSessionId, $nLastMessageId)
  {
    if (is_numeric($nHelpdeskSessionId) && is_numeric($nLastMessageId)) {
      $oSelect = $this->select();
      $oSelect->where("helpdesk_session_id = ?", $nHelpdeskSessionId);
      $oSelect->where("helpdesk_message_id > ?", $nLastMessageId);
      $oSelect->order("helpdesk_message_id ASC");
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
    }
    return null;
  }

  public function getHelpdesk($nHelpdeskSessionId, $sOrder = "ASC")
  {
    if (is_numeric($nHelpdeskSessionId)) {
      $oSelect = $this->select();
      $oSelect->where("helpdesk_session_id = ?", $nHelpdeskSessionId);
      $oSelect->order("helpdesk_message_id $sOrder");
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
    }
    return null;
  }

}

?>
