<?php

class Zend_View_Helper_GoogleMaps extends Zend_View_Helper_Abstract
{

  public function googleMaps()
  {
    $oModelGoogleMaps = new Admin_Model_GoogleMaps();
    $sApiKey = $oModelGoogleMaps->getRow(1)->api_key;
    if (isset($sApiKey) && strlen($sApiKey)) {
      $this->view->api_key = $sApiKey;
      return $this->view->render('_helpers/gm.phtml');
    }
  }

}

?>
