<?php

class AppCms2_Validate_IsOnline extends Zend_Validate_Abstract
{
  const IS_EXIST = "notMatch";
  protected $_messageTemplates = array(
    self::IS_EXIST => "Użytkownik jest w trybie off-line"
  );

  public function isValid($nValue)
  {
    $nValue = (int)$nValue;
    if (is_numeric($nValue) && $nValue > 0) {
      $oModelUser = new Admin_Model_User();
      $nIsOnline = $oModelUser->getAskOnline($nValue);
      if ($nIsOnline == 1) {
        return true;
      }
    }
    $this->_error(self::IS_EXIST);
    return false;
  }
}
