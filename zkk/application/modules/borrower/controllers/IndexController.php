<?php

class Borrower_IndexController extends Zend_Controller_Action
{
  private $_oAuth;
  private $_nUserId = null;
  private $_sRoleName = null;
  private $_sSiteUrl = null;
  private $_sUserName = null;

  public function preDispatch()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_sRoleName = $this->_oAuth->getStorage()->read()->role_name;
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
    $this->_redirect("borrower/orders");
  }
}

?>
