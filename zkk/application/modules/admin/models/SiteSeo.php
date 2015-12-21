<?php

class Admin_Model_SiteSeo extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "site_seo";
  protected $_dependentTables = array();
  protected $_referenceMap = array(
    "fk_site_seo_robots_site_seo" => array(
      "columns" => array("site_seo_robots_id"),
      "refTableClass" => "Admin_Model_SiteSeoRobots",
      "refColumns" => array("id"),
      "onDelete" => self::SET_NULL,
      "onUpdate" => self::CASCADE
    )
  );

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
