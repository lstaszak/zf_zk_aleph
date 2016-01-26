<?php

class AppCms2_AjaxRender
{

  private $_oAuth, $_nUserId, $_sRoleName = null;

  public function __construct()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_sRoleName = $this->_oAuth->getStorage()->read()->role_name;
    }
  }

  public function renderForm($nInstanceId, $aData = null)
  {
    $sForm = null;
    if (is_numeric($nInstanceId)) {
      if ($nInstanceId == 1) {
        $oFormImageAddTo = new Admin_Form_ImageAddTo();
        $sForm = $oFormImageAddTo->render();
      } else if ($nInstanceId == 2) {
        $oFormImageSettings = new Admin_Form_ImageSettings();
        $sForm = $oFormImageSettings->render();
      } else if ($nInstanceId == 4) {
        if ($aData["module"] == "borrower") {
          $oFormOrderSettings = new Borrower_Form_OrderInvoice();
        }
        if (isset($oFormOrderSettings)) {
          $sForm = $oFormOrderSettings->render();
        }
      }
      return $sForm;
    }
    return null;
  }

  public function setSettings($nImageId)
  {
    if (is_numeric($nImageId)) {
      return true;
    }
    return null;
  }

}

?>
