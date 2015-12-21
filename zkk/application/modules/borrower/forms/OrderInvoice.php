<?php

class Borrower_Form_OrderInvoice extends Zend_Form
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
    $oPaymentId = new Zend_Form_Element_Text("id");
    $oPaymentId->setLabel("Numer płatności:")->setAttrib("disabled", "disabled");
    $oPaymentId->addValidator(new Zend_Validate_Digits());
    $oPaymentId->setRequired(TRUE);
    $this->addElement($oPaymentId);
    $oOrderId = new Zend_Form_Element_Text("order_id");
    $oOrderId->setLabel("Numer zamówienia:")->setAttrib("disabled", "disabled");
    $oOrderId->setRequired(TRUE);
    $this->addElement($oOrderId);
    $oAmount = new Zend_Form_Element_Text("amount");
    $oAmount->setLabel("Cena:")->setAttrib("disabled", "disabled");
    //$oAmount->addValidator(new Zend_Validate_Digits());
    $oAmount->setRequired(TRUE);
    $this->addElement($oAmount);
    $oAddressEmail = new Zend_Form_Element_Text("user_email_address");
    $oAddressEmail->setLabel("Adres e-mail:")->setAttrib("disabled", "disabled");
    $oAddressEmail->addValidator(new Zend_Validate_EmailAddress());
    $oAddressEmail->setRequired(TRUE);
    $oAddressEmail->setAttrib("class", "valid");
    $this->addElement($oAddressEmail);
    $oUserName = new Zend_Form_Element_Text("user_name");
    $oUserName->setLabel("Imię i nazwisko:");
    $oUserName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oUserName->setRequired(TRUE);
    $oUserName->setAttrib("class", "valid");
    $this->addElement($oUserName);
    $oCompanyName = new Zend_Form_Element_Text("company_name");
    $oCompanyName->setLabel("Nazwa firmy:");
    //$oCompanyName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oCompanyName->setRequired(TRUE);
    $oCompanyName->setAttrib("class", "valid");
    $this->addElement($oCompanyName);
    $oUserNip = new Zend_Form_Element_Text("user_nip");
    $oUserNip->setLabel("NIP:");
    $oUserNip->addValidator(new AppCms2_Validate_Nip());
    $oUserNip->setRequired(TRUE);
    $oUserNip->setAttrib("class", "valid");
    $this->addElement($oUserNip);
    $oCompanyAddress = new Zend_Form_Element_Textarea("company_address");
    $oCompanyAddress->setLabel("Adres firmy:");
    //$oCompanyAddress->addValidator(new AppCms2_Validate_SpecialAlpha());
    $oCompanyAddress->setRequired(TRUE);
    $oCompanyAddress->setAttrib("class", "valid");
    $this->addElement($oCompanyAddress);
    $oForwardingAddress = new Zend_Form_Element_Textarea("forwarding_address");
    $oForwardingAddress->setLabel("Adres korespondencyjny:");
    //$oForwardingAddress->addValidator(new AppCms2_Validate_SpecialAlpha());
    $oForwardingAddress->setRequired(FALSE);
    $oForwardingAddress->setAttrib("class", "valid");
    $this->addElement($oForwardingAddress);
    $oSubmitGetInvoice = new Zend_Form_Element_Submit("order_submit_get_invoice");
    $oSubmitGetInvoice->setLabel("Wyślij dane");
    $this->addElement($oSubmitGetInvoice);
    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 1440));
    $this->getElement("csrf_token")->removeDecorator("Label");
    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("borrower");
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
