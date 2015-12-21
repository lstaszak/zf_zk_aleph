<?php

class User_IndexController extends Zend_Controller_Action
{

  private $_oAuth;
  private $_nUserId = null;
  private $_sSiteUrl = null;

  public function preDispatch()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_sSiteUrl = $this->_oAuth->getStorage()->read()->site_url;
    }
    if ($this->_oAuth->hasIdentity() && ($this->_sSiteUrl != str_replace("/", "", $this->view->baseUrl())))
      $this->_oAuth->clearIdentity();
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("user/layout");
  }

  public function indexAction()
  {
    $this->_redirect("user/orders");
  }

}

?>
