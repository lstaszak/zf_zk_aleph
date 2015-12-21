<?php

class Admin_Form_ChatBtnAddAddress extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllAddressName = array();
  private $_aAllAddressUrl = array();

  public function __construct($options = null)
  {
    $oModelAddress = new Admin_Model_Address();
    $oAllAddress = $oModelAddress->getAll();
    if (isset($oAllAddress)) {
      foreach ($oAllAddress as $oValue) {
        $this->_aAllAddressName[$oValue->id] = stripcslashes($oValue->name);
        $this->_aAllAddressUrl[$oValue->id] = stripcslashes($oValue->url);
      }
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

    $oAddressSelect = new Zend_Form_Element_Select("address_select");
    $oAddressSelect->setLabel("Nazwa:");
    $oAddressSelect->setRequired(FALSE);
    $oAddressSelect->addMultiOptions($this->_aAllAddressName);
    $this->addElement($oAddressSelect);

    $oAddressAnswer = new Zend_Form_Element_Textarea("address_answer");
    $oAddressAnswer->setLabel("Odpowiedź:")->setFilters($this->_aFilters);
    $oAddressAnswer->setRequired(TRUE);
    $oAddressAnswer->setAttrib("class", "valid");
    $this->addElement($oAddressAnswer);

    $oSubmit = new Zend_Form_Element_Submit("submit_add_address");
    $oSubmit->setLabel("Dodaj");
    $this->addElement($oSubmit);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

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

  public function getAllAddressUrl()
  {
    return $this->_aAllAddressUrl;
  }

}

?>
