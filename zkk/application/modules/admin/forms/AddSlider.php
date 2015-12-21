<?php

class Admin_Form_AddSlider extends Zend_Form
{

  private $_aFilters = array("StringTrim");

  public function __construct($options = null)
  {
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

    $oFormSliderEditId = new Zend_Form_Element_Hidden("slider_edit_id");
    $oFormSliderEditId->setValue(0);
    $oFormSliderEditId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oFormSliderEditId);

    $oSliderName = new Zend_Form_Element_Text("name");
    $oSliderName->setLabel("Nazwa slidera:");
    $oSliderName->addValidator(new Zend_Validate_StringLength(array("min" => 3, "max" => 45)));
    $oSliderName->setRequired(TRUE);
    $oSliderName->setAttrib("class", "valid");
    $this->addElement($oSliderName);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
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

  public function clearForm()
  {
    $this->reset();
    $this->getElement("form_name")->setValue(get_class());
  }

}

?>
