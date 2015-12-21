<?php

class Admin_Form_SiteLayout extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllMenu = array();
  private $_aAllSiteLayout = array();

  public function __construct($options = null)
  {
    $oModelVNavigationMenu = new Admin_Model_VNavigationMenu();
    $oModelVNavigationSubmenu = new Admin_Model_VNavigationSubmenu();
    $oModelVNavigationSubsubmenu = new Admin_Model_VNavigationSubsubmenu();
    $oModelSiteLayout = new Admin_Model_SiteLayout();
    $sModule = "default";
    $aMenu = $oModelVNavigationMenu->getSiteMenu($sModule)->toArray();
    foreach ($aMenu as $nKey => $aMenuValue) {
      $sMenuLabel = $aMenuValue["label"];
      $aSubmenu = null;
      $aSubmenu = $oModelVNavigationSubmenu->getSiteMenu($aMenuValue["id"])->toArray();
      if ($aSubmenu) {
        foreach ($aSubmenu as $aSubmenuValue) {
          $sSubmenuLabel = $aSubmenuValue["label"];
          $aSubmenuValue["label"] = $sMenuLabel . " / " . $sSubmenuLabel;
          array_push($aMenu, $aSubmenuValue);
          $aSubsubmenu = $oModelVNavigationSubsubmenu->getSiteMenu($aSubmenuValue["id"])->toArray();
          if ($aSubsubmenu) {
            foreach ($aSubsubmenu as $aSubsubmenuValue) {
              $sSubsubmenuLabel = $aSubsubmenuValue["label"];
              $aSubsubmenuValue["label"] = $sMenuLabel . " / " . $sSubmenuLabel . " / " . $sSubsubmenuLabel;
              array_push($aMenu, $aSubsubmenuValue);
            }
          }
        }
      }
    }
    foreach ($aMenu as $nKey => $aValue) {
      $this->_aAllMenu[$aValue["id"]] = $aValue["label"];
    }
    $aSiteLayout = $oModelSiteLayout->getAll();
    foreach ($aSiteLayout as $nKey => $aValue)
      $this->_aAllSiteLayout[$aValue["id"]] = $aValue["name"];
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

    $oLayoutName = new Zend_Form_Element_Hidden("layout_name");
    $oLayoutName->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oLayoutName);

    $oMenuId = new Zend_Form_Element_Select("menu_id");
    $oMenuId->setLabel("Menu:");
    $oMenuId->setRequired(TRUE);
    $oMenuId->addMultiOptions($this->_aAllMenu);
    $this->addElement($oMenuId);

    $oSiteLayout = new Zend_Form_Element_Select("site_layout_id");
    $oSiteLayout->setLabel("Szablon:");
    $oSiteLayout->setRequired(TRUE);
    $oSiteLayout->addMultiOptions($this->_aAllSiteLayout);
    $this->addElement($oSiteLayout);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/sitelayout.phtml");
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
