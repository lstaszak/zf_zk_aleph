<?php

class Admin_Form_PasswordRemind extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aEmailAddress = null;

  public function __construct($options = null)
  {
    $oModelUser = new Admin_Model_VUser();
    $aEmailAddress = $oModelUser->getAllEmailAddress()->toArray();
    if (isset($aEmailAddress))
      foreach ($aEmailAddress as $aValue) {
        $this->_aEmailAddress[$aValue["email_address"]] = $aValue["first_name"] . " " . $aValue["last_name"];
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

    $oSelectEmailAddress = new Zend_Form_Element_Text("user_email_address");
    $oSelectEmailAddress->addValidator(new Zend_Validate_EmailAddress());
    $oSelectEmailAddress->setLabel("Adres e-mail:");
    $oSelectEmailAddress->setRequired(TRUE);
    //$oSelectEmailAddress->addMultiOptions($this->_aEmailAddress);
    $oSelectEmailAddress->setAttrib("class", "valid");
    $this->addElement($oSelectEmailAddress);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Przypomnij hasło");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/passwordremind.phtml");
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
