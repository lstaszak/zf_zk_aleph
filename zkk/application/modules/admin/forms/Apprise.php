<?php

class Admin_Form_Apprise extends Zend_Form
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

    $oAddressEditId = new Zend_Form_Element_Hidden("address_edit_id");
    $oAddressEditId->setValue(0);
    $oAddressEditId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oAddressEditId);

    $oName = new Zend_Form_Element_Text("name");
    $oName->setLabel("Nazwa:");
    $oName->addValidator(new Zend_Validate_StringLength(array("min" => 3, "max" => 255)));
    $oName->setRequired(TRUE);
    $oName->setAttrib("class", "valid");
    $this->addElement($oName);

    $oUrl = new Zend_Form_Element_Textarea("url");
    $oUrl->setLabel("Adres:");
    $oUrl->setRequired(FALSE);
    $this->addElement($oUrl);

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
