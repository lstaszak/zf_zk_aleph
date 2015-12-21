<?php

class AppCms2_Validate_UserRole extends Zend_Validate_Abstract
{
  const IS_EXIST = "notMatch";
  protected $_messageTemplates = array(
    self::IS_EXIST => "Kategoria użytkownika o takiej nazwie już istnieje"
  );

  public function isValid($sValue, $mContext = null)
  {
    $sValue = (string)$sValue;
    if (is_array($mContext)) {
      if (isset($sValue)) {
        $oModelUserRole = new Admin_Model_UserRole();
        $nRoleId = $oModelUserRole->check($sValue);
        if (!is_numeric($nRoleId))
          return true;
      }
    }
    $this->_error(self::IS_EXIST);
    return false;
  }
}
