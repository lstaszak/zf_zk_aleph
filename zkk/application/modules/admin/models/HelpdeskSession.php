<?php

class Admin_Model_HelpdeskSession extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "helpdesk_session";
  protected $_dependentTables = array("Admin_Model_Helpdesk");
  protected $_referenceMap = array(
    "fk_user_helpdesk_session" => array(
      "columns" => array("helpdesk_user_recipient_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_helpdesk_user_sender_helpdesk_session" => array(
      "columns" => array("helpdesk_user_sender_id"),
      "refTableClass" => "Admin_Model_HelpdeskUserSender",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getHelpdeskSession($nId)
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

  public function getActiveHelpdesk($nUserId)
  {
    $oSelect = $this->select();
    $oSelect->where("helpdesk_user_recipient_id = ?", $nUserId);
    $oSelect->where("init_date >= ?", time() - (3 * 60 * 60));
    $oSelect->where("last_update_date >= ?", time() - (60 * 60));
    $oSelect->where("finish_date IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function checkActiveHelpdesk($nId, $nHelpdeskUserRecipientId)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oRow = $this->find($nId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        if (($oRow->helpdesk_user_recipient_id == $nHelpdeskUserRecipientId) && ($oRow->init_date >= time() - (3 * 60 * 60)) && ($oRow->last_update_date >= time() - (60 * 60)) && ($oRow->finish_date != null))
          return true;
      }
    }
    return null;
  }

}

?>
