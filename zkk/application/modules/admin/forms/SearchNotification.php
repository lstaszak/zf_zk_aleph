<?php

class Admin_Form_SearchNotification extends Zend_Form
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

    $oNotificationTypeId = new Zend_Form_Element_MultiCheckbox("notification_type_id");
    $oNotificationTypeId->setLabel("Typ:");
    $oNotificationTypeId->setRequired(FALSE);
    $oNotificationTypeId->addMultiOptions($this->_aAllNotificationType);
    $this->addElement($oNotificationTypeId);

    $oNotificationStatusId = new Zend_Form_Element_MultiCheckbox("notification_status_id");
    $oNotificationStatusId->setLabel("Status:");
    $oNotificationStatusId->setRequired(FALSE);
    $oNotificationStatusId->addMultiOptions($this->_aAllNotificationStatus);
    $this->addElement($oNotificationStatusId);

    $oNotificationPriorityId = new Zend_Form_Element_MultiCheckbox("notification_priority_id");
    $oNotificationPriorityId->setLabel("Priorytet:");
    $oNotificationPriorityId->setRequired(FALSE);
    $oNotificationPriorityId->addMultiOptions($this->_aAllNotificationPriority);
    $this->addElement($oNotificationPriorityId);

    $oNotificationCategoryId = new Zend_Form_Element_Select("search_notification_category_id");
    $oNotificationCategoryId->setLabel("Kategoria pytania:");
    $oNotificationCategoryId->setRequired(FALSE);
    $oNotificationCategoryId->addMultiOptions($this->_aAllNotificationCategory);
    $this->addElement($oNotificationCategoryId);

    $oUserId = new Zend_Form_Element_Select("search_notification_user_id");
    $oUserId->setLabel("Konsultant:");
    $oUserId->setRequired(FALSE);
    $oUserId->addMultiOptions($this->_aAllUser);
    $this->addElement($oUserId);

    $oInitDate = new Zend_Form_Element_Text("init_date");
    $oInitDate->setLabel("Data rozpoczęcia:");
    $oInitDate->setRequired(FALSE);
    $oInitDate->setFilters($this->_aFilters);
    $this->addElement($oInitDate);

    $oBlankNotificationUserId = new Zend_Form_Element_Checkbox("search_blank_notification_user_id");
    $oBlankNotificationUserId->setLabel("Pokaż nieprzydzielone zgłoszenia:");
    $this->addElement($oBlankNotificationUserId);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "search_notification");
    $oSubmit->setLabel("Szukaj");
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
