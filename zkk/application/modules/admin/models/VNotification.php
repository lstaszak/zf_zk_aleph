<?php

class Admin_Model_VNotification extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_notification";
  protected $_primary = "id";
  private $_oAuth;
  private $_nUserId;

  public function __construct($config = array())
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
    parent::__construct($config);
  }

  public function getAll($aFilter = array(), $nCount = null, $nOffset = null, $sOrderBy = null)
  {
    if ($this->_oAuth->hasIdentity()) {
      if (!isset($sOrderBy))
        $sOrderBy = array("id ASC");
      $oSelect = $this->select();
      if (count($aFilter)) {
        foreach ($aFilter as $sKey => $mValue) {
          if (in_array($sKey, array("notification_type_id", "notification_priority_id", "notification_status_id"))) {
            $sWhere = null;
            foreach ($mValue as $nKey => $nValue) {
              if (!$nKey)
                $sWhere = "$sKey = $nValue";
              else
                $sWhere .= " OR $sKey = $nValue";
            }
            $oSelect->where($sWhere);
          }
          if ($sKey == "notification_category_id") {
            if ($mValue)
              $oSelect->where("$sKey = ?", $mValue);
          }
          if ($sKey == "user_id" && isset($aFilter["search_blank"])) {
            if ($aFilter["search_blank"] == 1) {
              $oSelect->where("$sKey = $mValue OR $sKey IS NULL");
            } else {
              if ($mValue)
                $oSelect->where("$sKey = ?", $mValue);
            }
          }
          if ($sKey == "user_id" && !isset($aFilter["search_blank"])) {
            if ($mValue)
              $oSelect->where("$sKey = ?", $mValue);
          }
          if ($sKey == "search_blank" && !isset($aFilter["user_id"])) {
            if ($aFilter["search_blank"] == 1) {
              $oSelect->where("user_id IS NULL");
            }
          }
          if ($sKey == "init_date") {
            $oSelect->where("$sKey >= ?", $mValue);
            $oSelect->where("$sKey <= ?", $mValue + 86400);
          }
        }
      }
      $oSelect->where("is_hidden IS NULL");
      $oSelect->order($sOrderBy);
      $oSelect->limit($nCount, $nOffset);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
      return null;
    }
  }

  public function getAllBorrower($nCount = null, $nOffset = null, $sOrderBy = null)
  {
    if ($this->_oAuth->hasIdentity()) {
      $sQuery = "SELECT id FROM notification WHERE x_session_id IN (SELECT hs.id FROM helpdesk_session hs INNER JOIN helpdesk_user_sender hus ON (hs.helpdesk_user_sender_id = hus.id) WHERE hus.user_id = $this->_nUserId) AND notification_type_id = 3";
      $aTempBorrowerNotificationId = $this->_db->fetchAll($sQuery);
      if (count($aTempBorrowerNotificationId)) {
        $aBorrowerNotificationId = array();
        foreach ($aTempBorrowerNotificationId as $nKey => $aValue) {
          array_push($aBorrowerNotificationId, $aValue["id"]);
        }
      }
      if (!isset($sOrderBy))
        $sOrderBy = array("id ASC");
      $oSelect = $this->select();
      $oSelect->where("id IN (?)", $aBorrowerNotificationId);
      $oSelect->where("is_hidden IS NULL");
      $oSelect->order($sOrderBy);
      $oSelect->limit($nCount, $nOffset);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
      return null;
    }
  }

  public function getCurrentMonthCount($aFilter = null)
  {
    $oSelect = $this->select();
    if (isset($aFilter["user_id"]))
      $oSelect->where("user_id = ?", $aFilter["user_id"]);
    $oSelect->where("init_date >= ?", mktime(0, 0, 0, date("m"), 1, date("Y")));
    $oSelect->where("init_date <= ?", mktime(0, 0, 0, (date("m") + 1), 1, date("Y")));
    $oSelect->where("is_hidden IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset)
      return $oRowset->count();
    return 0;
  }

  public function getMonthCount($nWhichMonth, $aFilter = null)
  {
    $oSelect = $this->select();
    if (isset($aFilter["user_id"]))
      $oSelect->where("user_id = ?", $aFilter["user_id"]);
    if (isset($aFilter["notification_type_id"]))
      $oSelect->where("notification_type_id = ?", $aFilter["notification_type_id"]);
    else if (isset($aFilter["notification_status_id"]))
      $oSelect->where("notification_status_id = ?", $aFilter["notification_status_id"]);
    else if (isset($aFilter["notification_priority_id"]))
      $oSelect->where("notification_priority_id = ?", $aFilter["notification_priority_id"]);
    $oSelect->where("init_date >= ?", mktime(0, 0, 0, ($nWhichMonth), 1, date("Y")));
    $oSelect->where("init_date <= ?", mktime(0, 0, 0, ($nWhichMonth + 1), 1, date("Y")));
    $oSelect->where("is_hidden IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset)
      return $oRowset->count();
    return 0;
  }

  public function getNotificationTypeCount($aFilter = null)
  {
    $oSelect = $this->select();
    if (isset($aFilter["user_id"]))
      $oSelect->where("user_id = ?", $aFilter["user_id"]);
    if (isset($aFilter["notification_type_id"]))
      $oSelect->where("notification_type_id = ?", $aFilter["notification_type_id"]);
    if (isset($aFilter["from_init_date"]))
      $oSelect->where("init_date >= ?", $aFilter["from_init_date"]);
    if (isset($aFilter["to_init_date"]))
      $oSelect->where("init_date <= ?", $aFilter["to_init_date"]);
    $oSelect->where("is_hidden IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset)
      return $oRowset->count();
    return 0;
  }

  public function getNotificationStatusCount($aFilter = null)
  {
    $oSelect = $this->select();
    if (isset($aFilter["user_id"]))
      $oSelect->where("user_id = ?", $aFilter["user_id"]);
    if (isset($aFilter["notification_status_id"]))
      $oSelect->where("notification_status_id = ?", $aFilter["notification_status_id"]);
    if (isset($aFilter["from_init_date"]))
      $oSelect->where("init_date >= ?", $aFilter["from_init_date"]);
    if (isset($aFilter["to_init_date"]))
      $oSelect->where("init_date <= ?", $aFilter["to_init_date"]);
    $oSelect->where("is_hidden IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset)
      return $oRowset->count();
    return 0;
  }

  public function getNotificationPriorityCount($aFilter = null)
  {
    $oSelect = $this->select();
    if (isset($aFilter["user_id"]))
      $oSelect->where("user_id = ?", $aFilter["user_id"]);
    if (isset($aFilter["notification_priority_id"]))
      $oSelect->where("notification_priority_id = ?", $aFilter["notification_priority_id"]);
    if (isset($aFilter["from_init_date"]))
      $oSelect->where("init_date >= ?", $aFilter["from_init_date"]);
    if (isset($aFilter["to_init_date"]))
      $oSelect->where("init_date <= ?", $aFilter["to_init_date"]);
    $oSelect->where("is_hidden IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset)
      return $oRowset->count();
    return 0;
  }

  public function getNotificationCategoryCount($aFilter = null)
  {
    $oSelect = $this->select();
    if (isset($aFilter["user_id"]))
      $oSelect->where("user_id = ?", $aFilter["user_id"]);
    if (isset($aFilter["notification_category_id"]))
      $oSelect->where("notification_category_id = ?", $aFilter["notification_category_id"]);
    if (isset($aFilter["from_init_date"]))
      $oSelect->where("init_date >= ?", $aFilter["from_init_date"]);
    if (isset($aFilter["to_init_date"]))
      $oSelect->where("init_date <= ?", $aFilter["to_init_date"]);
    $oSelect->where("is_hidden IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset)
      return $oRowset->count();
    return 0;
  }

  public function getUserNotificationCount($aFilter = null)
  {
    $oSelect = $this->select();
    if (isset($aFilter["user_id"]))
      $oSelect->where("user_id = ?", $aFilter["user_id"]);
    if (isset($aFilter["from_init_date"]))
      $oSelect->where("init_date >= ?", $aFilter["from_init_date"]);
    if (isset($aFilter["to_init_date"]))
      $oSelect->where("init_date <= ?", $aFilter["to_init_date"]);
    $oSelect->where("is_hidden IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset)
      return $oRowset->count();
    return 0;
  }

  public function getCount($aFilter = null)
  {
    $oSelect = $this->select();
    if (isset($aFilter["user_id"])) {
      if ($aFilter["user_id"] === 0)
        $oSelect->where("user_id IS NULL");
      else
        $oSelect->where("user_id = ?", $aFilter["user_id"]);
    }
    if (isset($aFilter["notification_status_id"]))
      $oSelect->where("notification_status_id = ?", $aFilter["notification_status_id"]);
    if (isset($aFilter["from_init_date"]))
      $oSelect->where("init_date >= ?", $aFilter["from_init_date"]);
    if (isset($aFilter["to_init_date"]))
      $oSelect->where("init_date <= ?", $aFilter["to_init_date"]);
    $oSelect->where("is_hidden IS NULL");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset)
      return $oRowset->count();
    return 0;
  }

}

?>
