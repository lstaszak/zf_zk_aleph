<?php

class User_Model_OrderFile extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "order_file";
  protected $_dependentTables = array("User_Model_OrderJournal");

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oSelect->order("id desc");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getNotExists()
  {
    $sQuery = "SELECT * FROM order_file WHERE NOT EXISTS (SELECT order_file_id FROM order_journal oj WHERE oj.order_file_id = order_file.id)";
    $aRow = $this->_db->fetchAll($sQuery);
    if ($aRow)
      return $aRow;
    return null;
  }

}

?>
