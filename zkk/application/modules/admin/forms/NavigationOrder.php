<?php

class Admin_Form_NavigationOrder extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllModule = array();

  public function __construct($options = null)
  {
    $oModelNavigationModule = new Admin_Model_NavigationModule();

    $aAllModule = $oModelNavigationModule->getAll()->toArray();
    if (count($aAllModule)) {
      foreach ($aAllModule as $aValue) {
        $this->_aAllModule[$aValue["id"]] = $aValue["value"];
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

    $oModuleId = new Zend_Form_Element_Select("navigation_module_id");
    $oModuleId->setLabel("Moduł:");
    $oModuleId->setRequired(TRUE);
    $oModuleId->addMultiOptions($this->_aAllModule);
    $this->addElement($oModuleId);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/navigationorder.phtml");
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
