<?php

class Borrower_SettingsController extends Zend_Controller_Action
{
  private $_oMail;
  private $_oAuth;
  private $_nUserId = null;
  private $_sSiteUrl = null;
  private $_sUserName = null;

  public function preDispatch()
  {
    $this->_oMail = new AppCms2_Controller_Plugin_Mail();
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_sSiteUrl = $this->_oAuth->getStorage()->read()->site_url;
      $this->_sUserName = $this->_oAuth->getStorage()->read()->first_name . " " . $this->_oAuth->getStorage()->read()->last_name;
    }
    if ($this->_oAuth->hasIdentity() && ($this->_sSiteUrl != str_replace("/", "", $this->view->baseUrl()))) {
      $this->_oAuth->clearIdentity();
    }
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("borrower/layout");
  }

  public function indexAction()
  {
    $this->_redirect("borrower/settings/changepassword");
  }

  public function changepasswordAction()
  {
    $oModelUser = new Admin_Model_User();
    $oFormChangePassword = new Admin_Form_ChangePassword();
    $aPostData = array();
    $sSuccess = "";
    if ($this->_request->isPost()) {
      $sSuccess = "NO OK";
      $aPostData = $this->_request->getPost();
      if ($oModelUser->updatePassword($aPostData["old_password"], $aPostData["new_password"])) {
        $sSuccess = "OK";
      }
    }
    $this->view->oFormChangePassword = $oFormChangePassword;
    $this->view->sSuccess = $sSuccess;
  }
}
