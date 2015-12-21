<?php

class IndexController extends Zend_Controller_Action
{

  public function indexAction()
  {
    $this->_redirect("/admin");
  }

  public function objectToArray($d)
  {
    if (is_object($d)) {
      $d = get_object_vars($d);
    }
    if (is_array($d)) {
      return array_map(__FUNCTION__, $d);
    } else {
      return $d;
    }
  }

}
