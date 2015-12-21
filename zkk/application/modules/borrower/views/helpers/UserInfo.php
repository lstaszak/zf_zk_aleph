<?php

class Zend_View_Helper_UserInfo extends Zend_View_Helper_Abstract
{
  private $_oAuth;
  private $_nUserId;

  public function __construct()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
  }

  public function __get($name)
  {
    return $this->$name;
  }

  public function userInfo()
  {
    if ($this->_oAuth->hasIdentity()) {
      $aUserInfo = array(
        "first_name" => $this->_oAuth->getStorage()->read()->first_name,
        "last_name" => $this->_oAuth->getStorage()->read()->last_name,
        "last_activity" => $this->_oAuth->getStorage()->read()->last_activity,
      );
    }
    $this->view->aUserInfo = $aUserInfo;
    return $this->view->render("_helpers/userinfo.phtml");
  }
}

?>
