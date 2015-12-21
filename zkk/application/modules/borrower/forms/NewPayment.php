<?php

class Borrower_Form_NewPayment extends Zend_Form
{
  private $_aFilters = array("StringTrim");
  private $_oAuth;
  private $_nUserId;
  private $_aUserParam;
  private $_bIsInit;
  private $_sKey3 = "6f0c041c36b3c064c82448676124e139";
  private $_nMerchantId = 244;
  private $_nAmount;
  private $_sDescr;
  private $_nTime;
  private $_sHash;
  private $_sAuthKey1;
  private $_sAuthKey2;
  private $_sUrl;

  public function __construct($options = null)
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
    }
    $this->setAmount();
    if ($this->getAmount()) {
      $this->setMerchantId();
      $this->setTime();
      $this->setDescr();
      $this->setUrl();
      $this->setAuthKey1();
      $this->setHash();
      $this->setUserParam();
      $this->_bIsInit = true;
    }
    parent::__construct($options);
  }

  public function getUserId()
  {
    return $this->_nUserId;
  }

  private function setUserParam()
  {
    $oModelUser = new Admin_Model_User();
    $this->_aUserParam = $oModelUser->findUser($this->_nUserId);
  }

  private function setMerchantId()
  {
    $this->_nMerchantId = 244;
  }

  public function getMerchantId()
  {
    return $this->_nMerchantId;
  }

  private function setAmount()
  {
    $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
    $this->_nAmount = (int)$oModelVOrderJournal->getCartTotalAmount($this->getUserId());
  }

  public function getAmount()
  {
    return $this->_nAmount;
  }

  private function setDescr()
  {
    $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
    $oModelOrderCart = new Borrower_Model_OrderCart();
    $oModelOrderJournalOrderCart = new Borrower_Model_OrderJournalOrderCart();
    $oUserOrderJournal = $oModelVOrderJournal->getUserOrderJournal($this->getUserId());
    $nOrderCartId = $oModelOrderCart->getOrderCartId($this->getUserId()); //pobiera id koszyka użytkownika
    foreach ($oUserOrderJournal as $oValue) { //sprawdza czy na pewno wszystkie zamówienia znajdują się w koszyku
      $bIsExists = $oModelOrderJournalOrderCart->findOrderJournal($oValue->id, $nOrderCartId);
      if (!$bIsExists)
        $oModelOrderJournalOrderCart->addOrderJournalOrderCart(array("order_journal_id" => $oValue->id, "order_cart_id" => $nOrderCartId));
    }
    $oCart = $oModelOrderJournalOrderCart->getCartJournals($nOrderCartId); //pobiera id czasopism znajdujących się w koszyku
    $nCartCount = $oCart->count();
    foreach ($oCart as $oValue) {
      $sPaymentDesc .= $oValue->order_journal_id;
      if ($nCartCount > 1)
        $sPaymentDesc .= "; ";
    }
    $sPaymentDesc = trim($sPaymentDesc);
    $this->_sDescr = "Zamówienie nr $sPaymentDesc";
  }

  public function getDescr()
  {
    return $this->_sDescr;
  }

  private function setTime()
  {
    $this->_nTime = time();
  }

  public function getTime()
  {
    return $this->_nTime;
  }

  private function setHash()
  {
    $this->_sHash = md5($this->getMerchantId() . $this->getAmount() . $this->getDescr() . "" . "" . $this->getUserId() . $this->getTime() . $this->getAuthKey1());
  }

  public function getHash()
  {
    return $this->_sHash;
  }

  private function setAuthKey1()
  {
    $this->_sAuthKey1 = "UDU3GPT4AES3961JJ6EVIEQ7TDDSIONNJEAT7SURD20CAATJ0IB0WD3R9ECNIZE95V1JCMXJE3GJ2NVXKT7GTXSJMU4EKQTVI8GGOLBNF72TZM90KNZHT1BTFBWZN4X7E453GC5FVF275HH6Z0CQNG1P4068ERKAP4OQ770ZCZOOXRZQ7J09HPD6QBV43TJWXLPTHN1T7N0KLA1VA1OL54HHPLZDZ219XTCF125TWX4TA9N16AQBAE1R9AA1WD8V";
  }

  public function getAuthKey1()
  {
    return $this->_sAuthKey1;
  }

  private function setAuthKey2()
  {
    $this->_sAuthKey2 = "W8L07TDIWTG89HTEEXKQO5GSN6BO3VCTO9B4MHRKPCGOATOOWTZP9U49VTNICMK031792G7H4DHDFWZA6FV1VW00Q4E366Q1WL5UKMRKVEVDXNGFLN8TW8MNSUZSM9TZK7PAWG28BLB2G1ES4T94DQTH0I34WRTKM02A5A4EMULSK2EA0UTX69XJB30TJT3N6X1HLU5BTZCQEP342W7HJTTWG10ARZLVKOB9XZ0E2495MV72XXZITWVHEDAN1JLH";
  }

  public function getAuthKey2()
  {
    return $this->_sAuthKey2;
  }

  private function setUrl()
  {
    $oViewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper("viewRenderer");
    $oViewRenderer->initView();
    $this->_sUrl = $oViewRenderer->view->sHost . "/borrower/orders";
  }

  public function getUrl()
  {
    return $this->_sUrl;
  }

  public function getIsInit()
  {
    return $this->_bIsInit;
  }

  public function getKey3()
  {
    return $this->_sKey3;
  }

  private function setSessionKey()
  {
    $oSessionNewPayment = new Zend_Session_Namespace("new_payment");
    if (isset($oSessionNewPayment->session_key))
      unset($oSessionNewPayment->session_key);
    $oSessionNewPayment->session_key = md5($this->_nUserId . $this->_aUserParam["email_address"] . $this->getAmount() . $this->getKey3());
  }

  public function clearSessionKey()
  {
    $oPersonelData = new Zend_Session_Namespace("new_payment");
    unset($oPersonelData->session_key);
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
    $oFirstName->setLabel("Imię:");
    $oFirstName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oFirstName->setRequired(TRUE);
    $oFirstName->setAttrib("class", "valid");
    $oFirstName->setValue($this->_aUserParam["first_name"]);
    $this->addElement($oFirstName);
    $oLastName = new Zend_Form_Element_Text("last_name");
    $oLastName->setLabel("Nazwisko:");
    $oLastName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oLastName->setRequired(TRUE);
    $oLastName->setAttrib("class", "valid");
    $oLastName->setValue($this->_aUserParam["last_name"]);
    $this->addElement($oLastName);
    $oAddressEmail = new Zend_Form_Element_Text("email_address");
    $oAddressEmail->setLabel("Adres e-mail:");
    $oAddressEmail->addValidator(new Zend_Validate_EmailAddress());
    $oAddressEmail->setRequired(TRUE);
    $oAddressEmail->setAttrib("class", "valid");
    $oAddressEmail->setAttrib("readonly", "readonly");
    $oAddressEmail->setValue($this->_aUserParam["email_address"]);
    $this->addElement($oAddressEmail);
    $oAmount = new Zend_Form_Element_Text("amount");
    $oAmount->setLabel("Kwota:");
    $oAmount->setRequired(TRUE);
    $oAmount->setAttrib("class", "valid");
    $oAmount->setAttrib("readonly", "readonly");
    $oAmount->setValue(($this->_nAmount / 100));
    $this->addElement($oAmount);
    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 1440));
    $this->getElement("csrf_token")->removeDecorator("Label");
    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapłać");
    $this->addElement($oSubmit);
    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("borrower");
    $oViewScript->setViewScript("_forms/newpayment.phtml");
    $this->clearDecorators();
    $this->setDecorators(array(
      array($oViewScript)
    ));
    $oElements = $this->getElements();
    foreach ($oElements as $oElement) {
      $oElement->setFilters($this->_aFilters);
      $oElement->removeDecorator("Errors");
    }
    $this->setSessionKey();
  }
}

?>
