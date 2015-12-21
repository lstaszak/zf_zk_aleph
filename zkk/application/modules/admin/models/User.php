<?php

class Admin_Model_User extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "user";
  protected $_dependentTables = array(
    "Admin_Model_UserParam",
    "Admin_Model_UserLog",
    "Admin_Model_UserNewAccount",
    "Admin_Model_UserNewPassword",
    "Admin_Model_Image",
    "Admin_Model_ChatSession",
    "Admin_Model_MailSession",
    "Admin_Model_HelpdeskSession",
    "Admin_Model_Notification",
    "Admin_Model_DefaultContact",
    "Admin_Model_HelpdeskUserSender"
  );
  protected $_referenceMap = array(
    "fk_user_role_user" => array(
      "columns" => array("user_role_id"),
      "refTableClass" => "Admin_Model_UserRole",
      "refColumns" => array("id"),
      "onDelete" => self::CASCADE,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  public function getAll()
  {
    $oSelect = $this->select();
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function checkUser($nUserId, $sEmailAddress)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract && $sEmailAddress == $oRow->email_address)
        return true;
    }
    return null;
  }

  public function getAllEmailAddress()
  {
    $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART);
    $oSelect->setIntegrityCheck(false);
    $oSelect->join(array("up" => "user_param"), "user.id = up.user_id", array("up.first_name", "up.last_name"));
    $oSelect->columns("email_address");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function newUser($aParam)
  {
    if (is_array($aParam) && count($aParam)) {
      $oModelUserParm = new Admin_Model_UserParam();
      $oModelUserNewAccount = new Admin_Model_UserNewAccount();
      $oGenerateSessionId = new AppCms2_GenereteSessionId();
      $oBootstrap = Zend_Controller_Front::getInstance()->getParam("bootstrap");
      $sOptions = $oBootstrap->getOptions();
      try {
        $this->_db->beginTransaction();
        $nTime = time();
        $sSalt = md5(sha1($nTime . $sOptions["resources"]["frontController"]["salt"] . $nTime));
        $oRow = $this->createRow();
        if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
          if (!isset($aParam["role_id"]))
            $oRow->user_role_id = 1;
          else
            $oRow->user_role_id = $aParam["role_id"];
          $oRow->email_address = $aParam["email_address"];
          $oRow->password = md5(md5($aParam["password"]) . $sSalt);
          $oRow->salt = $sSalt;
          $oRow->created_date = $nTime;
          $oRow->is_active = $aParam["is_active"];
          $nUserId = $oRow->save();
          if ($oModelUserParm->newUserParam($nUserId, $aParam)) {
            $sConfirmCode = $oGenerateSessionId->generate();
            if ($oModelUserNewAccount->addConfirmCode($nUserId, $sConfirmCode)) {
              $this->_db->commit();
              return $sConfirmCode;
            }
          }
        }
      } catch (Zend_Exception $e) {
        $this->_db->rollBack();
        return null;
      }
    }
    return null;
  }

  public function activatingNewUser($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->is_active = 1;
        return $oRow->save();
      }
    }
    return null;
  }

  public function enableUser($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->is_active = 1;
        return $oRow->save();
      }
    }
    return null;
  }

  public function disableUser($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->is_active = 0;
        return $oRow->save();
      }
    }
    return null;
  }

  public function activatingNewPassword($nUserId, $sNewPassword)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->password = $sNewPassword;
        return $oRow->save();
      }
    }
    return null;
  }

  public function findEmailAddress($nUserId)
  {
    $oRow = $this->find($nUserId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      return $oRow->email_address;
    }
    return null;
  }

  public function findUserByEmailAddress($sEmailAddress, $nIsActive = null)
  {
    $oSelect = $this->select();
    $oSelect->where("email_address = ?", $sEmailAddress);
//    if (isset($nIsActive))
//      $oSelect->where("is_active = ?", $nIsActive);
//    else
//      $oSelect->where("is_active = ?", 0);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract)
      return $oRow->id;
    return null;
  }

  public function getUserRole($nUserId)
  {
    if (is_numeric($nUserId)) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->user_role_id;
      }
    }
    return null;
  }

  public function findAskOnline()
  {
    $oSelect = $this->select();
    $oSelect->where("ask_online = ?", 1);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract) {
      if ($oRowset->count())
        return $oRowset;
    }
    return null;
  }

  public function findAskRecipients()
  {
    $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART);
    $oSelect->setIntegrityCheck(false);
    $oSelect->join(array("up" => "user_param"), "user.id = up.user_id", array("up.first_name", "up.last_name"));
    $oSelect->where("user_role_id IN (3, 4)");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function getAskOnline($nUserId)
  {
    if (is_numeric($nUserId)) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        return $oRow->ask_online;
      }
    }
    return null;
  }

  public function setAskOnline($nUserId, $nValue)
  {
    if (is_numeric($nUserId)) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
        $oRow->ask_online = $nValue;
        return $oRow->save();
      }
    }
    return null;
  }

  public function findUser($nUserId)
  {
    $oRow = $this->find($nUserId)->current();
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      $aRow = $oRow->findDependentRowset("Admin_Model_UserParam")->toArray();
      if (is_array($aRow) && count($aRow) == 1):
        $aRow[0]["user_id"] = $oRow->id;
        $aRow[0]["user_role_id"] = $oRow->user_role_id;
        $aRow[0]["email_address"] = $oRow->email_address;
        return $aRow[0];
      endif;
    }
    return null;
  }

  public function findUsers($mUserRoleId)
  {
    $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART);
    $oSelect->setIntegrityCheck(false);
    $oSelect->join(array("up" => "user_param"), "user.id = up.user_id", array("up.first_name", "up.last_name"));
    $oSelect->where("user_role_id IN (?)", $mUserRoleId);
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

  public function updatePassword($sOldPassword, $sNewPassword)
  {
    if (is_string($sOldPassword) && is_string($sNewPassword)) {
      $oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
      $nUserId = $oAuth->getStorage()->read()->user_id;
      if (is_numeric($nUserId)) {
        $oRow = $this->find($nUserId)->current();
        if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
          if ($oRow->password != md5(md5($sOldPassword) . $oRow->salt))
            return null;
          $oRow->password = md5(md5($sNewPassword) . $oRow->salt);
          return $oRow->save();
        }
      }
    }
    return null;
  }

  public function deleteRow($nUserId)
  {
    if (is_numeric($nUserId))
      return $this->delete($this->_db->quoteInto("id = ?", $nUserId));
    return null;
  }

  public function editUser($nUserId, $aParam)
  {
    $oModelUserParm = new Admin_Model_UserParam();
    if (is_numeric($nUserId) && $nUserId > 0) {
      try {
        $this->_db->beginTransaction();
        $oRow = $this->find($nUserId)->current();
        if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
          $oRow->user_role_id = $aParam["role_id"];
          $oRow->is_active = $aParam["is_active"];
          if ($oRow->save()) {
            if ($oModelUserParm->editUserParam($nUserId, $aParam))
              $this->_db->commit();
          }
        }
      } catch (Zend_Exception $e) {
        $this->_db->rollBack();
        return null;
      }
    }
    return null;
  }

  public function ajaxLogin($sEmailAddress, $sPassword)
  {
    $oSelect = $this->select();
    $oSelect->where("email_address = ?", $sEmailAddress);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      if (!($oRow->is_active === 1 && $oRow->password === md5(md5($sPassword) . $oRow->salt)))
        return true;
    }
    return null;
  }

  public function newUserFb($aParam)
  {
    if (is_array($aParam) && count($aParam)) {
      $nUserId = $this->findUserByEmailAddress($aParam["email"]);
      if ($nUserId) {
        $this->editRow($nUserId, array("user_fb_id" => $aParam["id"]));
        $aRow = $this->findUser($nUserId);
        return $aRow;
      } else {
        $oModelUserParm = new Admin_Model_UserParam();
        try {
          $this->_db->beginTransaction();
          $oRow = $this->createRow();
          if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
            $oRow->user_role_id = 5;
            $oRow->user_fb_id = $aParam["id"];
            $oRow->email_address = $aParam["email"];
            $oRow->created_date = time();
            $oRow->is_active = 2;
            $nUserId = $oRow->save();
            if ($oModelUserParm->newUserParam($nUserId, $aParam)) {
              $this->_db->commit();
              return $oRow->toArray();
            }
          }
        } catch (Zend_Exception $e) {
          $this->_db->rollBack();
          return null;
        }
      }
    }
    return null;
  }

  public function findUserByFbId($sUserFbId)
  {
    $oSelect = $this->select();
    $oSelect->where("user_fb_id = ?", $sUserFbId);
    $oRow = $this->fetchRow($oSelect);
    if ($oRow instanceof Zend_Db_Table_Row_Abstract) {
      return $oRow;
    }
    return null;
  }

  public function getUserSalt($nUserId)
  {
    if (is_numeric($nUserId) && $nUserId > 0) {
      $oRow = $this->find($nUserId)->current();
      if ($oRow instanceof Zend_Db_Table_Row_Abstract)
        return $oRow->salt;
    }
    return null;
  }

  public function findAdminRecipients()
  {
    $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART);
    $oSelect->setIntegrityCheck(false);
    $oSelect->join(array("up" => "user_param"), "user.id = up.user_id", array("up.first_name", "up.last_name"));
    $oSelect->where("user_role_id IN (3, 4)");
    $oRowset = $this->fetchAll($oSelect);
    if ($oRowset instanceof Zend_Db_Table_Rowset_Abstract)
      return $oRowset;
    return null;
  }

}

?>
