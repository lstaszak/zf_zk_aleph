<?php

class Admin_Form_ChangePassword extends Zend_Form
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

    $oOldPassword = new Zend_Form_Element_Password("old_password");
    $oOldPassword->addValidator(new Zend_Validate_Alnum());
    $oOldPassword->setLabel("Stare hasło:")->setRequired(TRUE);
    $oOldPassword->setAttrib("class", "valid");
    $this->addElement($oOldPassword);

    $oNewPassword = new Zend_Form_Element_Password("new_password");
    $oNewPassword->addValidator(new Zend_Validate_Alnum());
    $oNewPassword->setLabel("Nowe hasło:")->setRequired(TRUE);
    $oNewPassword->setAttrib("class", "valid");
    $this->addElement($oNewPassword);

    $oNewPasswordConfirm = new Zend_Form_Element_Password("new_password_confirm");
    $oNewPasswordConfirm->addValidator(new AppCms2_Validate_PasswordConfirmation());
    $oNewPasswordConfirm->addValidator(new Zend_Validate_Alnum());
    $oNewPasswordConfirm->setLabel("Powtórz nowe hasło:")->setRequired(TRUE);
    $oNewPasswordConfirm->setAttrib("class", "valid");
    $this->addElement($oNewPasswordConfirm);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/changepassword.phtml");
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
