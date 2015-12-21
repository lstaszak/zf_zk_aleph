<?php

class Zend_View_Helper_GoogleAnalytics extends Zend_View_Helper_Abstract
{
  public function googleAnalytics()
  {
    $oModelGoogleAnalytics = new Admin_Model_GoogleAnalytics();
    $sCode = $oModelGoogleAnalytics->getRow(1)->code;
    $this->view->code = $sCode;
    return $this->view->render('_helpers/ga.phtml');
  }
}

?>
