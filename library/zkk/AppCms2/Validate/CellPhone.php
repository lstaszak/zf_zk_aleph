<?php

class AppCms2_Validate_CellPhone extends Zend_Validate_Abstract
{

  const PHONE_BAD_CHARS = "phoneBadChars";
  const PHONE_BAD_LENGTH = "phoneBadLength";

  private $_aAllowedCharacters = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  private $_aSeparators = array();
  //private $_aSeparators = array('-', '/', '.', '+', ' ');
  protected $_messageTemplates = array(
    self::PHONE_BAD_CHARS => 'Numer telefonu powinien zawierać cyfry 0-9',
    self::PHONE_BAD_LENGTH => "%value% nie jest poprawnym numerem telefonu",
  );

  public function isValid($mValue)
  {
    $sValue = (string)$mValue;
    $this->_setValue($sValue);
    $aValue = str_split($sValue);
    foreach ($aValue as $sChar) {
      if (!in_array($sChar, $this->_aAllowedCharacters) && !in_array($sChar, $this->_aSeparators)) {
        $this->_error(self::PHONE_BAD_CHARS);
        return false;
      }
    }
    $nCount = str_replace($this->_aSeparators, "", $sValue);
    $nLen = strlen($nCount);
    if ($nLen != 9) {
      $this->_error(self::PHONE_BAD_LENGTH);
      return false;
    }
    return true;
  }

}

?>
