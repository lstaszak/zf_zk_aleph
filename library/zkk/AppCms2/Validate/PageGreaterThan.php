<?php

class AppCms2_Validate_PageGreaterThan extends Zend_Validate_Abstract
{
  const IS_EXIST = "notMatch";
  protected $_messageTemplates = array(
    self::IS_EXIST => "Podana wartość powinna być większa lub równa wartości w polu 'Strona od'"
  );

  public function isValid($sValue, $aContext = null)
  {
    $sValue = (string)$sValue;
    if (is_array($aContext)) {
      if (isset($sValue)) {
        if ($sValue >= $aContext["page_from"]) {
          return true;
        }
      }
      $this->_error(self::IS_EXIST);
      return false;
    }
  }
}
