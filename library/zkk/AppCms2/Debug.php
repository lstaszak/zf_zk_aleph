<?php

class AppCms2_Debug
{
  protected static $_instance = null;
  protected static $_db = null;

  public static function getInstance()
  {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public static function dump($mParameter = null)
  {
    self::getInstance();
    $oWriter = new Zend_Log_Writer_Firebug();
    $oLogger = new Zend_Log($oWriter);
    if (isset($mParameter)) {
      $oLogger->log($mParameter, Zend_Log::INFO);
    }
  }
}

?>
