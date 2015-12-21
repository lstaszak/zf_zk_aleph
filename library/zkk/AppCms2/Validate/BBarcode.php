<?php

class AppCms2_Validate_BBarcode extends Zend_Validate_Abstract
{
  const NOT_MATCH = "notMatch";
  protected $_messageTemplates = array(
    self::NOT_MATCH => "Nie znaleziono czytelnika o tym numerze karty bibliotecznej"
  );

  public function isValid($sValue)
  {
    $bIsExist = null;
    $nBBarcodeId = $sValue;
    $oModelSybase = new Admin_Model_Sybase();
    try {
      $sToday = $oModelSybase->getToday();
      if (isset($sToday)) {
        $bIsExist = $oModelSybase->findBorrowerBBarcode($nBBarcodeId);
      } else {
        throw new Zend_Exception();
      }
    } catch (Zend_Exception $e) {
      return true;
    }
    if ($bIsExist) {
      return true;
    } else {
      $this->_error(self::NOT_MATCH);
      return false;
    }
  }
}
