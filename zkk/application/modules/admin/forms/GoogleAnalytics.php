<?php

class Admin_Form_GoogleAnalytics extends Zend_Form
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

    $oProfile = new Zend_Form_Element_Text("profile");
    $oProfile->setLabel("Identyfikator profilu:");
    $oProfile->addValidator(new Zend_Validate_Digits());
    $oProfile->setRequired(TRUE);
    $oProfile->setAttrib("class", "valid");
    $this->addElement($oProfile);

    $oCode = new Zend_Form_Element_Text("code");
    $oCode->setLabel("Identyfikator śledzenia:");
    $oCode->setRequired(TRUE);
    $oCode->setAttrib("class", "valid");
    $this->addElement($oCode);

    $oStartDate = new Zend_Form_Element_Text("start_date");
    $oStartDate->setLabel("Zakres początkowy:");
    $oStartDate->addValidator(new Zend_Validate_Date("Y-m-d"));
    $oStartDate->setRequired(TRUE);
    $oStartDate->setAttrib("class", "valid");
    $this->addElement($oStartDate);

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
