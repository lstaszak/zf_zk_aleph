<?php

class Admin_Form_Lang extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllLang = array();

  public function __construct($options = null)
  {
    $this->_aAllLang = array("lang_pl" => "język polski", "lang_en" => "język angielski");
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

    $oTranslation = new Zend_Form_Element_Select("translation");
    $oTranslation->setLabel("Tłumaczenie:");
    $oTranslation->setRequired(TRUE)->setAttrib("class", "valid");
    $oTranslation->addMultiOptions($this->_aAllLang);
    $this->addElement($oTranslation);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Dalej");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/lang.phtml");
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
