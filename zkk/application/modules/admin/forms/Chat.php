<?php

class Admin_Form_Chat extends Zend_Form
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

    $oMessage = new Zend_Form_Element_Textarea("message");
    $oMessage->setFilters($this->_aFilters);
    $oMessage->setRequired(FALSE);
    $oMessage->removeDecorator("label");
    $this->addElement($oMessage);

    $oIsDing = new Zend_Form_Element_Checkbox("is_ding");
    $oIsDing->setLabel("Włącz dźwięk");
    $oIsDing->setValue(1);
    $this->addElement($oIsDing);

    $oSubmit = new Zend_Form_Element_Submit("send_message");
    $oSubmit->setLabel("Wyślij wiadomość");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/chat.phtml");
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
