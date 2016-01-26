<?php

class AppCms2_Validate_NavigationElement extends Zend_Validate_Abstract
{

  const IS_EXIST = "notMatch";

  protected $_messageTemplates = array(
    self::IS_EXIST => "Element o takiej wartości już istnieje"
  );

  public function isValid($sValue, $mContext = null)
  {
    $sValue = (string)$sValue;
    if (is_array($mContext)) {
      if (isset($sValue)) {
        $oModelNavigationModule = new Admin_Model_NavigationModule();
        $oModelNavigationController = new Admin_Model_NavigationController();
        $oModelNavigationAction = new Admin_Model_NavigationAction();
        $oModelNavigationResource = new Admin_Model_NavigationResource();
        $oModelNavigationPrivilege = new Admin_Model_NavigationPrivilege();
        switch ($mContext["navigation_element_id"]) {
          case 0:
            $oModelNavigationElement = $oModelNavigationModule;
            break;
          case 1:
            $oModelNavigationElement = $oModelNavigationController;
            break;
          case 2:
            $oModelNavigationElement = $oModelNavigationAction;
            break;
          case 3:
            $oModelNavigationElement = $oModelNavigationResource;
            break;
          case 4:
            $oModelNavigationElement = $oModelNavigationPrivilege;
            break;
        }

        $nElementId = $oModelNavigationElement->check($sValue);
        if (!is_numeric($nElementId))
          return true;
      }
    }

    $this->_error(self::IS_EXIST);
    return false;
  }

}
