<?php

class AppCms2_ImageAddTo
{
  private $_oAuth, $_nUserId = null;

  public function __construct()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity())
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
  }

  public function setSettings($nImageId, $nImageType, $nElementId)
  {
    $oModelImage = new Admin_Model_Image();
    if (is_numeric($nImageId)) {
      if ($nElementId == 0)
        $nElementId = null;
      if ($nImageType == 1) {
        $sColumnName = "image_gallery_id";
        return $oModelImage->saveAddTo($nImageId, $sColumnName, $nElementId);
      } else if ($nImageType == 2) {
        $sColumnName = "image_slider_id";
        return $oModelImage->saveAddTo($nImageId, $sColumnName, $nElementId);
      }
      return true;
    }
    return null;
  }
}

?>
