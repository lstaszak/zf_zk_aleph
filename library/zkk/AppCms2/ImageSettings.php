<?php

class AppCms2_ImageSettings
{
  private $_oAuth, $_nUserId = null;

  public function __construct()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity())
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
  }

  public function getSettings($nImageId)
  {
    if (is_numeric($nImageId)) {
      $oModelImage = new Admin_Model_Image();
      return $oModelImage->getOne($nImageId)->toArray();
    }
    return null;
  }

  public function setSettings($nImageId, $sUserName, $sDescr)
  {
    if (is_numeric($nImageId)) {
      $aImageSettings = array();
      $oStringLenght = new Zend_Validate_StringLength();
      $oStringLenght->setMin(0);
      $oStringLenght->setMax(50);
      if ($oStringLenght->isValid($sUserName)) {
        $aImageSettings["user_name"] = $sUserName;
      }
      $oStringLenght->setMax(100);
      if ($oStringLenght->isValid($sDescr)) {
        $aImageSettings["descr"] = $sDescr;
      }
      $oModelImage = new Admin_Model_Image();
      if ($oModelImage->saveSettings($nImageId, $aImageSettings)):
        return true;
      endif;
    }
    return null;
  }
}

?>
