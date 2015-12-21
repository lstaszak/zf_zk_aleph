<?php

class Admin_Form_DefaultContact extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aEmailAddress;

  public function __construct($options = null)
  {
    $oModelUser = new Admin_Model_User();
    $oEmailAddress = $oModelUser->findAdminRecipients();
    if (count($oEmailAddress))
      foreach ($oEmailAddress as $oValue) {
        $this->_aEmailAddress[$oValue->id] = $oValue->email_address;
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

    $oEmailAddress = new Zend_Form_Element_Multiselect("email_address");
    $oEmailAddress->addMultiOptions($this->_aEmailAddress);
    $oEmailAddress->setRequired(FALSE);
    $oEmailAddress->setLabel("Adres e-mail:");
    $oEmailAddress->setAttrib("class", "multiselect");
    $this->addElement($oEmailAddress);

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
