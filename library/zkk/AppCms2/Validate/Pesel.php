<?php

class AppCms2_Validate_Pesel extends Zend_Validate_Abstract
{
  const LENGTH = 'numLength';
  const CHECKSUM = 'numChecksum';
  const SYBASE = 'sybase';
  protected static $_filter = null;
  protected $_messageTemplates = array(
    self::LENGTH => "'%value%' must contain 11 digits",
    self::CHECKSUM => "Luhn algorithm (mod-11 checksum) failed on '%value%'",
    self::SYBASE => "Błąd autoryzacji w systemie bibliotecznym Horizon"
  );

  public function isValid($sValue, $aContext = null)
  {
    if (is_array($aContext) && isset($aContext["bbarcode_id"])) {
      $oModelSybase = new Admin_Model_Sybase();
      $sBorrowerId = $oModelSybase->checkBorrowerLogin($aContext["bbarcode_id"], $sValue);
      if ($sBorrowerId === false) {
        $this->_error(self::SYBASE);
        return false;
      }
    }
    if (null === self::$_filter) {
      self::$_filter = new Zend_Filter_Digits();
    }
    $sValueFiltered = self::$_filter->filter($sValue);
    $nLength = strlen($sValueFiltered);
    if ($nLength != 11) {
      $this->_error(self::LENGTH);
      return false;
    }
    $nErasmus = $sValue[6] + $sValue[7] + $sValue[8] + $sValue[9];
    if ($nErasmus == 0) {
      return true;
    }
    $nMod = 10;
    $nSum = 0;
    $aWeights = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
    preg_match_all("/\d/", $sValueFiltered, $aDigits);
    $aValueFiltered = $aDigits[0];
    foreach ($aValueFiltered as $nDigit) {
      $nWeight = current($aWeights);
      $nSum += $nDigit * $nWeight;
      next($aWeights);
    }
    if ((((10 - ($nSum % $nMod) == 10) ? 0 : 10) - ($nSum % $nMod)) != $aValueFiltered[$nLength - 1]) {
      $this->_error(self::CHECKSUM, $sValueFiltered);
      return false;
    }
    return true;
  }
}

?>
