<?php

class ErrorController extends Zend_Controller_Action
{

  public function indexAction()
  {
    $errors = $this->_getParam('error_handler');
    if (!$errors) {
      $this->view->message = 'You have reached the error page';
      return;
    }
    switch ($errors->type) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = 'Page not found';
        break;
      default:
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->message = 'Application error';
        break;
    }
    if ($log = $this->getLog()) {
      $log->crit($this->view->message, $errors->exception);
    }
    if ($this->getInvokeArg('displayExceptions') == true) {
      $this->view->exception = $errors->exception;
    }
    $this->view->request = $errors->request;
  }

  public function errorAction()
  {
    $this->_helper->layout()->setLayout("admin/layout_error");
    $errors = $this->_getParam('error_handler');
    Zend_Debug::dump($errors->exception->__toString());
    if (!$errors) {
      $this->view->message = 'You have reached the error page';
      return;
    }
    switch ($errors->type) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = 'Page not found';
        break;
      default:
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->message = 'Application error';
        break;
    }
    if ($log = $this->getLog()) {
      $log->crit($this->view->message, $errors->exception);
    }
    if ($this->getInvokeArg('displayExceptions') == true) {
      $this->view->exception = $errors->exception;
    }
    $this->view->request = $errors->request;
  }

  public function getLog()
  {
    $bootstrap = $this->getInvokeArg('bootstrap');
    if (!$bootstrap->hasResource('Log')) {
      return false;
    }
    $log = $bootstrap->getResource('Log');
    return $log;
  }

}
