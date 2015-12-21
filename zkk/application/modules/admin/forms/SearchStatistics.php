<?php

class Admin_Form_SearchStatistics extends Zend_Form
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

    $oPostStep = new Zend_Form_Element_Hidden("post_step");
    $oPostStep->addValidator(new Zend_Validate_GreaterThan(0));
    $oPostStep->addValidator(new Zend_Validate_LessThan(2));
    $oPostStep->setValue(1);
    $oPostStep->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oPostStep);

    $oUserIdSelect = new Zend_Form_Element_Select("search_notification_user_id");
    $oUserIdSelect->setLabel("Konsultant:");
    $oUserIdSelect->setRequired(FALSE);
    $oUserIdSelect->addMultiOptions($this->_aAllUser);
    $this->addElement($oUserIdSelect);

    $oFromInitDate = new Zend_Form_Element_Text("from_init_date");
    $oFromInitDate->setLabel("Data rozpoczęcia (od):");
    $oFromInitDate->setRequired(FALSE);
    $oFromInitDate->setFilters($this->_aFilters);
    $this->addElement($oFromInitDate);

    $oToInitDate = new Zend_Form_Element_Text("to_init_date");
    $oToInitDate->setLabel("Data rozpoczęcia (do):");
    $oToInitDate->setRequired(FALSE);
    $oToInitDate->setFilters($this->_aFilters);
    $this->addElement($oToInitDate);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "search_statistics");
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
