<?php

class Admin_Form_TrancateDb extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aTableName = array();

  public function __construct($options = null)
  {
    $this->_aTableName = array(
      "address" => "address",
      "chat" => "chat",
      "chat_message" => "chat_message",
      "chat_session" => "chat_session",
      "chat_user_sender" => "chat_user_sender",
      //"default_contact" => "default_contact",
      "dynamic_content" => "dynamic_content",
      //"email_notification" => "email_notification",
      //"facebook_box" => "facebook_box",
      "faq" => "faq",
      //"google_analytics" => "google_analytics",
      //"google_maps" => "google_maps",
      //"google_maps_marker" => "google_maps_marker",
      "helpdesk" => "helpdesk",
      "helpdesk_message" => "helpdesk_message",
      "helpdesk_session" => "helpdesk_session",
      "helpdesk_user_sender" => "helpdesk_user_sender",
      "image" => "image",
      "image_gallery" => "image_gallery",
      "image_module" => "image_module",
      "image_slider" => "image_slider",
      "mail" => "mail",
      "mail_message" => "mail_message",
      "mail_session" => "mail_session",
      "mail_user_sender" => "mail_user_sender",
      "news" => "news",
      "notification" => "notification",
      "portfolio" => "portfolio",
      "user_log" => "user_log",
      "user_new_account" => "user_new_account",
      "user_new_password" => "user_new_password",
      //"user_param" => "user_param",
      "video" => "video"
    );
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

    $oTableName = new Zend_Form_Element_Multiselect("table_name");
    $oTableName->addMultiOptions($this->_aTableName);
    $oTableName->addValidator(new Zend_Validate_InArray(array_keys($this->_aTableName)));
    $oTableName->setRequired(FALSE);
    $oTableName->setLabel("Nazwa tabeli:");
    $oTableName->setAttrib("class", "multiselect");
    $this->addElement($oTableName);

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
