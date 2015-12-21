<?php

class Admin_Model_ImageGallery extends AppCms2_Controller_Plugin_TableAbstract
{

  protected $_name = "image_gallery";
  protected $_dependentTables = array(
    "Admin_Model_Image",
    "Admin_Model_Site"
  );
  protected $_referenceMap = array();

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

}

?>
