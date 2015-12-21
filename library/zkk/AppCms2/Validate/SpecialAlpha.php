<?php

class AppCms2_Validate_SpecialAlpha extends Zend_Validate_Abstract
{
  public function isValid($sValue)
  {
    $sValue = preg_replace("/_/", " ", $sValue);
    $oAlphaValidator = new Zend_Validate_Alpha(array("allowWhiteSpace" => true));
    if ($oAlphaValidator->isValid($sValue))
      return true;
    return false;
  }
}

?>
