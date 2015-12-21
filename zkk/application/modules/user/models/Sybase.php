<?php

class User_Model_Sybase extends AppCms2_Controller_Plugin_TableAbstractSybase
{

  protected static $_instance = null;
  protected $_aLog;

  public function __construct($config = array())
  {
    $this->_aLog = array();
    parent::__construct($config);
  }

  public static function getInstance()
  {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function getJournalSettings($nItemHash)
  {
    if (is_numeric($nItemHash)) {
      $sQuery = "SELECT i.item#, i.call, i.collection, i.volume, iwt.processed FROM item i INNER JOIN item_with_title iwt ON (i.item# = iwt.item#) WHERE i.item# = $nItemHash";
      $aRow = $this->_db->fetchAll($sQuery);
      return $aRow[0];
    }
    return null;
  }

  public function getItemStatusAndRequestable($nItemHash)
  {
    if (is_numeric($nItemHash)) {
      $sQuery = "SELECT item_status, requestable FROM item WHERE item# = $nItemHash";
      $aRow = $this->_db->fetchAll($sQuery);
      return $aRow[0];
    }
    return null;
  }

  public function setItemStatusAndRequestable($nItemHash, $sItemStatus, $sRequestable)
  {
    if (is_numeric($nItemHash)) {
      if ($sItemStatus == "s") {
        $sRequestableCopy = 0;
      } else if ($sItemStatus == "article") {
        $sRequestableCopy = 1;
      }
      $sQuery = "UPDATE item SET item_status = '$sItemStatus', requestable = '$sRequestable', requestable_copy = '$sRequestableCopy' WHERE item# = $nItemHash";
      $this->_db->fetchAll($sQuery);
    }
    return null;
  }

}
