<?php

class Admin_IndexController extends Zend_Controller_Action
{

  private $_oAuth;
  private $_nUserId = null;
  private $_nRoleName = null;
  private $_sSiteUrl = null;

  public function preDispatch()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_nRoleName = $this->_oAuth->getStorage()->read()->role_name;
      $this->_sSiteUrl = $this->_oAuth->getStorage()->read()->site_url;
    }
    if ($this->_oAuth->hasIdentity() && ($this->_sSiteUrl != str_replace("/", "", $this->view->sBaseUrl))) {
      $this->_oAuth->clearIdentity();
    } else {
      $this->_redirect("/admin/settings");
    }
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("admin/layout");
  }

  public function indexAction()
  {

  }

}

?>
