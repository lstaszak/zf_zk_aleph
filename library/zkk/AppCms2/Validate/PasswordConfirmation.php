<?php

class AppCms2_Validate_PasswordConfirmation extends Zend_Validate_Abstract
{

  const NOT_MATCH = "notMatch";

  protected $_messageTemplates = array(
    self::NOT_MATCH => "Hasła są różne"
  );

  public function isValid($sValue, $mContext = null)
  {
    $sValue = (string)$sValue;
    if (is_array($mContext)) {
      if (isset($sValue)) {
        if ($sValue == $mContext["new_password"]) {
          return true;
        }
      } elseif (is_string($mContext) && ($sValue == $mContext)) {
        return true;
      }
      $this->_error(self::NOT_MATCH);
      return false;
    }
  }

}
