<?php

class Admin_Form_NavigationSubmenu extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllNavigationMenu = array();
  private $_aAllAction = array();
  private $_aAllResource = array();
  private $_aAllPrivilege = array();
  private $_aAllUserRole = array();
  private $_sClassName = "";
  private $_aAllSiteLayout;

  public function __construct($options = null)
  {
    $oModelVNavigationMenu = new Admin_Model_VNavigationMenu();
    $oModelNavigationAction = new Admin_Model_NavigationAction();
    $oModelNavigationResource = new Admin_Model_NavigationResource();
    $oModelNavigationPrivilege = new Admin_Model_NavigationPrivilege();
    $oModelUserRole = new Admin_Model_UserRole();
    $aAllNavigationMenu = $oModelVNavigationMenu->getAll()->toArray();
    if (count($aAllNavigationMenu)) {
      foreach ($aAllNavigationMenu as $aValue) {
        $this->_aAllNavigationMenu[$aValue["id"]] = $aValue["label"] . " / " . $aValue["navigation_module"];
      }
    }
    $aAllAction = $oModelNavigationAction->getAll()->toArray();
    if (count($aAllAction)) {
      foreach ($aAllAction as $aValue) {
        $this->_aAllAction[$aValue["id"]] = $aValue["value"];
      }
    }
    $aAllRosource = $oModelNavigationResource->getAll()->toArray();
    if (count($aAllRosource)) {
      foreach ($aAllRosource as $aValue) {
        $this->_aAllResource[$aValue["id"]] = $aValue["value"];
      }
    }
    $aAllPriviage = $oModelNavigationPrivilege->getAll()->toArray();
    if (count($aAllPriviage)) {
      foreach ($aAllPriviage as $aValue) {
        $this->_aAllPrivilege[$aValue["id"]] = $aValue["value"];
      }
    }
    $aAllUserRole = $oModelUserRole->getAll()->toArray();
    if (count($aAllUserRole)) {
      foreach ($aAllUserRole as $aValue) {
        $this->_aAllUserRole[$aValue["id"]] = $aValue["role_name"];
      }
    }
    $this->_sClassName = get_class();
    $this->getSiteLayout();
    parent::__construct($options);
  }

  public function getSiteLayout()
  {
    $oModelSiteLayout = new Admin_Model_SiteLayout();
    $aSiteLayout = $oModelSiteLayout->getAll();
    foreach ($aSiteLayout as $nKey => $aValue)
      $this->_aAllSiteLayout[$aValue["id"]] = $aValue["name"];
  }

  public function init()
  {
    $this->setName(strtolower(get_class()));
    $this->setMethod("post");

    $oFormName = new Zend_Form_Element_Hidden("form_name");
    $oFormName->setValue($this->_sClassName);
    $oFormName->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oFormName);

    $oLayoutName = new Zend_Form_Element_Hidden("layout_name");
    $oLayoutName->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oLayoutName);

    $oLabel = new Zend_Form_Element_Text("label");
    $oLabel->setLabel("Etykieta:")->setFilters($this->_aFilters);
    $oLabel->setRequired(TRUE)->setAttrib("class", "valid");
    $this->addElement($oLabel);

    $oMenuId = new Zend_Form_Element_Select("navigation_menu_id");
    $oMenuId->setLabel("Menu nadrzędne:");
    $oMenuId->setRequired(TRUE)->setAttrib("class", "valid");
    $oMenuId->addMultiOptions($this->_aAllNavigationMenu);
    $this->addElement($oMenuId);

    $oSiteLayout = new Zend_Form_Element_Select("site_layout_id");
    $oSiteLayout->setLabel("Szablon:");
    $oSiteLayout->setRequired(TRUE)->setAttrib("class", "valid");
    $oSiteLayout->addMultiOptions($this->_aAllSiteLayout);
    $this->addElement($oSiteLayout);

    $oActionId = new Zend_Form_Element_Select("navigation_action_id");
    $oActionId->setLabel("Akcja:");
    $oActionId->setRequired(TRUE)->setAttrib("class", "valid");
    $oActionId->addMultiOptions($this->_aAllAction);
    $this->addElement($oActionId);

    $oResourceId = new Zend_Form_Element_Select("navigation_resource_id");
    $oResourceId->setLabel("Kwalifikator zasobu:");
    $oResourceId->setRequired(TRUE)->setAttrib("class", "valid");
    $oResourceId->addMultiOptions($this->_aAllResource);
    //$this->addElement($oResourceId);

    $oPrivilegeId = new Zend_Form_Element_Select("navigation_privilege_id");
    $oPrivilegeId->setLabel("Kwalifikator:");
    $oPrivilegeId->setRequired(TRUE)->setAttrib("class", "valid");
    $oPrivilegeId->addMultiOptions($this->_aAllPrivilege);
    $this->addElement($oPrivilegeId);

    $oVisible = new Zend_Form_Element_Select("visible");
    $oVisible->setLabel("Widoczne w menu:");
    $oVisible->setRequired(TRUE)->setAttrib("class", "valid");
    $oVisible->addMultiOptions(array(1 => "Tak", 0 => "Nie"));
    $this->addElement($oVisible);

    $oPrivilege = new Zend_Form_Element_Multiselect("user_role");
    $oPrivilege->addMultiOptions($this->_aAllUserRole);
    $oPrivilege->setRequired(FALSE);
    $oPrivilege->setLabel("Dostęp do zasobu dla grupy użytkowników:");
    $oPrivilege->setAttrib("class", "multiselect");
    $this->addElement($oPrivilege);

    $oNavigationSubmenuCopy = new Zend_Form_Element_Hidden("navigation_submenu_edit_id");
    $oNavigationSubmenuCopy->setValue(0);
    $oNavigationSubmenuCopy->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oNavigationSubmenuCopy);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/navigationmenu.phtml");
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
