<?php

class Admin_Form_ImageSettings extends Zend_Form
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

    $oImageSrc = new Zend_Form_Element_Text("image_src");
    $oImageSrc->setLabel("Nazwa wygenerowana:")->setFilters($this->_aFilters);
    $oImageSrc->setRequired(FALSE);
    $this->addElement($oImageSrc);

    $oUserName = new Zend_Form_Element_Text("image_user_name");
    $oUserName->setLabel("Nazwa użytkownika:")->setFilters($this->_aFilters);
    $oUserName->addValidator(new Zend_Validate_StringLength(array("max" => 50)));
    $oUserName->setRequired(TRUE);
    $oUserName->setAttrib("class", "valid");
    $this->addElement($oUserName);

    $oDescription = new Zend_Form_Element_Textarea("image_descr");
    $oDescription->setLabel("Opis:")->setFilters($this->_aFilters);
    $oDescription->addValidator(new Zend_Validate_StringLength(array("max" => 100)));
    $oDescription->setRequired(FALSE);
    $oDescription->setAttrib("class", "valid");
    $this->addElement($oDescription);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "image_settings_submit");
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

}

?>
