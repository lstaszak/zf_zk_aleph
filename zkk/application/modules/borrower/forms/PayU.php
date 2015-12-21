<?php

class Borrower_Form_PayU extends Zend_Form
{
  private $_aFilters = array("StringTrim");
  protected $_oPayU;

  public function __construct($oPlatnoscipl, $options = null)
  {
    $this->setPayments($oPlatnoscipl);
    parent::__construct($options);
  }

  public function setPayments($oPayU)
  {
    if ($oPayU instanceof AppCms2_Payments_PayU) {
      $this->_oPayU = $oPayU;
    } else {
      throw new Zend_Exception("błąd podczas inicjalizacji formularza");
    }
  }

  public function getPayments()
  {
    if (null === $this->_oPayU) {
      throw new Zend_Exception("nie przekazano obiektu płatności");
    }
    return $this->_oPayU;
  }

  public function init()
  {
    $oPayments = $this->getPayments();
    $sAction = $this->_oPayU->getUrlNewPayment();
    $this->setName(strtolower(get_class()));
    $this->setAction($sAction);
    $this->setMethod("POST");
    $oPosId = new Zend_Form_Element_Hidden("pos_id");
    $oPosId->setValue($oPayments->getPosId());
    $oPosId->removeDecorator("Label");
    $this->addElement($oPosId);
    $oPosAuthKey = new Zend_Form_Element_Hidden("pos_auth_key");
    $oPosAuthKey->setValue($oPayments->getPosAuthKey());
    $oPosAuthKey->removeDecorator("Label");
    $this->addElement($oPosAuthKey);
    $oOrderId = new Zend_Form_Element_Hidden("order_id");
    $oOrderId->setValue($oPayments->getOrderId());
    $oOrderId->removeDecorator("Label");
    $this->addElement($oOrderId);
    $oSessionId = new Zend_Form_Element_Hidden("session_id");
    $oSessionId->setValue($oPayments->getSessionId());
    $oSessionId->removeDecorator("Label");
    $this->addElement($oSessionId);
    $oAmount = new Zend_Form_Element_Hidden("amount");
    $oAmount->setValue($oPayments->getAmount());
    $oAmount->removeDecorator("Label");
    $this->addElement($oAmount);
    $oDesc = new Zend_Form_Element_Hidden("desc");
    $oDesc->setValue($oPayments->getDesc());
    $oDesc->removeDecorator("Label");
    $this->addElement($oDesc);
    $oClientIP = new Zend_Form_Element_Hidden("client_ip");
    $oClientIP->setValue($oPayments->getClientIP());
    $oClientIP->removeDecorator("Label");
    $this->addElement($oClientIP);
    $oFirstName = new Zend_Form_Element_Hidden("first_name");
    $oFirstName->setValue($oPayments->getFirstName());
    $oFirstName->removeDecorator("Label");
    $this->addElement($oFirstName);
    $oLastName = new Zend_Form_Element_Hidden("last_name");
    $oLastName->setValue($oPayments->getLastName());
    $oLastName->removeDecorator("Label");
    $this->addElement($oLastName);
    $oStreet = new Zend_Form_Element_Hidden("street");
    $oStreet->setValue($oPayments->getStreet());
    $oStreet->removeDecorator("Label");
    $this->addElement($oStreet);
    $oCity = new Zend_Form_Element_Hidden("city");
    $oCity->setValue($oPayments->getCity());
    $oCity->removeDecorator("Label");
    $this->addElement($oCity);
    $oPostCode = new Zend_Form_Element_Hidden("post_code");
    $oPostCode->setValue($oPayments->getPostCode());
    $oPostCode->removeDecorator("Label");
    $this->addElement($oPostCode);
    $oPhone = new Zend_Form_Element_Hidden("phone");
    $oPhone->setValue($oPayments->getPhone());
    $oPhone->removeDecorator("Label");
    $this->addElement($oPhone);
    $oEmail = new Zend_Form_Element_Hidden("email");
    $oEmail->setValue($oPayments->getEmailAddress());
    $oEmail->removeDecorator("Label");
    $this->addElement($oEmail);
    $oSig = new Zend_Form_Element_Hidden("sig");
    $oSig->setValue($oPayments->getSig());
    $oSig->removeDecorator("Label");
    $this->addElement($oSig);
    $oTs = new Zend_Form_Element_Hidden("ts");
    $oTs->setValue($oPayments->getTs());
    $oTs->removeDecorator("Label");
    $this->addElement($oTs);
    $oSubmit = new Zend_Form_Element_Submit("submit");
    $oSubmit->removeDecorator("Label");
    $this->addElement($oSubmit);
    $oElements = $this->getElements();
    foreach ($oElements as $oElement) {
      $oElement->removeDecorator("Label");
      $oElement->removeDecorator("Errors");
      $oElement->setFilters($this->_aFilters);
    }
    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewScript("_forms/payu.phtml");
    $this->clearDecorators();
    $this->setDecorators(array(
      array($oViewScript)
    ));
  }
}
