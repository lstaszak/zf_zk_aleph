<?php

class Admin_Form_Notification extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_oAuth = null;
  private $_nUserId = null;
  private $_aAllUser = array();
  private $_aAllNotificationStatus = array();
  private $_aAllNotificationPriority = array();
  private $_aAllNotificationCategory = array();
  private $_aAllUserCategory = array();

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
    $oModelNotificationPriority = new Admin_Model_NotificationPriority();
    $oAllNotificationPriority = $oModelNotificationPriority->getAll();
    if (isset($oAllNotificationPriority)) {
      foreach ($oAllNotificationPriority as $oValue) {
        $this->_aAllNotificationPriority[$oValue->id] = stripcslashes($oValue->name);
      }
    }
    $oModelNotificationStatus = new Admin_Model_NotificationStatus();
    $oAllNotificationStatus = $oModelNotificationStatus->getAll();
    if (isset($oAllNotificationStatus)) {
      foreach ($oAllNotificationStatus as $oValue) {
        $this->_aAllNotificationStatus[$oValue->id] = stripcslashes($oValue->name);
      }
    }
    $oModelNotificationCategory = new Admin_Model_NotificationCategory();
    $oAllNotificationCategory = $oModelNotificationCategory->getAll();
    if (isset($oAllNotificationCategory)) {
      foreach ($oAllNotificationCategory as $oValue) {
        $this->_aAllNotificationCategory[0] = "-";
        $this->_aAllNotificationCategory[$oValue->id] = stripcslashes($oValue->name);
      }
    }
    $oModelUserCategory = new Admin_Model_UserCategory();
    $aAllUserCategory = $oModelUserCategory->getAll();
    if (isset($aAllUserCategory)) {
      foreach ($aAllUserCategory as $oValue) {
        $this->_aAllUserCategory[$oValue->id] = stripcslashes($oValue->name);
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

    $oNotificationTypeId = new Zend_Form_Element_Hidden("notification_type_id");
    $oNotificationTypeId->setValue(0);
    $oNotificationTypeId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oNotificationTypeId);

    $oNotificationPriorityIdSelect = new Zend_Form_Element_Select("notification_priority_id");
    $oNotificationPriorityIdSelect->setLabel("Priorytet:");
    $oNotificationPriorityIdSelect->setRequired(TRUE);
    $oNotificationPriorityIdSelect->addMultiOptions($this->_aAllNotificationPriority);
    $this->addElement($oNotificationPriorityIdSelect);

    $oNotificationStatusIdSelect = new Zend_Form_Element_Select("notification_status_id");
    $oNotificationStatusIdSelect->setLabel("Status:");
    $oNotificationStatusIdSelect->setRequired(TRUE);
    $oNotificationStatusIdSelect->addMultiOptions($this->_aAllNotificationStatus);
    $this->addElement($oNotificationStatusIdSelect);

    $oNotificationCategoryIdSelect = new Zend_Form_Element_Select("notification_category_id");
    $oNotificationCategoryIdSelect->setLabel("Kategoria pytania:");
    $oNotificationCategoryIdSelect->setRequired(TRUE);
    $oNotificationCategoryIdSelect->addMultiOptions($this->_aAllNotificationCategory);
    $this->addElement($oNotificationCategoryIdSelect);

    $oUserIdSelect = new Zend_Form_Element_Select("user_id");
    $oUserIdSelect->setLabel("Konsultant:");
    $oUserIdSelect->setRequired(TRUE);
    $oUserIdSelect->addMultiOptions($this->_aAllUser);
    $this->addElement($oUserIdSelect);

    $oUserCategoryIdSelect = new Zend_Form_Element_Select("user_category_id");
    $oUserCategoryIdSelect->setLabel("Kategoria użytkownika:");
    $oUserCategoryIdSelect->setRequired(TRUE);
    $oUserCategoryIdSelect->addMultiOptions($this->_aAllUserCategory)->setValue(10);
    $this->addElement($oUserCategoryIdSelect);

    $oSubmit = new Zend_Form_Element_Submit("submit_change_notification");
    $oSubmit->setLabel("Zmień");
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
