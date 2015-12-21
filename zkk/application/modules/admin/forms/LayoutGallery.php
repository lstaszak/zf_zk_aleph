<?php

class Admin_Form_LayoutGallery extends AppCms2_Controller_Plugin_FormLayoutAbstract
{

  private $_aAllGallery = array();

  public function __construct()
  {
    parent::__construct();
    $this->_aAllGallery = parent::getAllGallery();

    $this->setName(get_class());
    $this->getElement("form_name")->setValue(get_class());

    $oImageGalleryId = new Zend_Form_Element_Select("image_gallery_id");
    $oImageGalleryId->setLabel("Galeria:");
    $oImageGalleryId->setRequired(TRUE);
    $oImageGalleryId->addMultiOptions($this->_aAllGallery);
    $oImageGalleryId->setAttrib("class", "valid");
    $this->addElement($oImageGalleryId);

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);
  }

  public function getSiteFields()
  {
    return array_merge(parent::getStandardSiteFields(), array(
        "image_gallery_id")
    );
  }

}

?>
