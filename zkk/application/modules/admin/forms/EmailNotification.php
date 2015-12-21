<?php

class Admin_Form_EmailNotification extends Zend_Form
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

    $oAdminAccountRegistrationSubject = new Zend_Form_Element_Textarea("admin_account_registration_subject");
    $oAdminAccountRegistrationSubject->setLabel("Temat - Rejestracja konta użytkownika (maila do administratora)");
    $oAdminAccountRegistrationSubject->setRequired(FALSE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oAdminAccountRegistrationSubject->setAttrib("class", "valid");
    $this->addElement($oAdminAccountRegistrationSubject);

    $oAdminAccountRegistration = new Zend_Form_Element_Textarea("admin_account_registration");
    $oAdminAccountRegistration->setLabel("Treść - Rejestracja konta użytkownika (maila do administratora)");
    $oAdminAccountRegistration->setRequired(FALSE);
    $oAdminAccountRegistration->setAttrib("class", "ckeditor");
    $this->addElement($oAdminAccountRegistration);

    $oAdminAccountRegistrationAndActivationSubject = new Zend_Form_Element_Textarea("admin_account_registration_and_activation_subject");
    $oAdminAccountRegistrationAndActivationSubject->setLabel("Temat - Rejestracja i aktywacja konta użytkownika (maila do administratora)");
    $oAdminAccountRegistrationAndActivationSubject->setRequired(FALSE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oAdminAccountRegistrationAndActivationSubject->setAttrib("class", "valid");
    $this->addElement($oAdminAccountRegistrationAndActivationSubject);

    $oAdminAccountRegistrationAndActivation = new Zend_Form_Element_Textarea("admin_account_registration_and_activation");
    $oAdminAccountRegistrationAndActivation->setLabel("Treść - Rejestracja i aktywacja konta użytkownika (maila do administratora)");
    $oAdminAccountRegistrationAndActivation->setRequired(FALSE);
    $oAdminAccountRegistrationAndActivation->setAttrib("class", "ckeditor");
    $this->addElement($oAdminAccountRegistrationAndActivation);

    $oUserAccountRegistrationSubject = new Zend_Form_Element_Textarea("user_account_registration_subject");
    $oUserAccountRegistrationSubject->setLabel("Temat - Rejestracja konta użytkownika");
    $oUserAccountRegistrationSubject->setRequired(FALSE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oUserAccountRegistrationSubject->setAttrib("class", "valid");
    $this->addElement($oUserAccountRegistrationSubject);

    $oUserAccountRegistration = new Zend_Form_Element_Textarea("user_account_registration");
    $oUserAccountRegistration->setLabel("Treść - Rejestracja konta użytkownika");
    $oUserAccountRegistration->setRequired(FALSE);
    $oUserAccountRegistration->setAttrib("class", "ckeditor");
    $this->addElement($oUserAccountRegistration);

    $oUserAccountRegistrationAndActivationSubject = new Zend_Form_Element_Textarea("user_account_registration_and_activation_subject");
    $oUserAccountRegistrationAndActivationSubject->setLabel("Temat - Rejestracja i jednoczesna aktywacja konta użytkownika");
    $oUserAccountRegistrationAndActivationSubject->setRequired(FALSE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oUserAccountRegistrationAndActivationSubject->setAttrib("class", "valid");
    $this->addElement($oUserAccountRegistrationAndActivationSubject);

    $oUserAccountRegistrationAndActivation = new Zend_Form_Element_Textarea("user_account_registration_and_activation");
    $oUserAccountRegistrationAndActivation->setLabel("Treść - Rejestracja i jednoczesna aktywacja konta użytkownika");
    $oUserAccountRegistrationAndActivation->setRequired(FALSE);
    $oUserAccountRegistrationAndActivation->setAttrib("class", "ckeditor");
    $this->addElement($oUserAccountRegistrationAndActivation);

    $oUserAccountActivationSubject = new Zend_Form_Element_Textarea("user_account_activation_subject");
    $oUserAccountActivationSubject->setLabel("Temat - Aktywacja konta użytkownika");
    $oUserAccountActivationSubject->setRequired(FALSE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oUserAccountActivationSubject->setAttrib("class", "valid");
    $this->addElement($oUserAccountActivationSubject);

    $oUserAccountActivation = new Zend_Form_Element_Textarea("user_account_activation");
    $oUserAccountActivation->setLabel("Treść - Aktywacja konta użytkownika");
    $oUserAccountActivation->setRequired(FALSE);
    $oUserAccountActivation->setAttrib("class", "ckeditor");
    $this->addElement($oUserAccountActivation);

    $oUserAccountDeactivationSubject = new Zend_Form_Element_Textarea("user_account_deactivation_subject");
    $oUserAccountDeactivationSubject->setLabel("Temat - Dezaktywacja konta użytkownika");
    $oUserAccountDeactivationSubject->setRequired(FALSE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oUserAccountDeactivationSubject->setAttrib("class", "valid");
    $this->addElement($oUserAccountDeactivationSubject);

    $oUserAccountDeactivation = new Zend_Form_Element_Textarea("user_account_deactivation");
    $oUserAccountDeactivation->setLabel("Treść - Dezaktywacja konta użytkownika");
    $oUserAccountDeactivation->setRequired(FALSE);
    $oUserAccountDeactivation->setAttrib("class", "ckeditor");
    $this->addElement($oUserAccountDeactivation);

    $oNewPasswordSubject = new Zend_Form_Element_Textarea("new_password_subject");
    $oNewPasswordSubject->setLabel("Temat - Nowe hasło");
    $oNewPasswordSubject->setRequired(FALSE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oNewPasswordSubject->setAttrib("class", "valid");
    $this->addElement($oNewPasswordSubject);

    $oNewPassword = new Zend_Form_Element_Textarea("new_password");
    $oNewPassword->setLabel("Treść - Nowe hasło");
    $oNewPassword->setRequired(FALSE);
    $oNewPassword->setAttrib("class", "ckeditor");
    $this->addElement($oNewPassword);

    $oNewPasswordConfirmationSubject = new Zend_Form_Element_Textarea("new_password_confirmation_subject");
    $oNewPasswordConfirmationSubject->setLabel("Temat - Potwierdzenie nowego hasła");
    $oNewPasswordConfirmationSubject->setRequired(FALSE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oNewPasswordConfirmationSubject->setAttrib("class", "valid");
    $this->addElement($oNewPasswordConfirmationSubject);

    $oNewPasswordConfirmation = new Zend_Form_Element_Textarea("new_password_confirmation");
    $oNewPasswordConfirmation->setLabel("Treść - Potwierdzenie nowego hasła");
    $oNewPasswordConfirmation->setRequired(FALSE);
    $oNewPasswordConfirmation->setAttrib("class", "ckeditor");
    $this->addElement($oNewPasswordConfirmation);

    $oConclusion = new Zend_Form_Element_Textarea("conclusion");
    $oConclusion->setLabel("Stopka");
    $oConclusion->setRequired(FALSE);
    $oConclusion->setAttrib("class", "ckeditor");
    $this->addElement($oConclusion);

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
