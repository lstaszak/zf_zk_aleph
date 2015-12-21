<?php

class Admin_Form_ImageAddTo extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllGallery = array();
  private $_aAllSlider = array();

  public function __construct($options = null)
  {
    $oModelImageGallery = new Admin_Model_ImageGallery();
    $oModelImageSlider = new Admin_Model_ImageSlider();
    $aAllGallery = $oModelImageGallery->getAll();
    $this->_aAllGallery["gallery_0"] = "-";
    foreach ($aAllGallery as $nKey => $aValue) {
      $this->_aAllGallery["gallery_" . $aValue["id"]] = $aValue["name"];
    }
    $aAllSlider = $oModelImageSlider->getAll();
    $this->_aAllSlider["slider_0"] = "-";
    foreach ($aAllSlider as $nKey => $aValue) {
      $this->_aAllSlider["slider_" . $aValue["id"]] = $aValue["name"];
    }
    parent::__construct($options);
  }

  public function init()
  {
    $this->setName(strtolower(get_class()));
    $this->setMethod("post");

    $oFormName = new Zend_Form_Element_Hidden("form_name");
    $oFormName->setValue(get_class());
    $oFormName->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oFormName);

    $oAddTo = new Zend_Form_Element_Select("addto_type_id");
    $oAddTo->setLabel("Typ zasobu:");
    $oAddTo->setRequired(TRUE);
    $oAddTo->addMultiOptions(array("Galeria", "Slider"));
    $this->addElement($oAddTo);

    $oAddToGallery = new Zend_Form_Element_Select("addto_gallery_id");
    $oAddToGallery->setLabel("Nazwa:");
    $oAddToGallery->setRequired(TRUE);
    $oAddToGallery->addMultiOptions($this->_aAllGallery);
    $this->addElement($oAddToGallery);

    $oAddToSlider = new Zend_Form_Element_Select("addto_slider_id");
    $oAddToSlider->setLabel("Nazwa:");
    $oAddToSlider->setRequired(TRUE);
    $oAddToSlider->addMultiOptions($this->_aAllSlider);
    $this->addElement($oAddToSlider);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "image_addto_submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/_defaultform.phtml");
    $this->clearDecorators();
    $this->setDecorators(array(
      array($oViewScript)
    ));

    $oElements = $this->getElements();
    foreach ($oElements as $oElement) {
      $oElement->setFilters($this->_aFilters);
      $oElement->removeDecorator("Errors");
    }
  }

}

?>
