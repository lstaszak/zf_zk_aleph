<?php

class AppCms2_Imagick extends Imagick
{
  private $nWidth = 0;
  private $nHeight = 0;
  private $sMimeType = '';
  private $sImageType = '';
  private $aImageTypes = array('jpg', 'jpeg', 'gif', 'png');
  private $aMimeTypes = array(
    'image/gif' => 'gif',
    'image/jpeg' => 'jpg',
    'image/pjpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/x-png' => 'png',
    'image/png' => 'png',
    'application/zip' => 'zip'
  );

  public function __construct($option)
  {
    parent::__construct($option);
  }
}

?>
