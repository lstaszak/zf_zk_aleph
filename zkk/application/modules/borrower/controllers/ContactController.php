<?php

class Borrower_ContactController extends Zend_Controller_Action
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
    $this->_redirect("borrower/contact/contact");
  }

  public function contactAction()
  {
    $oFormContact = new Admin_Form_Contact();
    $oModelUser = new Admin_Model_User();
    $sEmailAddress = $oModelUser->findEmailAddress($this->_nUserId);
    $oFormContact->populate(array("email_address" => $sEmailAddress));
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $oMail = new AppCms2_Controller_Plugin_Mail();
      $sEmailAddress = $oModelUser->findEmailAddress($this->_nUserId);
      if ($oFormContact->isValid($aPostData)) {
        $oMail->sendEmail(array("subject" => trim($sStr = "Kontakt z systemu zamawiania kopii - " . $aPostData["subject"]), "email_address" => $sEmailAddress, "user_name" => $this->_sUserName), $aPostData["message"]);
      }
      $oFormContact->clearForm();
    }
    $this->view->oFormContact = $oFormContact;
  }
}

?>
