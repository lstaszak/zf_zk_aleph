<?php

class Admin_Model_GoogleAnalytics extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "google_analytics";
  protected $_dependentTables = array();
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
