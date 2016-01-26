<?php

class AppCms2_Validate_CheckUser extends Zend_Validate_Abstract
{

  const IS_EXIST = "notMatch";

  protected $_messageTemplates = array(
    self::IS_EXIST => "Użytkownik o takim adresie e-mail już istnieje"
  );

  public function isValid($sValue, $mContext = null)
  {
    $sValue = (string)$sValue;
    if (isset($sValue)) {
      $oModelUser = new Admin_Model_User();
      $nUserId = $oModelUser->findUserByEmailAddress($sValue);
      if (!is_numeric($nUserId)) {
        return true;
      }
    }
    $this->_error(self::IS_EXIST);
    return false;
  }

}
