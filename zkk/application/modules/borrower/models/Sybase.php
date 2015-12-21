<?php

class Borrower_Model_Sybase extends AppCms2_Controller_Plugin_TableAbstractSybase
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
      $sQuery = "SELECT i.item#, i.call, i.csa_call, c.descr collection, i.location, i.volume, iwt.processed FROM item i INNER JOIN item_with_title iwt ON (i.item# = iwt.item#) LEFT JOIN collection c ON (i.collection = c.collection) WHERE i.item# = $nItemHash";
      $aRow = $this->_db->fetchAll($sQuery);
      return $aRow[0];
    }
    return null;
  }
}
