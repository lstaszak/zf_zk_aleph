<?php

class Admin_Form_ChooseSite extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllLang = array();
  private $_aAllMenu = array();

  public function __construct($options = null)
  {
    $oModelVNavigationMenu = new Admin_Model_VNavigationMenu();
    $oModelVNavigationSubmenu = new Admin_Model_VNavigationSubmenu();
    $oModelVNavigationSubsubmenu = new Admin_Model_VNavigationSubsubmenu();
    $oModelSite = new Admin_Model_Site();
    $this->_aAllLang = array("lang_pl" => "język polski", "lang_en" => "język angielski");
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
      if ($oModelSite->findSiteId($aValue["id"])) {
        $this->_aAllMenu[$aValue["id"]] = $aValue["label"];
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
    $oPostStep->addValidator(new Zend_Validate_GreaterThan(-1));
    $oPostStep->addValidator(new Zend_Validate_LessThan(1));
    $oPostStep->setValue(0);
    $oPostStep->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oPostStep);

    $oTranslation = new Zend_Form_Element_Select("translation");
    $oTranslation->setLabel("Tłumaczenie:");
    $oTranslation->setRequired(TRUE)->setAttrib("class", "valid");
    $oTranslation->addMultiOptions($this->_aAllLang);
    $this->addElement($oTranslation);

    $oMenuId = new Zend_Form_Element_Select("menu_id");
    $oMenuId->setLabel("Menu:");
    $oMenuId->setRequired(TRUE)->setAttrib("class", "valid");
    $oMenuId->addMultiOptions($this->_aAllMenu);
    $this->addElement($oMenuId);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "choose_site_submit");
    $oSubmit->setLabel("Dalej");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/choosesite.phtml");
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
