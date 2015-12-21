<?php

class Admin_Form_AddKeywords extends Zend_Form
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

    $oKeywordEditId = new Zend_Form_Element_Hidden("keyword_edit_id");
    $oKeywordEditId->setValue(0);
    $oKeywordEditId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oKeywordEditId);

    $oValue = new Zend_Form_Element_Text("value");
    $oValue->setLabel("Słowo kluczowe:");
    $oValue->addValidator(new Zend_Validate_StringLength(array("min" => 1, "max" => 45)));
    $oValue->setRequired(TRUE);
    $oValue->setAttrib("class", "valid");
    $this->addElement($oValue);

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
