<?php

class Admin_Form_GetSybaseUserParam extends Zend_Form
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

    $oBBarcode = new Zend_Form_Element_Text("bbarcode_id");
    $oBBarcode->setLabel("Numer karty bibliotecznej:")->setFilters($this->_aFilters);
    $oBBarcode->addValidator(new AppCms2_Validate_BBarcode());
    $oBBarcode->addValidator(new Zend_Validate_Digits());
    $oBBarcode->setRequired(TRUE);
    $oBBarcode->setAttrib("class", "valid");
    $this->addElement($oBBarcode);

    $oSubmit = new Zend_Form_Element_Submit("submit_get_sybase_user_param");
    $oSubmit->setLabel("Pobierz");
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
