<?php

class Admin_Model_UserLog extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "user_log";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_user_user_log" => array(
      "columns" => array("user_id"),
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

  public function addLog($nUserId)
  {
    $oRow = $this->createRow();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $oRow->user_id = $nUserId;
      $oRow->server_addr = $this->getRealIpAddr();
      $oRow->date = time();
      return $oRow->save();
    }
    return null;
  }

  public function getRealIpAddr()
  {
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
      $sAddressIp = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      $sAddressIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
      $sAddressIp = $_SERVER["REMOTE_ADDR"];
    }
    return $sAddressIp;
  }

}

?>
