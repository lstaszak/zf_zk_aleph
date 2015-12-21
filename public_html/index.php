<?php
//function full_url($s) {
//  $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
//  $sp = strtolower($s['SERVER_PROTOCOL']);
//  $port = $s['SERVER_PORT'];
//  $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
//  $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : $s['SERVER_NAME'];
//  return str_replace('horizon2.linuxpl.info/zamowkopie', 'zamowkopie.pl', $host . $port . $s['REQUEST_URI']);
//}
//
//$absolute_url = full_url($_SERVER);
//header('Location: ' . 'http://' . $absolute_url);
//die();
ini_set("soap.wsdl_cache_enabled", "0");
error_reporting(E_ALL ^ E_NOTICE);
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../zkk/application'));
// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
  realpath(dirname(__FILE__) . '/../library'),
  realpath(dirname(__FILE__) . '/../library/zkk'),
  realpath(dirname(__FILE__) . '/../zkk/application'),
  get_include_path()
)));
/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(
  APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
  ->run();
