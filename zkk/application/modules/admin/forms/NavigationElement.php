<?php

class Admin_Form_NavigationElement extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aNavigationElement = null;

  public function __construct($options = null)
  {
    $this->_aNavigationElement = array("Moduł", "Kontroler", "Akcja", "Zasób", "Uprawnienie");
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

    $oSelectNavigationElementId = new Zend_Form_Element_Select("navigation_element_id");
    $oSelectNavigationElementId->setLabel("Typ:");
    $oSelectNavigationElementId->setRequired(TRUE);
    $oSelectNavigationElementId->addMultiOptions($this->_aNavigationElement);
    $this->addElement($oSelectNavigationElementId);

    $oValue = new Zend_Form_Element_Text("value");
    $oValue->addValidator(new Zend_Validate_StringLength(array("min" => 1, "max" => 45)));
    $oValue->addValidator(new AppCms2_Validate_NavigationElement());
    $oValue->setLabel("Wartość:");
    $oValue->setRequired(TRUE);
    $oValue->setAttrib("class", "valid");
    $this->addElement($oValue);

    $oNavigationElementCopy = new Zend_Form_Element_Hidden("navigation_element_edit_id");
    $oNavigationElementCopy->setValue(0);
    $oNavigationElementCopy->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oNavigationElementCopy);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/navigationelement.phtml");
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
