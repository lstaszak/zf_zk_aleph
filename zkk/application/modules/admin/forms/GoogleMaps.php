<?php

class Admin_Form_GoogleMaps extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_sApiKey = null;

  public function __construct($options = null)
  {
    $oModelGoogleMaps = new Admin_Model_GoogleMaps();
    $this->_sApiKey = $oModelGoogleMaps->getRow(1)->api_key;
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

    $oApiKey = new Zend_Form_Element_Text("api_key");
    $oApiKey->setLabel("Google Maps API key:");
    $oApiKey->setRequired(FALSE);
    $oApiKey->setAttrib("class", "valid");
    $oApiKey->setValue($this->_sApiKey);
    $this->addElement($oApiKey);

    $oTitle = new Zend_Form_Element_Text("unique_title");
    $oTitle->setLabel("Identyfikator punktu:");
    $oTitle->setAttrib("disabled", "disabled");
    $this->addElement($oTitle);

    $oLng = new Zend_Form_Element_Text("lng");
    $oLng->setLabel("Współrzędna X (longitude):");
    $oLng->setRequired(TRUE);
    $oLng->setAttrib("class", "valid");
    $this->addElement($oLng);

    $oLat = new Zend_Form_Element_Text("lat");
    $oLat->setLabel("Współrzędna Y (latitude):");
    $oLat->setRequired(TRUE);
    $oLat->setAttrib("class", "valid");
    $this->addElement($oLat);

    $oDesc = new Zend_Form_Element_Textarea("desc");
    $oDesc->setLabel("Opis:");
    $oDesc->setRequired(FALSE);
    $oDesc->setAttrib("class", "ckeditor");
    $this->addElement($oDesc);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

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

  public function clearForm()
  {
    $this->reset();
    $this->getElement("form_name")->setValue(get_class());
  }

}

?>
