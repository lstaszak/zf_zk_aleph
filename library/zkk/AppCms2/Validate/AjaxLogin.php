<?php

class AppCms2_Validate_AjaxLogin extends Zend_Validate_Abstract
{
  const NOT_MATCH = "notMatch";
  protected $_messageTemplates = array(
    self::NOT_MATCH => "Nieprawidłowa nazwa użytkownika lub hasło"
  );

  public function isValid($sValue, $mContext = null)
  {
    $oModelUser = new Admin_Model_User();
    $sValue = (string)$sValue;
    if (is_array($mContext)) {
      if (isset($sValue)) {
        $sEmailAddress = $mContext["user_email_address"];
        $sPassword = $sValue;
        if ($oModelUser->ajaxLogin($sEmailAddress, $sPassword)) {
          $this->_error(self::NOT_MATCH);
          return false;
        }
      }
    }
    return true;
  }
}
