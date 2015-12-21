<?php

class Admin_Model_VChatHistory extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_chat_history";
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
        $sOrderBy = array("init_date ASC");
      $oSelect = $this->select();
      if (count($aFilter)) {
        foreach ($aFilter as $sKey => $mValue) {
          if ($sKey != "init_date") {
            $oSelect->where("$sKey LIKE ?", "%$mValue%");
          }
        }
      }
      if (isset($aFilter["init_date"])) {
        $oSelect->where("init_date >= ?", $aFilter["init_date"]);
        $oSelect->where("init_date <= ?", $aFilter["init_date"] + 86400);
      }
      $oSelect->order($sOrderBy);
      $oSelect->limit($nCount, $nOffset);
      $oRowset = $this->fetchAll($oSelect);
      if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
        return $oRowset;
      else
        return null;
    } else {
      throw new Zend_Exception();
    }
  }

}

?>
