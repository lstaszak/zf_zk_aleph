<?php

class Admin_Form_AddNotification extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_oAuth = null;
  private $_nUserId = null;
  private $_aAllUser = array();
  private $_aAllNotificationType = array();
  private $_aAllNotificationStatus = array();
  private $_aAllNotificationPriority = array();
  private $_aAllNotificationCategory = array();

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
    $oModelNotificationType = new Admin_Model_NotificationType();
    $oAllNotificationType = $oModelNotificationType->getAll();
    if (isset($oAllNotificationType)) {
      foreach ($oAllNotificationType as $oValue) {
        if ($oValue->id > 3)
          $this->_aAllNotificationType[$oValue->id] = stripcslashes($oValue->name);
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

    $oNotificationTypeId = new Zend_Form_Element_Select("notification_type_id");
    $oNotificationTypeId->setLabel("Type:");
    $oNotificationTypeId->setRequired(TRUE);
    $oNotificationTypeId->addMultiOptions($this->_aAllNotificationType);
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
    $oUserIdSelect->setValue($this->_nUserId);
    $oUserIdSelect->addMultiOptions($this->_aAllUser);
    $this->addElement($oUserIdSelect);

    $oSubject = new Zend_Form_Element_Text("subject");
    $oSubject->setLabel("Temat:");
    $oSubject->setFilters($this->_aFilters);
    $oSubject->setRequired(FALSE);
    $this->addElement($oSubject);

    $oComment = new Zend_Form_Element_Textarea("comment");
    $oComment->setLabel("Komentarz:");
    $oComment->setFilters($this->_aFilters);
    $oComment->setRequired(FALSE);
    $this->addElement($oComment);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = new Zend_Form_Element_Submit("submit_add_notification");
    $oSubmit->setLabel("Utwórz zgłoszenie");
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
