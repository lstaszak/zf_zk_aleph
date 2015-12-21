<?php

class Admin_UserController extends Zend_Controller_Action
{

  private $_oAuth;
  private $_nUserId = null;
  private $_nRoleName = null;
  private $_sSiteUrl = null;
  private $_sFirstName = null;
  private $_sLastName = null;

  public function preDispatch()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_nRoleName = $this->_oAuth->getStorage()->read()->role_name;
      $this->_sSiteUrl = $this->_oAuth->getStorage()->read()->site_url;
      $this->_sFirstName = $this->_oAuth->getStorage()->read()->first_name;
      $this->_sLastName = $this->_oAuth->getStorage()->read()->last_name;
    }
    if ($this->_oAuth->hasIdentity() && ($this->_sSiteUrl != str_replace("/", "", $this->view->sBaseUrl))) {
      $this->_oAuth->clearIdentity();
    }
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("borrower/layout_login");
  }

  public function indexAction()
  {
    $this->_helper->layout()->setLayout("borrower/layout_login_index");
  }

  public function loginAction()
  {
    $this->_helper->layout()->setLayout("borrower/layout_login");
    $oFormLogin = new Admin_Form_Login();
    $oFacebook = new Facebook_Facebook();
    if (!$this->_oAuth->hasIdentity() && $oFacebook->getUser()) {
      $oFacebook->destroySession();
    }
    $aPostData = array();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormLogin->isValid($aPostData)) {
        $sEmailAddress = $oFormLogin->getValue("user_email_address") != "" ? $oFormLogin->getValue("user_email_address") : "";
        $sPassword = $oFormLogin->getValue("user_password");
        $oResult = $this->_oAuth->auth($sEmailAddress, $sPassword);
        if ($oResult->isValid())
          $this->_redirect("/admin");
      }
    }
    $oFormLogin->populate($aPostData);
    $this->view->oFormLogin = $oFormLogin;
  }

  public function fbloginAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oModelUser = new Admin_Model_User();
    $oFacebook = new Facebook_Facebook();
    $sUserFbId = $oFacebook->getUser();
    if (is_string($sUserFbId)) {
      $oUser = $oModelUser->findUserByFbId($sUserFbId);
      $aParam = $oFacebook->api("/me");
      if (isset($oUser)) {
        $aParam["user_id"] = $oUser->id;
        $aParam["user_role_id"] = $oUser->user_role_id;
        $bResult = $this->_oAuth->fbAuth($aParam);
      } else {
        $aUser = $oModelUser->newUserFb($aParam);
        if (isset($aUser)):
          $aParam["user_id"] = $aUser["id"];
          $aParam["user_role_id"] = $aUser["user_role_id"];
          $bResult = $this->_oAuth->fbAuth($aParam);
        endif;
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($bResult);
    exit;
  }

  public function logoutAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oFacebook = new Facebook_Facebook();
    $oModelUser = new Admin_Model_User();
    if (is_string($oFacebook->getUser())) {
      $sLogoutUrl = $oFacebook->getLogoutUrl();
      if ($this->_oAuth->hasIdentity()) {
        $this->_oAuth->clearIdentity();
        $oModelUser->setAskOnline($this->_nUserId, 0);
      }
      $oFacebook->destroySession();
      $this->_redirect($sLogoutUrl);
    } else if ($this->_oAuth->hasIdentity()) {
      $this->_oAuth->clearIdentity();
      $oModelUser->setAskOnline($this->_nUserId, 0);
    }
    $this->_redirect("/");
  }

  public function registerAction()
  {
    $this->_helper->layout()->setLayout("borrower/layout_login");
    $oModelUser = new Admin_Model_User();
    $oFormRegister = new Admin_Form_RegisterBorrower();
    $aPostData = array();
    $aParam = array();
    $sSuccess = "";
    $aSeparators = array("-", "/", ".", "+", " ");
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormRegister->isValid($aPostData)) {
        $sEmailAddress = $oFormRegister->getValue("email_address");
        $nUserId = $oModelUser->findUserByEmailAddress($sEmailAddress, 2);
        if (!$nUserId) {
          $aParam["role_id"] = 6;
          $aParam["first_name"] = $oFormRegister->getValue("first_name");
          $aParam["last_name"] = $oFormRegister->getValue("last_name");
          $aParam["email_address"] = $oFormRegister->getValue("email_address");
          $aParam["email_address_confirm"] = $oFormRegister->getValue("email_address_confirm");
          $aParam["password"] = $oFormRegister->getValue("password");
          $aParam["phone_number"] = str_replace($aSeparators, "", $oFormRegister->getValue("phone_number"));
          $aParam["is_active"] = (int)$oFormRegister->getValue("is_active");
          $aParam["statute"] = $oFormRegister->getValue("statute");
          $sConfirmCode = $oModelUser->newUser($aParam);
          $aParam["confirm_code"] = $sConfirmCode;
          if ($sConfirmCode) {
            $oMail = new AppCms2_Controller_Plugin_Mail();
            $oMail->sendUserAccountRegistration($sEmailAddress, $aParam); //mail do użytkownika z informacją o rejestracji i aktywacji konta w systemie (w tym mailu wysłane jest hasło)
            //$oMail = new AppCms2_Controller_Plugin_Mail();
            //$oMail->sendAdminConfirmRegistrationAndActivation($aParam); //mail do administratora z informacją o rejestracji i aktywacji konta w systemie (w tym mailu wysłane jest hasło)
            $sSuccess = "OK";
          } else {
            $sSuccess = "NO OK";
          }
        } else {
          if ($oModelUser->editRow($nUserId, array("password" => md5($oFormRegister->getValue("user_password")), "is_active" => 1)))
            $sSuccess = "OK";
        }
      }
    }
    $this->view->oFormRegister = $oFormRegister;
    $this->view->sSuccess = $sSuccess;
  }

  public function confirmnewaccountAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oModelUser = new Admin_Model_User();
    $oModelUserNewAccount = new Admin_Model_UserNewAccount();
    $sActivatingCode = $this->_request->getParam("code");
    if (isset($sActivatingCode) && is_string($sActivatingCode) && strlen($sActivatingCode) == 32) {
      $nUserId = $oModelUserNewAccount->confirmNewAccount($sActivatingCode);
      if (!is_numeric($nUserId))
        $this->_redirect("admin/user/login");
      if ($oModelUser->activatingNewUser($nUserId)) {
        $sEmailAddress = $oModelUser->findEmailAddress($nUserId);
        $oModelUserNewAccount->deleteConfirmCode($sActivatingCode);
        $oMail = new AppCms2_Controller_Plugin_Mail();
        $oMail->sendUserAccountActivation($sEmailAddress);
      }
    }
    $this->_redirect("admin/user/login");
  }

  public function passwordremindAction()
  {
    $this->_helper->layout()->setLayout("borrower/layout_login");
    $oModelUser = new Admin_Model_User();
    $oModelVUser = new Admin_Model_VUser();
    $oModelUserNewPassword = new Admin_Model_UserNewPassword();
    $oFormPasswordRemind = new Admin_Form_PasswordRemind();
    $oGenereteSessionId = new AppCms2_GenereteSessionId();
    $aPostData = array();
    $sSuccess = "";
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormPasswordRemind->isValid($aPostData)) {
        $sEmailAddress = $oFormPasswordRemind->getValue("user_email_address");
        $nUserId = $oModelUser->findUserByEmailAddress($sEmailAddress);
        if (is_numeric($nUserId)) {
          $aParam = $oModelVUser->getUserParam($nUserId)->toArray();
          $aParam["salt"] = $oModelUser->getUserSalt($nUserId);
          $aParam = array_merge($aParam, $oGenereteSessionId->generatePassword($aParam["salt"]));
          if ($oModelUserNewPassword->addPassword($nUserId, $aParam)) {
            $sEmailAddress = $oModelUser->findEmailAddress($nUserId);
            $oMail = new AppCms2_Controller_Plugin_Mail();
            $oMail->sendNewPassword($sEmailAddress, $aParam);
            $sSuccess = "OK";
          } else {
            $sSuccess = "NO OK";
          }
        } else {
          $sSuccess = "USER";
        }
      }
    }
    $this->view->oFormPasswordRemind = $oFormPasswordRemind;
    $this->view->sSuccess = $sSuccess;
  }

  public function confirmchangepasswordAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oModelUser = new Admin_Model_User();
    $oModelUserNewPassword = new Admin_Model_UserNewPassword();
    $oModelVUser = new Admin_Model_VUser();
    $sActivatingCode = $this->_request->getParam("code");
    if (isset($sActivatingCode) && is_string($sActivatingCode) && strlen($sActivatingCode) == 32) {
      $oRow = $oModelUserNewPassword->confirmNewPassword($sActivatingCode);
      if (!isset($oRow) && is_string($oRow->new_password) && strlen($oRow->new_password) == 32)
        $this->_redirect("user/login");
      if ($oModelUser->activatingNewPassword($oRow->user_id, $oRow->new_password)) {
        $oModelUserNewPassword->deleteConfirmCode($sActivatingCode);
        $sEmailAddress = $oModelUser->findEmailAddress($oRow->user_id);
        $aParam = $oModelVUser->getUserParam($oRow->user_id)->toArray();
        $oMail = new AppCms2_Controller_Plugin_Mail();
        $oMail->sendNewPasswordConfirmation($sEmailAddress, $aParam);
      }
    }
    $this->_redirect("admin/user/login");
  }

  public function getuserinfoAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_Digits()));
    $aInputValidators = array(
      "user_id" => array(new Zend_Validate_Digits(), "allowEmpty" => false)
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    if ($oInput->isValid() && $oInput->getEscaped("user_id")) {
      $oModelVUser = new Admin_Model_VUser();
      $nUserId = (int)$oInput->getEscaped("user_id");
      $oRow = $oModelVUser->getRecipientInfo($nUserId);
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($oRow);
  }

}
