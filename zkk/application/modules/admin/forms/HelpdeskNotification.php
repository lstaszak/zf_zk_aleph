<?php

class Admin_Form_HelpdeskNotification extends Zend_Form
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

    $oNotificationId = new Zend_Form_Element_Hidden("notification_id");
    $oNotificationId->setValue(0);
    $oNotificationId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oNotificationId);

    $oHelpdeskSessionId = new Zend_Form_Element_Hidden("helpdesk_session_id");
    $oHelpdeskSessionId->setValue(0);
    $oHelpdeskSessionId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oHelpdeskSessionId);

    $oMessage = new Zend_Form_Element_Textarea("message");
    $oMessage->setFilters($this->_aFilters);
    $oMessage->setRequired(FALSE);
    $oMessage->setAttrib("class", "ckeditor");
    $oMessage->setLabel("Wiadomość:");
    $this->addElement($oMessage);

    $this->addFileIdElement();

    $oAddFile = new Zend_Form_Element_Button("add_file_button");
    $oAddFile->setLabel("Dodaj załącznik");
    $oFormName->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oAddFile);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = new Zend_Form_Element_Submit("submit_send_message");
    $oSubmit->setLabel("Wyślij wiadomość");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/mailnotification.phtml");
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
    $this->addFileIdElement();
  }

  public function addFileIdElement()
  {
    $oFileId = new Zend_Form_Element_MultiCheckbox("file_id");
    $oFileId->setLabel("Załączniki:");
    $oFileId->setRequired(FALSE);
    $oFileId->addMultiOptions(array());
    $this->addElement($oFileId);
  }

}

?>
