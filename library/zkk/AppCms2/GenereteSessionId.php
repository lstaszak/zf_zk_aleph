<?php

class AppCms2_GenereteSessionId
{
  private $_nPasswordLength = 300;
  private $_bUseSpecialChars = true;
  private $_bUseLowercaseLetters = true;
  private $_bUseUpperLetters = true;
  private $_bUseDigits = true;
  private $_sPassword = null;
  private $_aSource = array();
  private $_nSourceCounter = null;

  public function __get($name)
  {
    return $this->$name;
  }

  private function initialize()
  {
    if ($this->_bUseSpecialChars) {
      $this->_aSource[] = "!@#$%^*_-+=?:()|~";
    }
    if ($this->_bUseDigits) {
      $this->_aSource[] = "0123456789";
    }
    if ($this->_bUseLowercaseLetters) {
      $this->_aSource[] = "abcdefghijklmnopqrstuvwxyząćęłńóśźż";
    }
    if ($this->_bUseUpperLetters) {
      $this->_aSource[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZĄĆĘŁŃÓŚŹŻ";
    }
    if (!isset($this->_aSource)) {
      die("You must choose at least one password source (special chars, digits, lowercase letters, uppercase letters)");
    }
    $this->_nSourceCounter = sizeof($this->_aSource);
  }

  public function generate()
  {
    $this->_sPassword = null;
    $this->initialize();
    for ($i = 0; $i < $this->_nPasswordLength; $i++) {
      $temp = mt_rand(0, $this->_nSourceCounter - 1);
      $temp2 = mt_rand(0, strlen($this->_aSource[$temp]) - 1);
      $this->_sPassword .= $this->_aSource[$temp][$temp2] . uniqid() . md5(uniqid()) . md5(time());
    }
    return md5($this->_sPassword);
  }

  public function generatePassword()
  {
    $this->_sPassword = null;
    $this->initialize();
    for ($i = 0; $i < $this->_nPasswordLength; $i++) {
      $temp = mt_rand(0, $this->_nSourceCounter - 1);
      $temp2 = mt_rand(0, strlen($this->_aSource[$temp]) - 1);
      $this->_sPassword .= $this->_aSource[$temp][$temp2] . uniqid() . md5(uniqid()) . md5(time());
    }
    $sNewPassword = substr(md5($this->_sPassword), 22);
    return array(
      "user_password" => $sNewPassword,
      "hash" => $sNewPassword,
      "activating_code" => $this->generate(),
    );
  }

  public function generateImageName()
  {
    $this->_sPassword = null;
    $this->initialize();
    for ($i = 0; $i < $this->_nPasswordLength; $i++) {
      $temp = mt_rand(0, $this->_nSourceCounter - 1);
      $temp2 = mt_rand(0, strlen($this->_aSource[$temp]) - 1);
      $this->_sPassword .= $this->_aSource[$temp][$temp2] . uniqid() . md5(uniqid()) . md5(time());
    }
    $sNewPassword = substr(md5($this->_sPassword), 22);
    return md5($sNewPassword);
  }

  public function generatePaymentParam()
  {
    $this->initialize();
    $this->_nPasswordLength = 300;
    for ($i = 0; $i < $this->_nPasswordLength; $i++) {
      $temp = mt_rand(0, $this->_nSourceCounter - 1);
      $temp2 = mt_rand(0, strlen($this->_aSource[$temp]) - 1);
      $sOrderId .= $this->_aSource[$temp][$temp2] . uniqid() . md5(uniqid()) . md5(time());
    }
    for ($i = 0; $i < $this->_nPasswordLength; $i++) {
      $temp = mt_rand(0, $this->_nSourceCounter - 1);
      $temp2 = mt_rand(0, strlen($this->_aSource[$temp]) - 1);
      $sSessionId .= $this->_aSource[$temp][$temp2] . uniqid() . md5(uniqid()) . md5(time());
    }
    $sOrderId = sha1(md5($sOrderId . $sSessionId));
    $sSessionId = sha1(md5($sSessionId . $sOrderId));
    return array(
      "order_id" => md5($sOrderId),
      "session_id" => md5($sSessionId),
    );
  }
}

?>
