<?php

class Admin_Model_SiteSeoRobots extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "site_seo_robots";
  protected $_dependentTables = array(
    "Admin_Model_SiteSeo"
  );
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
