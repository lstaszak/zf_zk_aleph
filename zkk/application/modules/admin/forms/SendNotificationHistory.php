<?php

class Admin_Form_SendNotificationHistory extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_oAuth = null;
  private $_nUserId = null;
  private $_aAllUser = array();

  public function __construct($options = null)
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity())
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
    $oModelUser = new Admin_Model_User();
    $oAllUser = $oModelUser->findUsers(array(3, 4));
    if (isset($oAllUser)) {
      $this->_aAllUser[0] = "-";
      foreach ($oAllUser as $oValue) {
        $this->_aAllUser[$oValue->id] = trim($oValue->first_name . " " . $oValue->last_name);
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

    $oNotificationId = new Zend_Form_Element_Hidden("notification_id");
    $oNotificationId->setValue(0);
    $oNotificationId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oNotificationId);

    $oEmailAddress = new Zend_Form_Element_Text("email_address");
    $oEmailAddress->setLabel("Adres e-mail:")->setFilters($this->_aFilters);
    $oEmailAddress->addValidator(new Zend_Validate_EmailAddress());
    $oEmailAddress->setRequired(TRUE);
    $oEmailAddress->setAttrib("class", "valid");
    $this->addElement($oEmailAddress);

    $oSubmit = new Zend_Form_Element_Submit("submit_send_notification_history");
    $oSubmit->setLabel("Wyślij");
    $this->addElement($oSubmit);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

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
