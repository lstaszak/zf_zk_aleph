<?php

class Admin_SettingsController extends Zend_Controller_Action
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
    $this->_helper->layout()->setLayout("admin/layout");
  }

  private function getFileUploadScript()
  {
    $this->_sPath = APPLICATION_PATH . "/../public_html{$this->view->sSubDomain}/user_image/upload/";
  }

  public function indexAction()
  {
    $this->_redirect("/admin/settings/changepassword");
  }

  public function editfiletranslateAction()
  {
    $oFormFileTranslate = new Admin_Form_FileTranslate();
    $sPath = APPLICATION_PATH . "/resources/languages/";
    $sFileName = "en/en_EN.php";
    $aPostData = array();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $sFileName = $aPostData["file_name"];
    }
    $sFileContent = file_get_contents($sPath . $sFileName);
    $this->view->oFormFileTranslate = $oFormFileTranslate;
    $this->view->sFileName = $sFileName;
    $this->view->sFileContent = stripcslashes($sFileContent);
  }

  public function editfilecssAction()
  {
    $sPath = APPLICATION_PATH . "/../public_html{$this->view->sSubDomain}/skins/";
    $oFormFileCss = new Admin_Form_FileCss($sPath);
    $sFileName = "default/css/globalCss/main.css";
    $aPostData = array();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $sFileName = $aPostData["file_name"];
    }
    $sFileContent = file_get_contents($sPath . $sFileName);
    $this->view->oFormFileCss = $oFormFileCss;
    $this->view->sFileName = $sFileName;
    $this->view->sFileContent = stripcslashes($sFileContent);
  }

  public function editfilephtmlAction()
  {
    $sPath = APPLICATION_PATH . "/layouts/scripts/";
    $oFormFilePhtml = new Admin_Form_FilePhtml($sPath);
    $sFileName = "layout_home.phtml";
    $aPostData = array();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $sFileName = $aPostData["file_name"];
    }
    $sFileContent = file_get_contents($sPath . $sFileName);
    $this->view->oFormFilePhtml = $oFormFilePhtml;
    $this->view->sFileName = $sFileName;
    $this->view->sFileContent = stripcslashes($sFileContent);
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
      if ($oModelUser->updatePassword($aPostData["old_password"], $aPostData["new_password"]))
        $sSuccess = "OK";
    }
    $this->view->oFormChangePassword = $oFormChangePassword;
    $this->view->sSuccess = $sSuccess;
  }

  public function registerAction()
  {
    $this->getFileUploadScript();
    $oModelUser = new Admin_Model_User();
    $oModelVUser = new Admin_Model_VUser();
    $oFormRegister = new Admin_Form_Register();
    $aPostData = array();
    $aParam = array();
    $sSuccess = "";
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($aPostData["user_edit_id"]) {
        $oFormRegister->removeElement("email_address");
        $oFormRegister->removeElement("email_address_confirm");
        $oFormRegister->removeElement("password");
      }
      if ($oFormRegister->isValid($aPostData)) {
        $bIsEdit = $oFormRegister->getValue("user_edit_id");
        if ($bIsEdit) {
          $nUserId = (int)$oFormRegister->getValue("user_edit_id");
          $aParam["role_id"] = $oFormRegister->getValue("role_id");
          $aParam["first_name"] = $oFormRegister->getValue("first_name");
          $aParam["last_name"] = $oFormRegister->getValue("last_name");
          $aParam["phone_number"] = $oFormRegister->getValue("phone_number");
          $aParam["user_category_id"] = (int)$oFormRegister->getValue("user_category_id") != 0 ? $oFormRegister->getValue("user_category_id") : null;
          $aParam["is_active"] = (int)$oFormRegister->getValue("is_active");
          if ($aParam["is_active"]) {
            $sEmailAddress = $oModelUser->findEmailAddress($nUserId);
            if ($oModelUser->findUserByEmailAddress($sEmailAddress, 0) == $nUserId):
              $oMail = new AppCms2_Controller_Plugin_Mail();
              $oMail->sendUserAccountActivation($sEmailAddress, $aParam); //mail do użytkownika z informacją o aktywacji konta w systemie
            endif;
          } else {
            $sEmailAddress = $oModelUser->findEmailAddress($nUserId);
            if ($oModelUser->findUserByEmailAddress($sEmailAddress, 0) == $nUserId):
              $oMail = new AppCms2_Controller_Plugin_Mail();
              $oMail->sendUserAccountDeactivation($sEmailAddress, array("user_id" => $nUserId)); //mail do użytkownika z informacją o deaktywacji konta w systemie
            endif;
          }
          $oModelUser->editUser($nUserId, $aParam);
          $this->_redirect("admin/settings/register");
        } else {
          $sEmailAddress = $oFormRegister->getValue("email_address");
          $nUserId = $oModelUser->findUserByEmailAddress($sEmailAddress, 2);
          if (!$nUserId) {
            $aParam["role_id"] = $oFormRegister->getValue("role_id");
            $aParam["first_name"] = $oFormRegister->getValue("first_name");
            $aParam["last_name"] = $oFormRegister->getValue("last_name");
            $aParam["email_address"] = $oFormRegister->getValue("email_address");
            $aParam["email_address_confirm"] = $oFormRegister->getValue("email_address_confirm");
            $aParam["password"] = $oFormRegister->getValue("password");
            $aParam["phone_number"] = $oFormRegister->getValue("phone_number");
            $aParam["user_category_id"] = (int)$oFormRegister->getValue("user_category_id") != 0 ? $oFormRegister->getValue("user_category_id") : null;
            $aParam["is_active"] = (int)$oFormRegister->getValue("is_active");
            $sConfirmCode = $oModelUser->newUser($aParam);
            $aParam["confirm_code"] = $sConfirmCode;
            if ($sConfirmCode) {
              if ($aParam["is_active"]) {
                $oMail = new AppCms2_Controller_Plugin_Mail();
                $oMail->sendUserAccountRegistrationAndActivation($sEmailAddress, $aParam); //mail do użytkownika z informacją o rejestracji i aktywacji konta w systemie (w tym mailu wysłane jest hasło)
                $oMail = new AppCms2_Controller_Plugin_Mail();
                $oMail->sendAdminConfirmRegistrationAndActivation($aParam); //mail do administratora z informacją o rejestracji i aktywacji konta w systemie (w tym mailu wysłane jest hasło)
              } else {
                $oMail = new AppCms2_Controller_Plugin_Mail();
                $oMail->sendUserAccountRegistration($sEmailAddress, $aParam); //mail do użytkownika z informacją o rejestracji konta w systemie (w tym mailu wysłane jest hasło) konto jest nieaktywne
                $oMail = new AppCms2_Controller_Plugin_Mail();
                $oMail->sendAdminConfirmRegistration($aParam); //mail do administratora z informacją o rejestracji konta w systemie (w tym mailu wysłane jest hasło) konto jest nieaktywne
              }
              $sSuccess = "OK";
            } else {
              $sSuccess = "NO OK";
            }
          } else {
            $oBootstrap = Zend_Controller_Front::getInstance()->getParam("bootstrap");
            $sOptions = $oBootstrap->getOptions();
            $nTime = time();
            $sSalt = md5(sha1($nTime . $sOptions["resources"]["frontController"]["salt"] . $nTime));
            $sPassword = md5(md5($aParam["password"]) . $sSalt);
            if ($oModelUser->editRow($nUserId, array("password" => $sPassword, "salt" => $sSalt, "created_date" => $nTime, "is_active" => 1)))
              $sSuccess = "OK";
          }
        }
        $oFormRegister->clearForm();
      }
    }
    $this->view->oFormRegister = $oFormRegister;
    $this->view->aAllUser = $oModelVUser->getAll()->toArray();
    $this->view->sSuccess = $sSuccess;
  }

  public function roleAction()
  {
    $oModelUserRole = new Admin_Model_UserRole();
    $oFormUserRole = new Admin_Form_UserRole();
    $aPostData = array();
    $sSuccess = "";
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormUserRole->isValid($aPostData)) {
        $nRoleId = $aPostData["role_edit_id"];
        $sRoleName = $aPostData["role_name"];
        if ($nRoleId != 0) {
          $bIsEdited = $oModelUserRole->edit($nRoleId, $sRoleName);
          if (!is_numeric($bIsEdited))
            $sSuccess = "NO OK";
        } else {
          $nRoleId = $oModelUserRole->newRole($sRoleName);
          if (!is_numeric($nRoleId))
            $sSuccess = "NO OK";
        }
        $oFormUserRole->reset();
      }
    }
    $this->view->oFormUserRole = $oFormUserRole;
    $this->view->aAllUserRole = $oModelUserRole->getAll()->toArray();
    $this->view->sSuccess = $sSuccess;
  }

  public function seoAction()
  {
    $oModelSiteSeo = new Admin_Model_SiteSeo();
    $oFormSiteSeo = new Admin_Form_SiteSeo();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormSiteSeo->isValid($aPostData)) {
        $aData = array();
        $aData["site_seo_robots_id"] = $oFormSiteSeo->getValue("site_seo_robots_id");
        $aData["head_title"] = $oFormSiteSeo->getValue("head_title");
        if (is_array($oFormSiteSeo->getValue("keywords")))
          $sKeywords = implode(",", $oFormSiteSeo->getValue("keywords"));
        $aData["keywords"] = $sKeywords;
        $aData["description"] = $oFormSiteSeo->getValue("description");
        $oModelSiteSeo->editRow(1, $aData);
      }
    }
    $aSeo = $oModelSiteSeo->getRow(1)->toArray();
    if (strlen($aSeo["keywords"])) {
      $aSeo["keywords"] = explode(",", $aSeo["keywords"]);
    }
    $oFormSiteSeo->populate($aSeo);
    $this->view->oFormSiteSeo = $oFormSiteSeo;
  }

  public function statisticsAction()
  {
    $oModelGoogleAnalytics = new Admin_Model_GoogleAnalytics();
    $oFormGoogleAnalytics = new Admin_Form_GoogleAnalytics();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormGoogleAnalytics->isValid($aPostData)) {
        $aData = array();
        $aData["profile"] = $oFormGoogleAnalytics->getValue("profile");
        $aData["code"] = $oFormGoogleAnalytics->getValue("code");
        $aData["start_date"] = $oFormGoogleAnalytics->getValue("start_date");
        $oModelGoogleAnalytics->editRow(1, $aData);
      }
    }
    $oFormGoogleAnalytics->populate($oModelGoogleAnalytics->getRow(1)->toArray());
    $this->view->oFormGoogleAnalytics = $oFormGoogleAnalytics;
  }

  public function socialmediaAction()
  {
    $oModelFacebookBox = new Admin_Model_FacebookBox();
    $oFormFacebookBox = new Admin_Form_FacebookBox();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormFacebookBox->isValid($aPostData)) {
        $oModelFacebookBox->editRow(1, $aPostData);
      }
    }
    $oFormFacebookBox->populate($oModelFacebookBox->getRow(1)->toArray());
    $this->view->oFormFacebookBox = $oFormFacebookBox;
  }

  public function defaultcontactAction()
  {
    $oModelDefaultContact = new Admin_Model_DefaultContact();
    $oFormDefaultContact = new Admin_Form_DefaultContact();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormDefaultContact->isValid($aPostData)) {
        $oModelDefaultContact->truncate();
        if (count($aPostData["email_address"]))
          foreach ($aPostData["email_address"] as $sValue) {
            $nValue = (int)$sValue;
            if (is_numeric($nValue))
              $oModelDefaultContact->addRow(array("user_id" => $nValue));
          }
      }
    }
    $aDefaultContact = array();
    foreach ($oModelDefaultContact->getAll() as $oValue) {
      array_push($aDefaultContact, $oValue->user_id);
    }
    if (count($aDefaultContact))
      $oFormDefaultContact->populate(array("email_address" => $aDefaultContact));
    $this->view->oFormDefaultContact = $oFormDefaultContact;
  }

  public function deleteuserroleAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits())
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nUserRoleId = $oInput->getUnescaped("id");
    $oModelUserRole = new Admin_Model_UserRole();
    $bJson = $oModelUserRole->deleteUserRole($nUserRoleId);
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function enableuserAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits())
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nUserId = $oInput->getUnescaped("id");
    $oModelUser = new Admin_Model_User();
    $oModelVUser = new Admin_Model_VUser();
    $bJson = $oModelUser->enableUser($nUserId);
    if ($bJson) {
      $sEmailAddress = $oModelUser->findEmailAddress($nUserId);
      $aParam = $oModelVUser->getUserParam($nUserId);
      $oMail = new AppCms2_Controller_Plugin_Mail();
      $oMail->sendUserAccountActivation($sEmailAddress, $aParam);
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function disableuserAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits())
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nUserId = $oInput->getUnescaped("id");
    $oModelUser = new Admin_Model_User();
    $oModelVUser = new Admin_Model_VUser();
    $bJson = $oModelUser->disableUser($nUserId);
    if ($bJson) {
      $sEmailAddress = $oModelUser->findEmailAddress($nUserId);
      $aParam = $oModelVUser->getUserParam($nUserId);
      $oMail = new AppCms2_Controller_Plugin_Mail();
      $oMail->sendUserAccountDeactivation($sEmailAddress, $aParam);
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function savefiletranslateAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $sPath = APPLICATION_PATH . "/resources/languages/";
    $aData = $this->_getAllParams();
    $sFileName = $aData["file_name"];
    $sFileContent = stripslashes($aData["file_translate"]);
    $bJson = file_put_contents($sPath . $sFileName, $sFileContent);
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function savefilecssAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $sPath = APPLICATION_PATH . "/../public_html{$this->view->sSubDomain}/skins/";
    $aData = $this->_getAllParams();
    $sFileName = $aData["file_name"];
    $sFileContent = stripslashes($aData["file_css"]);
    $bJson = file_put_contents($sPath . $sFileName, $sFileContent);
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
  }

  public function savefilephtmlAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $sPath = APPLICATION_PATH . "/layouts/scripts/";
    $aData = $this->_getAllParams();
    $sFileName = $aData["file_name"];
    $sFileContent = stripslashes(html_entity_decode($aData["file_phtml"]));
    $bJson = file_put_contents($sPath . $sFileName, $sFileContent);
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
  }

  public function addpictureAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $this->getFileUploadScript();
    $oUploadHandler = new AppCms2_UploadHandler();
    $oUserParam = new Admin_Model_UserParam();
    if ($this->_request->isPost()) {
      $aResult = $oUploadHandler->post();
      if (!isset($aResult["files"][0]->error)) {
        $aPostData = $this->_request->getPost();
        $nUserId = (int)$aPostData["add_photo_user_id"];
        $nMediaWidth = $aPostData["media_width"];
        $nMediaHeight = $aPostData["media_height"];
        $sUserName = $aResult["files"][0]->name;
        $sGenName = $aResult["files"][0]->gen_name;
        if (!is_dir($this->_sPath . "user_id_" . $nUserId))
          if (!mkdir($this->_sPath . "user_id_" . $nUserId, 0777))
            throw new Zend_Exception();
        $oImagic = new AppCms2_Imagick($this->_sPath . $sUserName);
        if (isset($nMediaWidth) && isset($nMediaHeight)) {
          $oImagic->cropimage((int)$nMediaWidth, (int)$nMediaHeight, 0, 0);
        }
        $oImagic->writeImage($this->_sPath . "user_id_" . $nUserId . "/normal_" . $sGenName);
        $oImagic->cropThumbnailImage(70, 100);
        $oImagic->writeImage($this->_sPath . "user_id_" . $nUserId . "/min_" . $sGenName);
        $oImagic->writeImage($this->_sPath . "thumbnail/" . $sGenName);
        unlink($this->_sPath . $sUserName);
        if (file_exists($this->_sPath . $sUserName))
          throw new Zend_Exception();
        $oModelImage = new Admin_Model_Image();
        $nImageId = $oModelImage->saveImage(3, null, $sGenName, $sUserName);
        $oUserParam->editImageUserParam($nUserId, $nImageId);
      } else {
        $sUserName = $aResult["files"][0]->name;
        unlink($this->_sPath . $sUserName);
      }
      $oModelImage->deleteUnused();
    }
    exit;
  }

  public function getuserimageAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oModelVUser = new Admin_Model_VUser();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nUserId = (int)$aPostData["add_photo_user_id"];
      $aUserInfo = $oModelVUser->getUserImage($nUserId)->toArray();
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aUserInfo);
  }

  public function loaduserdataAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "num_row_per_page" => array(new Zend_Validate_Digits()),
      "curr_page" => array(new Zend_Validate_Digits()),
      "sort_column" => array(new AppCms2_Validate_SpecialAlpha()),
      "sort_method" => array(new Zend_Validate_Alpha()),
      "filter_company_name" => array("allowEmpty" => true),
      "filter_user_name" => array("allowEmpty" => true),
      "filter_email_address" => array("allowEmpty" => true),
      "filter_user_category_name" => array("allowEmpty" => true)
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    if ($oInput->isValid()) {
      $oModelVUser = new Admin_Model_VUser();
      $nNumRowPerPage = $oInput->getEscaped("num_row_per_page");
      $nCurrPage = $oInput->getEscaped("curr_page");
      $sSortColumn = $oInput->getEscaped("sort_column");
      $sSortMethod = $oInput->getEscaped("sort_method");
      $aFilter = array(
        "company_name" => $oInput->getEscaped("filter_company_name"),
        "user_name" => $oInput->getEscaped("filter_user_name"),
        "email_address" => $oInput->getEscaped("filter_email_address"),
        "user_category_name" => $oInput->getEscaped("filter_user_category_name"),
      );
      $oRowset = $oModelVUser->getAllUser($aFilter, $nNumRowPerPage, ($nCurrPage - 1) * $nNumRowPerPage, $sSortColumn . " " . $sSortMethod);
      $nNumRows = $oModelVUser->getAllUser($aFilter)->count();
      $aJson = array("rowset" => $oRowset->toArray(), "num_rows" => $nNumRows);
      header("Content-type: application/json");
      echo Zend_Json::encode($aJson);
    }
  }

  public function deleteuserAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $this->getFileUploadScript();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits())
    );
    $bJson = false;
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nId = $oInput->getUnescaped("id");
    $oModelUser = new Admin_Model_User();
    if ($oModelUser->deleteRow($nId))
      $bJson = true;
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function emailnotificationAction()
  {
    $oModelEmailNotification = new Admin_Model_EmailNotification();
    $oFormEmailNotification = new Admin_Form_EmailNotification();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormEmailNotification->isValid($aPostData)) {
        if (is_string($sValue)) {
          $aPostData[$sKey] = stripcslashes($sValue);
        }
      }
      $oModelEmailNotification->editRow(1, $aPostData);
      $oFormEmailNotification->clearForm();
    }
    $aEmailNotifications = $oModelEmailNotification->getRow(1)->toArray();
    $this->view->oFormEmailNotification = $oFormEmailNotification->populate($aEmailNotifications);
  }

  public function keywordsAction()
  {
    $oFormAddKeywords = new Admin_Form_AddKeywords();
    $oModelSiteSeoKeywords = new Admin_Model_SiteSeoKeywords();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormAddKeywords->isValid($aPostData)) {
        $aData = array();
        $bIsEdit = $oFormAddKeywords->getValue("keyword_edit_id");
        if ($bIsEdit) {
          $nKeywordId = $bIsEdit;
          $aData["value"] = $oFormAddKeywords->getValue("value");
          $oModelSiteSeoKeywords->editRow($nKeywordId, $aData);
        } else {
          $aData["value"] = $oFormAddKeywords->getValue("value");
          $oModelSiteSeoKeywords->addRow($aData);
        }
      }
    }
    $oFormAddKeywords->clearForm();
    $this->view->oFormKeywords = $oFormAddKeywords;
    $this->view->aAllKeywords = $oModelSiteSeoKeywords->getAll()->toArray();
  }

  public function truncatedbAction()
  {
    $oFormTrancateDb = new Admin_Form_TrancateDb();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormTrancateDb->isValid($aPostData)) {
        $oDb = Zend_Db_Table::getDefaultAdapter();
        $oDb->beginTransaction();
        try {
          if (count($aPostData["table_name"])) {
            foreach ($aPostData["table_name"] as $sTableName):
              $sQuery = "TRUNCATE $sTableName";
              $oDb->fetchRow($sQuery);
            endforeach;
            $oDb->commit();
            $sSuccess = "OK";
          }
        } catch (Zend_Exception $e) {
          $oDb->rollBack();
          $sSuccess = "NO OK";
        }
      }
    }
    $this->view->oFormTrancateDb = $oFormTrancateDb;
    $this->view->sSuccess = $sSuccess;
  }

  public function orderemailnotificationAction()
  {
    $oModelOrderEmailNotification = new User_Model_OrderEmailNotification();
    $oModelVOrderEmailNotification = new User_Model_VOrderEmailNotification();
    $oFormOrderEmailNotification = new User_Form_OrderEmailNotification();
    $oModelVOrderJournal = new User_Model_VOrderJournal();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormOrderEmailNotification->isValid($aPostData)) {
        $nOrderEmailNotificationEditId = (int)$aPostData["order_email_notification_edit_id"];
        foreach ($aPostData as $sKey => $sValue) {
          if (is_string($sValue)) {
            $aPostData[$sKey] = stripcslashes($sValue);
          }
        }
        if ($nOrderEmailNotificationEditId)
          $oModelOrderEmailNotification->editRow($nOrderEmailNotificationEditId, $aPostData);
        else {
          $oRow = $oModelVOrderEmailNotification->getOrderEmailNotification($aPostData["order_status_id_old"], $aPostData["order_status_id_new"]);
          if ($oRow)
            $oModelOrderEmailNotification->editRow($oRow->id, $aPostData);
          else
            $oModelOrderEmailNotification->addRow($aPostData);
        }
        $oFormOrderEmailNotification->clearForm();
      }
    }
    $aAllOrderEmailNotification = $oModelVOrderEmailNotification->getAll(array("order_status_id_old"))->toArray();
    $this->view->aColumnName = $oModelVOrderJournal->getColumnName();
    $this->view->oFormOrderEmailNotification = $oFormOrderEmailNotification;
    $this->view->aAllOrderEmailNotification = $aAllOrderEmailNotification;
  }

}
