<?php

class Admin_Form_UserRole extends Zend_Form
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
    $oFormName->removeDecorator("Label");
    $oFormName->setValue(get_class());
    $this->addElement($oFormName);

    $oRoleName = new Zend_Form_Element_Text("role_name");
    $oRoleName->setLabel("Nazwa:");
    $oRoleName->addValidator(new Zend_Validate_StringLength(array("min" => 1, "max" => 40)));
    $oRoleName->addValidator(new AppCms2_Validate_UserRole());
    $oRoleName->setFilters($this->_aFilters);
    $oRoleName->setRequired(TRUE);
    $oRoleName->setAttrib("class", "valid");
    $this->addElement($oRoleName);

    $oRoleEditId = new Zend_Form_Element_Hidden("role_edit_id");
    $oRoleEditId->setValue(0);
    $oRoleEditId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oRoleEditId);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/userrole.phtml");
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
