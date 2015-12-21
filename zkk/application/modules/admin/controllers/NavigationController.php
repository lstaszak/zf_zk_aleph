<?php

class Admin_NavigationController extends Zend_Controller_Action
{

  private $_oAuth;
  private $_nUserId = null;
  private $_nRoleName = null;
  private $_sSiteUrl = null;
  private $_sFirstName = null;
  private $_sLastName = null;

  public function preDispatch()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_nRoleName = $this->_oAuth->getStorage()->read()->role_name;
      $this->_sSiteUrl = $this->_oAuth->getStorage()->read()->site_url;
      $this->_sFirstName = $this->_oAuth->getStorage()->read()->first_name;
      $this->_sLastName = $this->_oAuth->getStorage()->read()->last_name;
    }
    if ($this->_oAuth->hasIdentity() && ($this->_sSiteUrl != str_replace("/", "", $this->view->sBaseUrl))) {
      $this->_oAuth->clearIdentity();
    }
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("admin/layout");
  }

  public function indexAction()
  {
    $this->_redirect("/admin/navigation/menu");
  }

  public function elementAction()
  {
    $oFormNavigationElement = new Admin_Form_NavigationElement();
    $oModelNavigationModule = new Admin_Model_NavigationModule();
    $oModelNavigationController = new Admin_Model_NavigationController();
    $oModelNavigationAction = new Admin_Model_NavigationAction();
    $oModelNavigationResource = new Admin_Model_NavigationResource();
    $oModelNavigationPrivilege = new Admin_Model_NavigationPrivilege();
    $aPostData = array();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormNavigationElement->isValid($aPostData)) {
        $nNavigationElementId = $aPostData["navigation_element_id"];
        $nNavigationElementEditId = $aPostData["navigation_element_edit_id"];
        $sValue = $aPostData["value"];
        switch ($nNavigationElementId) {
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
        if ($nNavigationElementEditId != 0) {
          $oModelNavigationElement->edit($nNavigationElementEditId, strtolower($sValue));
          $oFormNavigationElement->clearForm();
        } else {
          $oModelNavigationElement->add(strtolower($sValue));
          $oFormNavigationElement->clearForm();
        }
      }
    }
    $this->view->aAllModule = $oModelNavigationModule->getAll()->toArray();
    $this->view->aAllController = $oModelNavigationController->getAll()->toArray();
    $this->view->aAllAction = $oModelNavigationAction->getAll()->toArray();
    $this->view->aAllResource = $oModelNavigationResource->getAll()->toArray();
    $this->view->aAllPrivilege = $oModelNavigationPrivilege->getAll()->toArray();
    $this->view->oFormNavigationElement = $oFormNavigationElement;
  }

  public function menuAction()
  {
    $oModelNavigationResource = new Admin_Model_NavigationResource();
    $oModelNavigationResource->deleteNotUse();
    $oFormNavigationMenu = new Admin_Form_NavigationMenu();
    $oModelNavigationModule = new Admin_Model_NavigationModule();
    $oModelNavigationMenu = new Admin_Model_NavigationMenu();
    $oModelNavigationOptionUserRole = new Admin_Model_NavigationOptionUserRole();
    $oModelVSiteLayout = new Admin_Model_VSiteLayout();
    $oModelSite = new Admin_Model_Site();
    $oSite = new AppCms2_Controller_Plugin_FormLayoutAbstract();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormNavigationMenu->isValid($aPostData)) {
        $nNavigationMenuId = (int)$aPostData["navigation_menu_edit_id"];
        $aUserRole = $aPostData["user_role"];
        $aPostData["image_id"] = $aPostData["image_id"] != "0" ? $aPostData["image_id"] : null;
        if ($nNavigationMenuId != 0) {
          $nNavigationOptionId = $oModelNavigationMenu->edit($nNavigationMenuId, $aPostData);
          $oModelNavigationOptionUserRole->deleteUserRole($nNavigationOptionId);
          if (isset($aUserRole) && isset($nNavigationOptionId)) {
            foreach ($aUserRole as $nRoleId) {
              $oModelNavigationOptionUserRole->add($nNavigationOptionId, $nRoleId);
            }
          }
          $sLayoutName = $oModelVSiteLayout->getSiteLayoutId($nNavigationMenuId)->layout_name;
          if ((int)$aPostData["navigation_module_id"] == 2 && $sLayoutName != $aPostData["layout_name"]) {
            $aPostData["menu_id"] = $nNavigationMenuId;
            $sClassName = $oSite->getClassName($aPostData["layout_name"]);
            if (isset($sClassName)) {
              $oObjectReflection = new ReflectionClass($sClassName);
              $oFormInstance = $oObjectReflection->newInstanceArgs();
              $aSiteFileds = $oFormInstance->getSiteFields();
              $oModelSite->newSite($aPostData, $aSiteFileds);
            }
          }
          $oFormNavigationMenu->clearForm();
        } else {
          $nNavigationOptionId = $oModelNavigationMenu->add($aPostData);
          if (isset($aUserRole) && isset($nNavigationOptionId)) {
            foreach ($aUserRole as $nRoleId) {
              $oModelNavigationOptionUserRole->add($nNavigationOptionId, $nRoleId);
            }
          }
          if ((int)$aPostData["navigation_module_id"] == 2) {
            $nNavigationMenuId = $oModelNavigationMenu->findMenuId($nNavigationOptionId);
            $aPostData["menu_id"] = $nNavigationMenuId;
            $sClassName = $oSite->getClassName($aPostData["layout_name"]);
            if (isset($sClassName)) {
              $oObjectReflection = new ReflectionClass($sClassName);
              $oFormInstance = $oObjectReflection->newInstanceArgs();
              $aSiteFileds = $oFormInstance->getSiteFields();
              $oModelSite->newSite($aPostData, $aSiteFileds);
            }
          }
          $oFormNavigationMenu->clearForm();
        }
      }
    }
    $aAllModule = $oModelNavigationModule->getAll()->toArray();
    if (count($aAllModule)) {
      foreach ($aAllModule as $aValue) {
        $aNavigation[$aValue["value"]] = $this->prepareNavigation($aValue["value"]);
      }
    }
    $this->view->oFormNavigationElement = $oFormNavigationMenu;
    $this->view->aNavigation = $aNavigation;
  }

  public function submenuAction()
  {
    $oFormNavigationSubmenu = new Admin_Form_NavigationSubmenu();
    $oModelNavigationModule = new Admin_Model_NavigationModule();
    $oModelNavigationOption = new Admin_Model_NavigationOption();
    $oModelNavigationSubmenu = new Admin_Model_NavigationSubmenu();
    $oModelNavigationOptionUserRole = new Admin_Model_NavigationOptionUserRole();
    $oModelVSiteLayout = new Admin_Model_VSiteLayout();
    $oModelSite = new Admin_Model_Site();
    $oSite = new AppCms2_Controller_Plugin_FormLayoutAbstract();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormNavigationSubmenu->isValid($aPostData)) {
        $nNavigationSubmenuId = (int)$aPostData["navigation_submenu_edit_id"];
        $aUserRole = $aPostData["user_role"];
        if ($nNavigationSubmenuId != 0) {
          $nNavigationOptionId = $oModelNavigationSubmenu->edit($nNavigationSubmenuId, $aPostData);
          $oModelNavigationOptionUserRole->deleteUserRole($nNavigationOptionId);
          if (isset($aUserRole) && isset($nNavigationOptionId)) {
            foreach ($aUserRole as $nRoleId) {
              $oModelNavigationOptionUserRole->add($nNavigationOptionId, $nRoleId);
            }
          }
          $oRow = $oModelNavigationOption->getRow($nNavigationOptionId);
          $sLayoutName = $oModelVSiteLayout->getSiteLayoutId($nNavigationSubmenuId)->layout_name;
          if ($oRow->navigation_module_id == 2 && $sLayoutName != $aPostData["layout_name"]) {
            $aPostData["menu_id"] = $nNavigationSubmenuId;
            $sClassName = $oSite->getClassName($aPostData["layout_name"]);
            if (isset($sClassName)) {
              $oObjectReflection = new ReflectionClass($sClassName);
              $oFormInstance = $oObjectReflection->newInstanceArgs();
              $aSiteFileds = $oFormInstance->getSiteFields();
              $oModelSite->newSite($aPostData, $aSiteFileds);
            }
          }
          $oFormNavigationSubmenu->clearForm();
        } else {
          $nNavigationOptionId = $oModelNavigationSubmenu->add($aPostData);
          if (isset($aUserRole) && isset($nNavigationOptionId)) {
            foreach ($aUserRole as $nRoleId) {
              $oModelNavigationOptionUserRole->add($nNavigationOptionId, $nRoleId);
            }
          }
          $oRow = $oModelNavigationOption->getRow($nNavigationOptionId);
          if ($oRow->navigation_module_id == 2) {
            $nNavigationSubmenuId = $oModelNavigationSubmenu->findSubmenuId($nNavigationOptionId);
            $aPostData["menu_id"] = $nNavigationSubmenuId;
            $sClassName = $oSite->getClassName($aPostData["layout_name"]);
            if (isset($sClassName)) {
              $oObjectReflection = new ReflectionClass($sClassName);
              $oFormInstance = $oObjectReflection->newInstanceArgs();
              $aSiteFileds = $oFormInstance->getSiteFields();
              $oModelSite->newSite($aPostData, $aSiteFileds);
            }
          }
          $oFormNavigationSubmenu->clearForm();
        }
      }
    }
    $aAllModule = $oModelNavigationModule->getAll()->toArray();
    if (count($aAllModule)) {
      foreach ($aAllModule as $aValue) {
        $aNavigation[$aValue["value"]] = $this->prepareNavigation($aValue["value"]);
      }
    }
    $this->view->oFormNavigationElement = $oFormNavigationSubmenu;
    $this->view->aNavigation = $aNavigation;
  }

  public function subsubmenuAction()
  {
    $oFormNavigationSubsubmenu = new Admin_Form_NavigationSubsubmenu();
    $oModelNavigationModule = new Admin_Model_NavigationModule();
    $oModelNavigationOption = new Admin_Model_NavigationOption();
    $oModelNavigationSubsubmenu = new Admin_Model_NavigationSubsubmenu();
    $oModelNavigationOptionUserRole = new Admin_Model_NavigationOptionUserRole();
    $oModelVSiteLayout = new Admin_Model_VSiteLayout();
    $oModelSite = new Admin_Model_Site();
    $oSite = new AppCms2_Controller_Plugin_FormLayoutAbstract();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormNavigationSubsubmenu->isValid($aPostData)) {
        $nNavigationSubsubmenuId = (int)$aPostData["navigation_subsubmenu_edit_id"];
        $aUserRole = $aPostData["user_role"];
        if ($nNavigationSubsubmenuId != 0) {
          $nNavigationOptionId = $oModelNavigationSubsubmenu->edit($nNavigationSubsubmenuId, $aPostData);
          $oModelNavigationOptionUserRole->deleteUserRole($nNavigationOptionId);
          if (isset($aUserRole) && isset($nNavigationOptionId)) {
            foreach ($aUserRole as $nRoleId) {
              $oModelNavigationOptionUserRole->add($nNavigationOptionId, $nRoleId);
            }
          }
          $oRow = $oModelNavigationOption->getRow($nNavigationOptionId);
          $sLayoutName = $oModelVSiteLayout->getSiteLayoutId($nNavigationSubsubmenuId)->layout_name;
          if ($oRow->navigation_module_id == 2 && $sLayoutName != $aPostData["layout_name"]) {
            $aPostData["menu_id"] = $nNavigationSubsubmenuId;
            $sClassName = $oSite->getClassName($aPostData["layout_name"]);
            if (isset($sClassName)) {
              $oObjectReflection = new ReflectionClass($sClassName);
              $oFormInstance = $oObjectReflection->newInstanceArgs();
              $aSiteFileds = $oFormInstance->getSiteFields();
              $oModelSite->newSite($aPostData, $aSiteFileds);
            }
          }
          $oFormNavigationSubsubmenu->clearForm();
        } else {
          $nNavigationOptionId = $oModelNavigationSubsubmenu->add($aPostData);
          if (isset($aUserRole) && isset($nNavigationOptionId)) {
            foreach ($aUserRole as $nRoleId) {
              $oModelNavigationOptionUserRole->add($nNavigationOptionId, $nRoleId);
            }
          }
          $oRow = $oModelNavigationOption->getRow($nNavigationOptionId);
          if ($oRow->navigation_module_id == 2) {
            $nNavigationSubmenuId = $oModelNavigationSubsubmenu->findSubsubmenuId($nNavigationOptionId);
            $aPostData["menu_id"] = $nNavigationSubmenuId;
            $sClassName = $oSite->getClassName($aPostData["layout_name"]);
            if (isset($sClassName)) {
              $oObjectReflection = new ReflectionClass($sClassName);
              $oFormInstance = $oObjectReflection->newInstanceArgs();
              $aSiteFileds = $oFormInstance->getSiteFields();
              $oModelSite->newSite($aPostData, $aSiteFileds);
            }
          }
          $oFormNavigationSubsubmenu->clearForm();
        }
      }
    }
    $aAllModule = $oModelNavigationModule->getAll()->toArray();
    if (count($aAllModule)) {
      foreach ($aAllModule as $aValue) {
        $aNavigation[$aValue["value"]] = $this->prepareNavigation($aValue["value"]);
      }
    }
    $this->view->oFormNavigationElement = $oFormNavigationSubsubmenu;
    $this->view->aNavigation = $aNavigation;
  }

  public function orderAction()
  {
    $oFormNavigationOrder = new Admin_Form_NavigationOrder();
    $oModelNavigationModule = new Admin_Model_NavigationModule();
    $aAllModule = $oModelNavigationModule->getAll()->toArray();
    if (count($aAllModule)) {
      foreach ($aAllModule as $aValue) {
        $aNavigation[$aValue["value"]] = $this->prepareNavigation($aValue["value"]);
      }
    }
    $this->view->oFormNavigationOrder = $oFormNavigationOrder;
    $this->view->aNavigation = $aNavigation;
  }

  public function prepareNavigation($sModule)
  {
    $oModelVNavigationMenu = new Admin_Model_VNavigationMenu();
    $oModelVNavigationSubmenu = new Admin_Model_VNavigationSubmenu();
    $oModelVNavigationSubsubmenu = new Admin_Model_VNavigationSubsubmenu();
    $aMenu = null;
    $aMenu = $oModelVNavigationMenu->getConfig($sModule)->toArray();
    if (isset($aMenu)) {
      foreach ($aMenu as $nMenuKey => $aMenuValue) {
        $aSubmenu = null;
        $aSubmenu = $oModelVNavigationSubmenu->getConfig($aMenuValue["id"])->toArray();
        if (isset($aSubmenu)) {
          foreach ($aSubmenu as $nSubmenuKey => $aSubmenuValue) {
            $aSubsubmenu = null;
            $aSubsubmenu = $oModelVNavigationSubsubmenu->getConfig($aSubmenuValue["id"])->toArray();
            if (isset($aSubsubmenu)) {
              $aSubmenu[$nSubmenuKey]["pages"] = $aSubsubmenu;
            }
          }
          $aMenu[$nMenuKey]["pages"] = $aSubmenu;
        }
      }
    }
    return $aMenu;
  }

  public function deletenavigationelementAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits()),
      "element" => array(new Zend_Validate_StringLength(4, 45))
    );
    $bJson = false;
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nId = $oInput->getUnescaped("id");
    $sElement = $oInput->getUnescaped("element");
    $oModelNavigationModule = new Admin_Model_NavigationModule();
    $oModelNavigationController = new Admin_Model_NavigationController();
    $oModelNavigationAction = new Admin_Model_NavigationAction();
    $oModelNavigationResource = new Admin_Model_NavigationResource();
    $oModelNavigationPrivilege = new Admin_Model_NavigationPrivilege();
    switch ($sElement) {
      case "module":
        $oModelNavigationElement = $oModelNavigationModule;
        break;
      case "controller":
        $oModelNavigationElement = $oModelNavigationController;
        break;
      case "action":
        $oModelNavigationElement = $oModelNavigationAction;
        break;
      case "resource":
        $oModelNavigationElement = $oModelNavigationResource;
        break;
      case "privilege":
        $oModelNavigationElement = $oModelNavigationPrivilege;
        break;
    }
    $bJson = $oModelNavigationElement->deleteElement($nId);
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function deletenavigationmenuAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits()),
      "element" => array(new Zend_Validate_StringLength(4, 45))
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nNavigationMenuId = $oInput->getUnescaped("id");
    $oModelNavigationMenu = new Admin_Model_NavigationMenu();
    $oModelNavigationMenu->deleteRow($nNavigationMenuId);
    header("Content-type: application/json");
    echo Zend_Json::encode(true);
    exit;
  }

  public function deletenavigationsubmenuAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits()),
      "element" => array(new Zend_Validate_StringLength(4, 45))
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nNavigationSubmenuId = $oInput->getUnescaped("id");
    $oModelNavigationSubmenu = new Admin_Model_NavigationSubmenu();
    $oModelNavigationSubmenu->deleteRow($nNavigationSubmenuId);
    header("Content-type: application/json");
    echo Zend_Json::encode(true);
    exit;
  }

  public function deletenavigationsubsubmenuAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits()),
      "element" => array(new Zend_Validate_StringLength(4, 45))
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nNavigationSubsubmenuId = $oInput->getUnescaped("id");
    $oModelNavigationSubsubmenu = new Admin_Model_NavigationSubsubmenu();
    $oModelNavigationSubsubmenu->deleteRow($nNavigationSubsubmenuId);
    header("Content-type: application/json");
    echo Zend_Json::encode(true);
    exit;
  }

  public function getnavigationoptionuserroleAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits()),
      "element" => array(new Zend_Validate_StringLength(4, 45))
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    if ($oInput->getUnescaped("element") == "menu") {
      $nNavigationMenuId = $oInput->getUnescaped("id");
      $oModelNavigationMenu = new Admin_Model_NavigationMenu();
      $nNavigationOptionId = $oModelNavigationMenu->findOptionId($nNavigationMenuId);
    } else if ($oInput->getUnescaped("element") == "submenu") {
      $nNavigationSubmenuId = $oInput->getUnescaped("id");
      $oModelNavigationSubmenu = new Admin_Model_NavigationSubmenu();
      $nNavigationOptionId = $oModelNavigationSubmenu->findOptionId($nNavigationSubmenuId);
    } else if ($oInput->getUnescaped("element") == "subsubmenu") {
      $nNavigationSubsubmenuId = $oInput->getUnescaped("id");
      $oModelNavigationSubsubmenu = new Admin_Model_NavigationSubsubmenu();
      $nNavigationOptionId = $oModelNavigationSubsubmenu->findOptionId($nNavigationSubsubmenuId);
    }
    $oModelNavigationOptionUserRole = new Admin_Model_NavigationOptionUserRole();
    $bJson = $oModelNavigationOptionUserRole->getAll($nNavigationOptionId)->toArray();
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function getsitelayoutidAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oModelVSiteLayout = new Admin_Model_VSiteLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits())
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nNavigationMenuId = $oInput->getUnescaped("id");
    if ($nNavigationMenuId) {
      $oSiteLayoutId = $oModelVSiteLayout->getSiteLayoutId($nNavigationMenuId);
      if (isset($oSiteLayoutId))
        $bJson = $oSiteLayoutId->toArray();
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function setnavigationorderAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $sElement = $this->_getParam("element");
    $aOrder = $this->_getParam("order");
    if ($sElement == "menu") {
      $oModelNavigationElement = new Admin_Model_NavigationMenu();
    } else if ($sElement == "submenu") {
      $oModelNavigationElement = new Admin_Model_NavigationSubmenu();
    } else if ($sElement == "subsubmenu") {
      $oModelNavigationElement = new Admin_Model_NavigationSubsubmenu();
    }
    if (is_array($aOrder)) {
      foreach ($aOrder as $nId => $nOrder) {
        $oModelNavigationElement->setOrder((int)$nId, (int)$nOrder);
      }
    }
    $bJson = true;
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

}

?>
