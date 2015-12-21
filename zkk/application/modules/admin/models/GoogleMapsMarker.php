<?php

class Admin_Model_GoogleMapsMarker extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "google_maps_marker";
  protected $_dependentTables = array();
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
