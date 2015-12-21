<?php

class Admin_Form_RegisterBorrower extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllUserRole = array();
  private $_aCategory = array();

  public function __construct($options = null)
  {
    $oModelUserRole = new Admin_Model_UserRole();
    $oModelUserCategory = new Admin_Model_UserCategory();
    $aAllUserRole = $oModelUserRole->getAll()->toArray();
    if (count($aAllUserRole)) {
      foreach ($aAllUserRole as $aValue) {
        $this->_aAllUserRole[$aValue["id"]] = $aValue["role_name"];
      }
    }
    $oAllCategory = $oModelUserCategory->getAll();
    if (isset($oAllCategory)):
      foreach ($oAllCategory as $oRow):
        $this->_aCategory[$oRow->id] = trim($oRow->name);
      endforeach;
    endif;
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

    $oFirstName = new Zend_Form_Element_Text("first_name");
    $oFirstName->setLabel("Imię:")->setFilters($this->_aFilters);
    $oFirstName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oFirstName->setRequired(TRUE);
    $oFirstName->setAttrib("class", "valid");
    $this->addElement($oFirstName);

    $oLastName = new Zend_Form_Element_Text("last_name");
    $oLastName->setLabel("Nazwisko:")->setFilters($this->_aFilters);
    $oLastName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oLastName->setRequired(TRUE);
    $oLastName->setAttrib("class", "valid");
    $this->addElement($oLastName);

    $oEmailAddress = new Zend_Form_Element_Text("email_address");
    $oEmailAddress->setLabel("Adres e-mail:")->setFilters($this->_aFilters);
    $oEmailAddress->addValidator(new Zend_Validate_EmailAddress());
    $oEmailAddress->addValidator(new AppCms2_Validate_CheckUser());
    $oEmailAddress->setRequired(TRUE);
    $oEmailAddress->setAttrib("class", "valid");
    $this->addElement($oEmailAddress);

    $oEmailAddressConfirm = new Zend_Form_Element_Text("email_address_confirm");
    $oEmailAddressConfirm->setLabel("Powtórz adres e-mail:")->setFilters($this->_aFilters);
    $oEmailAddressConfirm->addValidator(new AppCms2_Validate_EmailConfirmation());
    $oEmailAddressConfirm->addValidator(new Zend_Validate_EmailAddress());
    $oEmailAddressConfirm->addValidator(new AppCms2_Validate_CheckUser());
    $oEmailAddressConfirm->setRequired(TRUE);
    $oEmailAddressConfirm->setAttrib("class", "valid");
    $this->addElement($oEmailAddressConfirm);

    $oPassword = new Zend_Form_Element_Password("password");
    $oPassword->setLabel("Hasło:")->setFilters($this->_aFilters);
    $oPassword->setRequired(TRUE);
    $oPassword->setAttrib("class", "valid");
    $this->addElement($oPassword);

    $oPhoneNumber = new Zend_Form_Element_Text("phone_number");
    $oPhoneNumber->setLabel("Numer telefonu:")->setFilters($this->_aFilters);
    $oPhoneNumber->addValidator(new AppCms2_Validate_CellPhone());
    $oPhoneNumber->setRequired(FALSE);
    $oPhoneNumber->setAttrib("class", "valid");
    $this->addElement($oPhoneNumber);

    $oUserCategoryId = new Zend_Form_Element_Select("user_category_id");
    $oUserCategoryId->setLabel("Kategoria użytkownika:")->setFilters($this->_aFilters);
    $oUserCategoryId->addMultiOptions($this->_aCategory);
    $oUserCategoryId->setRequired(TRUE);
    $oUserCategoryId->setAttrib("class", "valid");
    //$this->addElement($oUserCategoryId);

    $oStatute = new Zend_Form_Element_Select("statute");
    $oStatute->setLabel("Akceptuję regulamin:");
    $oStatute->addValidator(new AppCms2_Validate_Statute());
    $oStatute->setRequired(TRUE);
    $oStatute->addMultiOptions(array(0 => "NIE", 1 => "TAK"));
    $oStatute->setAttrib("class", "valid");
    $this->addElement($oStatute);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Załóż konto");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/registerborrower.phtml");
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
