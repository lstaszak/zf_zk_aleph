<?php

class Admin_Model_MailSession extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "mail_session";
  protected $_dependentTables = array("Admin_Model_Mail");
  protected $_referenceMap = array(
    "fk_user_mail_session" => array(
      "columns" => array("mail_user_recipient_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_mail_user_sender_mail_session" => array(
      "columns" => array("mail_user_sender_id"),
      "refTableClass" => "Admin_Model_MailUserSender",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getMailSession($nId)
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

  public function getActiveMail($nUserId)
  {
    $oSelect = $this->select();
    $oSelect->where("mail_user_recipient_id = ?", $nUserId);
    $oSelect->where("init_date >= ?", time() - (3 * 60 * 60));
    $oSelect->where("last_update_date >= ?", time() - (60 * 60));
    $oSelect->where("finish_date IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function checkActiveMail($nId, $nMailUserRecipientId)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oRow = $this->find($nId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        if (($oRow->mail_user_recipient_id == $nMailUserRecipientId) && ($oRow->init_date >= time() - (3 * 60 * 60)) && ($oRow->last_update_date >= time() - (60 * 60)) && ($oRow->finish_date != null))
          return true;
      }
    }
    return null;
  }

}

?>
