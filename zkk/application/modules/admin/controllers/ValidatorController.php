<?php

class Admin_ValidatorController extends Zend_Controller_Action
{

  public function validateformAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oCsrfSession = new Zend_Session_Namespace("Zend_Form_Element_Hash_salt_csrf_token");
    $sClassName = $this->_getParam("form_name");
    $oCsrfSession->hash = $this->_getParam("hash");
    $oObjectReflection = new ReflectionClass($sClassName);
    $oFormInstance = $oObjectReflection->newInstanceArgs();
    if (in_array($sClassName, array("Borrower_Form_OrderSettings", "User_Form_OrderSettings"))) {
      $oModelOrderJournal = new User_Model_OrderJournal();
      $nOrderId = $this->_getParam("order_id");
      $oOrderJournal = $oModelOrderJournal->getRow($nOrderId);
      if (isset($nOrderId) && is_numeric($nOrderId) && isset($oOrderJournal->order_status_id) && is_numeric($oOrderJournal->order_status_id)) {
        $oFormInstance->getOrderFields($oOrderJournal->order_status_id, $oOrderJournal->is_journal_collection);
      }
    }
    if (is_array($this->_getParam("valid"))):
      $aElement = array();
      foreach ($this->_getParam("valid") as $aValue) {
        $aElement["valid"][$aValue["name"]] = $aValue["value"];
      }
      if (is_array($this->_getParam("remove"))):
        foreach ($this->_getParam("remove") as $aValue):
          if ($oFormInstance->getElement($aValue["name"])):
            $oFormInstance->removeElement($aValue["name"]);
          endif;
        endforeach;
      endif;
      $oFormInstance->isValid($aElement["valid"]);
    elseif ($oFormInstance->getElement($this->_getParam("element_name"))):
      $oFormInstance->getElement($this->_getParam("element_name"))->isValid($this->_getParam("element_value"));
    endif;
    $aJson = $oFormInstance->getMessages();
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function deleteAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $bJson = false;
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits()),
      "model" => array(new Zend_Validate_StringLength(3, 45))
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nId = $oInput->getUnescaped("id");
    $sModel = $oInput->getUnescaped("model");
    switch ($sModel) {
      case "user":
        $sClassName = "Admin_Model_User";
        break;
      case "role":
        $sClassName = "Admin_Model_UserRole";
        break;
      case "gallery":
        $sClassName = "Admin_Model_ImageGallery";
        break;
      case "slider":
        $sClassName = "Admin_Model_ImageSlider";
        break;
      case "video":
        $sClassName = "Admin_Model_Video";
        break;
      case "faq":
        $sClassName = "Admin_Model_Faq";
        break;
      case "news":
        $sClassName = "Admin_Model_News";
        break;
      case "portfolio":
        $sClassName = "Admin_Model_Portfolio";
        break;
      case "category":
        $sClassName = "Admin_Model_UserCategory";
        break;
      case "address":
        $sClassName = "Admin_Model_Address";
        break;
      case "notificationcategory":
        $sClassName = "Admin_Model_NotificationCategory";
        break;
      case "keyword":
        $sClassName = "Admin_Model_SiteSeoKeywords";
        break;
    }
    if (isset($sClassName)) {
      $oObjectReflection = new ReflectionClass($sClassName);
      $oModelInstance = $oObjectReflection->newInstanceArgs();
      if ($oModelInstance->deleteRow($nId))
        $bJson = true;
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function translateAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "text" => array("allowEmpty" => true)
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $aInputText = $oInput->getUnescaped("text");
    if (count($aInputText)) {
      $aOutputText = array();
      foreach ($aInputText as $sKey => $sValue) {
        $aOutputText["text"][$sKey] = $this->view->translate($sValue);
      }
      header("Content-type: application/json");
      echo Zend_Json::encode($aOutputText);
    }
    exit;
  }

}

?>
