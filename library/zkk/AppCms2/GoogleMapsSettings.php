<?php

class AppCms2_GoogleMapsSettings
{
  private $_oAuth, $_nUserId = null;

  public function __construct()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity())
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
  }

  public function createNewMarker($nLng, $nLat, $sDesc)
  {
    $oModelGoogleMaps = new Admin_Model_GoogleMapsMarker();
    $aData = array("lng" => $nLng, "lat" => $nLat, "desc" => $sDesc);
    $nId = $oModelGoogleMaps->addRow($aData);
    if ($nId) {
      return $nId;
    }
    return null;
  }

  public function editMarker($nId, $nLng, $nLat)
  {
    $oModelGoogleMaps = new Admin_Model_GoogleMapsMarker();
    $aData = array("lng" => $nLng, "lat" => $nLat);
    $nId = $oModelGoogleMaps->editRow($nId, $aData);
    if ($nId) {
      return $nId;
    }
    return null;
  }

  public function editDescMarker($nId, $sDesc)
  {
    $oModelGoogleMaps = new Admin_Model_GoogleMapsMarker();
    $aData = array("desc" => $sDesc);
    $nId = $oModelGoogleMaps->editRow($nId, $aData);
    if ($nId) {
      return $nId;
    }
    return null;
  }

  public function getDescMarker($nId)
  {
    $oModelGoogleMaps = new Admin_Model_GoogleMapsMarker();
    $sDesc = $oModelGoogleMaps->getRow($nId)->desc;
    if (isset($sDesc)) {
      return stripslashes($sDesc);
    }
    return null;
  }

  public function deleteMarker($nId)
  {
    $oModelGoogleMaps = new Admin_Model_GoogleMapsMarker();
    return $oModelGoogleMaps->deleteRow($nId);
  }
}

?>
