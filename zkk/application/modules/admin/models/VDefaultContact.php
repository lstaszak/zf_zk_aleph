<?php

class Admin_Model_VDefaultContact extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "v_default_contact";
  protected $_primary = "email_address";

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
