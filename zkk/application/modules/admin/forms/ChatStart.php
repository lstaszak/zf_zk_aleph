<?php

class Admin_Form_ChatStart extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_oAuth = null;
  private $_aUser = array();
  private $_aChatUserRecipients = array();
  private $_aCategory = array();

  public function __construct($options = null)
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    $oModelUser = new Admin_Model_User();
    $oModelUserCategory = new Admin_Model_UserCategory();
    if ($this->_oAuth->hasIdentity()) {
      $this->_aUser = $oModelUser->findUser($this->_oAuth->getStorage()->read()->user_id);
    }
    $oChatUserRecipients = $oModelUser->findAskOnline();
    if (isset($oChatUserRecipients)):
      foreach ($oChatUserRecipients as $oRow):
        $aUserParam = $oModelUser->findUser($oRow->id);
        $this->_aChatUserRecipients[$oRow->id] = trim($aUserParam["first_name"] . " " . $aUserParam["last_name"]);
      endforeach;
    endif;
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
    $this->setAction("/admin/ask/chatsender");

    $oFormName = new Zend_Form_Element_Hidden("form_name");
    $oFormName->setValue(get_class());
    $oFormName->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oFormName);

    $oChatUserRecipientId = new Zend_Form_Element_Select("chat_user_recipient_id");
    $oChatUserRecipientId->setLabel("Wybierz konsultanta:")->setFilters($this->_aFilters);
    $oChatUserRecipientId->addValidator(new Zend_Validate_InArray(array_keys($this->_aChatUserRecipients)));
    $oChatUserRecipientId->addValidator(new AppCms2_Validate_IsOnline());
    $oChatUserRecipientId->addMultiOptions($this->_aChatUserRecipients);
    $oChatUserRecipientId->setRequired(TRUE);
    //$oChatUserRecipientId->setAttrib("class", "valid");
    $this->addElement($oChatUserRecipientId);

    $oFirstName = new Zend_Form_Element_Text("first_name");
    $oFirstName->setLabel("Imię i nazwisko:")->setFilters($this->_aFilters);
    $oFirstName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oFirstName->setRequired(TRUE)->setValue(trim($this->_aUser["first_name"] . " " . $this->_aUser["last_name"]));
    //$oFirstName->setAttrib("class", "valid");
    $this->addElement($oFirstName);

    $oEmailAddress = new Zend_Form_Element_Text("email_address");
    $oEmailAddress->setLabel("Adres e-mail:")->setFilters($this->_aFilters);
    $oEmailAddress->addValidator(new Zend_Validate_EmailAddress());
    $oEmailAddress->setRequired(TRUE)->setValue($this->_aUser[$oEmailAddress->getName()]);
    //$oEmailAddress->setAttrib("class", "valid");
    $this->addElement($oEmailAddress);

    $oUserCategoryId = new Zend_Form_Element_Select("user_category_id");
    $oUserCategoryId->setLabel("Kategoria użytkownika:")->setFilters($this->_aFilters);
    $oUserCategoryId->addMultiOptions($this->_aCategory);
    $oUserCategoryId->setRequired(TRUE)->setValue($this->_aUser[$oUserCategoryId->getName()]);
    $oUserCategoryId->setAttrib("class", "valid");
    //$this->addElement($oUserCategoryId);

    $oPhone = new Zend_Form_Element_Text("phone");
    $oPhone->setLabel("Numer telefonu:")->setFilters($this->_aFilters);
    $oPhone->addValidator(new AppCms2_Validate_CellPhone());
    $oPhone->setRequired(FALSE)->setValue($this->_aUser["phone_number"]);
    $oPhone->setAttrib("class", "valid");
    //$this->addElement($oPhone);

    $oBBarcode = new Zend_Form_Element_Text("bbarcode_id");
    $oBBarcode->setLabel("Numer karty bibliotecznej:")->setFilters($this->_aFilters);
    $oBBarcode->addValidator(new AppCms2_Validate_BBarcode());
    $oBBarcode->addValidator(new Zend_Validate_Digits());
    $oBBarcode->setRequired(FALSE)->setValue($this->_aUser[$oBBarcode->getName()]);
    $oBBarcode->setAttrib("class", "valid");
    //$this->addElement($oBBarcode);
    //$this->addDisplayGroup(array($oPhone, $oBBarcode), "not_valid", array("legend" => "Wprowadzenie poniższych danych nie jest obowiązkowe, ale ułatwi udzielenie odpowiedzi lub skontaktowanie się z Państwem telefonicznie"));

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Dalej");
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
