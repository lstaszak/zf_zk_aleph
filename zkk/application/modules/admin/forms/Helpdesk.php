<?php

class Admin_Form_Helpdesk extends Zend_Form
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

    $oFirstName = new Zend_Form_Element_Text("first_name");
    $oFirstName->setLabel("Imię i nazwisko:")->setFilters($this->_aFilters);
    $oFirstName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oFirstName->setRequired(TRUE);
    $oFirstName->setAttrib("class", "valid");
    $this->addElement($oFirstName);

    $oEmailAddress = new Zend_Form_Element_Text("email_address");
    $oEmailAddress->setLabel("Adres e-mail:")->setFilters($this->_aFilters);
    $oEmailAddress->addValidator(new Zend_Validate_EmailAddress());
    $oEmailAddress->setRequired(TRUE);
    $oEmailAddress->setAttrib("class", "valid");
    $this->addElement($oEmailAddress);

    $oSubject = new Zend_Form_Element_Text("subject");
    $oSubject->setLabel("Temat wiadomości:")->setFilters($this->_aFilters);
    $oSubject->setRequired(TRUE);
    $oSubject->setAttrib("class", "valid");
    $this->addElement($oSubject);

    $oPhone = new Zend_Form_Element_Text("phone");
    $oPhone->setLabel("Numer telefonu:")->setFilters($this->_aFilters);
    $oPhone->addValidator(new AppCms2_Validate_CellPhone());
    $oPhone->setRequired(FALSE);
    $oPhone->setAttrib("class", "valid");
    $this->addElement($oPhone);

    $oMessage = new Zend_Form_Element_Textarea("message");
    $oMessage->setLabel("Wiadomość:")->setFilters($this->_aFilters);
    $oMessage->setRequired(TRUE);
    $oMessage->setAttrib("class", "valid");
    $this->addElement($oMessage);

    $oCopy = new Zend_Form_Element_Checkbox("copy");
    $oCopy->setLabel("Chcę otrzymać kopię wiadomości:");
    $this->addElement($oCopy);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Wyślij wiadomość");
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
