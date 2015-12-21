<?php

class Admin_Model_Notification extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "notification";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_notification_category_notification" => array(
      "columns" => array("notification_category_id"),
      "refTableClass" => "Admin_Model_NotificationCategory",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
    "fk_notification_priority_notification" => array(
      "columns" => array("notification_priority_id"),
      "refTableClass" => "Admin_Model_NotificationPriority",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_notification_status_notification" => array(
      "columns" => array("notification_status_id"),
      "refTableClass" => "Admin_Model_NotificationStatus",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_notification_type_notification" => array(
      "columns" => array("notification_type_id"),
      "refTableClass" => "Admin_Model_NotificationType",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    ),
    "fk_user_notification" => array(
      "columns" => array("user_id"),
      "refTableClass" => "Admin_Model_User",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    ),
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getStatistics()
  {
    $oSelect = $this->select();
    $oSelect->from($this, array("notification_priority_id, count(*)"));
    $oSelect->group("notification_priority_id");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getPrevNotification($nId, $nNotificationStatusId = null)
  {
    $oSelect = $this->select();
    $oSelect->where("id < ?", $nId);
    if (isset($nNotificationStatusId))
      $oSelect->where("notification_status_id = ?", $nNotificationStatusId);
    $oSelect->where("is_hidden IS NULL");
    $oSelect->order("id desc");
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->id;
    return null;
  }

  public function getNextNotification($nId, $nNotificationStatusId = null)
  {
    $oSelect = $this->select();
    $oSelect->where("id > ?", $nId);
    if (isset($nNotificationStatusId))
      $oSelect->where("notification_status_id = ?", $nNotificationStatusId);
    $oSelect->where("is_hidden IS NULL");
    $oSelect->order("id asc");
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->id;
    return null;
  }

  public function deleteRow($nId)
  {
    if (is_numeric($nId) && $nId > 0) {
      $oRow = $this->find($nId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->is_hidden = 1;
        return $oRow->save();
      }
    }
    return null;
  }

  public function getMissingNotifications()
  {
    $oRow = $this->getLastRow();
    if ($oRow->init_date > time() - 20)
      return true;
    return null;
  }

}

?>
