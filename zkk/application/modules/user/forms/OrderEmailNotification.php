<?php

class User_Form_OrderEmailNotification extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllOrderStatus = array();

  public function __construct($options = null)
  {
    $oModelOrderStatus = new User_Model_OrderStatus();
    $aAllOrderStatus = $oModelOrderStatus->getAll();
    foreach ($aAllOrderStatus as $aValue) {
      $this->_aAllOrderStatus[$aValue["id"]] = $aValue["user_name"];
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

    $oOrderEmailNotificationEditId = new Zend_Form_Element_Hidden("order_email_notification_edit_id");
    $oOrderEmailNotificationEditId->setValue(0);
    $oOrderEmailNotificationEditId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oOrderEmailNotificationEditId);

    $oOrderStatusOldId = new Zend_Form_Element_Select("order_status_id_old");
    $oOrderStatusOldId->setMultiOptions($this->_aAllOrderStatus);
    $oOrderStatusOldId->setLabel("Status zamówienia - stary:");
    $oOrderStatusOldId->setRequired(TRUE);
    //$oOrderStatusOldId->setRequired(TRUE)->addValidator(new Zend_Validate_InArray($this->_aAllOrderStatus));
    $oOrderStatusOldId->setAttrib("class", "valid");
    $this->addElement($oOrderStatusOldId);

    $oOrderStatusNewId = new Zend_Form_Element_Select("order_status_id_new");
    $oOrderStatusNewId->setMultiOptions($this->_aAllOrderStatus);
    $oOrderStatusNewId->setLabel("Status zamówienia - nowy:");
    $oOrderStatusNewId->setRequired(TRUE);
    //$oOrderStatusNewId->setRequired(TRUE)->addValidator(new Zend_Validate_InArray($this->_aAllOrderStatus));
    $oOrderStatusNewId->setAttrib("class", "valid");
    $this->addElement($oOrderStatusNewId);

    $oNotificationSubject = new Zend_Form_Element_Textarea("notification_subject");
    $oNotificationSubject->setLabel("Temat:");
    $oNotificationSubject->setRequired(TRUE)->addValidator(new Zend_Validate_StringLength(array("max" => 160)));
    $oNotificationSubject->setAttrib("class", "valid");
    $this->addElement($oNotificationSubject);

    $oNotification = new Zend_Form_Element_Textarea("notification");
    $oNotification->setLabel("Treść:");
    $oNotification->setRequired(FALSE);
    $oNotification->setAttrib("class", "ckeditor");
    $this->addElement($oNotification);

    $oAppriseBorrower = new Zend_Form_Element_Checkbox("apprise_borrower");
    $oAppriseBorrower->setLabel("Powiadom czytelnika:");
    $this->addElement($oAppriseBorrower);

    $oAppriseLibrarian = new Zend_Form_Element_Checkbox("apprise_librarian");
    $oAppriseLibrarian->setLabel("Powiadom bibliotekarza:");
    $this->addElement($oAppriseLibrarian);

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
