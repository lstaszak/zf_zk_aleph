<?php

class AppCms2_Validate_Statute extends Zend_Validate_Abstract
{
  const NOT_ACCEPT = "notAccept";
  protected $_messageTemplates = array(
    self::NOT_ACCEPT => "Aby korzystać z usługi musisz zaakcpetować regulamin"
  );

  public function isValid($nValue)
  {
    $nValue = (int)$nValue;
    if (isset($nValue)) {
      if (is_numeric($nValue) && $nValue == 1) {
        return true;
      }
    }
    $this->_error(self::NOT_ACCEPT);
    return false;
  }
}
