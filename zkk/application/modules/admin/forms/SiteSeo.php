<?php

class Admin_Form_SiteSeo extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllSiteSeoRobots = array();
  private $_aAllSiteSeoKeywords = array();

  public function __construct($options = null)
  {
    $oModelSiteSeoRobots = new Admin_Model_SiteSeoRobots();
    $oModelSiteSeoKeywords = new Admin_Model_SiteSeoKeywords();
    $aSiteSeoRobots = $oModelSiteSeoRobots->getAll()->toArray();
    foreach ($aSiteSeoRobots as $nKey => $aValue) {
      $this->_aAllSiteSeoRobots[$aValue["id"]] = $aValue["desc"];
    }
    $aSiteSeoKeywords = $oModelSiteSeoKeywords->getAll()->toArray();
    foreach ($aSiteSeoKeywords as $nKey => $aValue) {
      $this->_aAllSiteSeoKeywords[$aValue["value"]] = $aValue["value"];
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

    $oRobots = new Zend_Form_Element_Select("site_seo_robots_id");
    $oRobots->setLabel("Tryb indeksowania przez roboty:");
    $oRobots->setRequired(FALSE);
    $oRobots->addMultiOptions($this->_aAllSiteSeoRobots);
    $oRobots->setValue(1);
    $this->addElement($oRobots);

    $oSeoTagTitle = new Zend_Form_Element_Textarea("head_title");
    $oSeoTagTitle->setLabel("Tytuł strony (meta titile):")->setFilters($this->_aFilters);
    $oSeoTagTitle->setRequired(FALSE);
    $this->addElement($oSeoTagTitle);

    $oAdditionalSeoTagKeywords = new Zend_Form_Element_Text("additional_seo_tag_keywords");
    $oAdditionalSeoTagKeywords->setLabel("Dodaj nowe słowo kluczowe:");
    $oAdditionalSeoTagKeywords->setRequired(FALSE);
    $oAdditionalSeoTagKeywords->addValidator(new Zend_Validate_StringLength(array("min" => 1, "max" => 45)));
    $oAdditionalSeoTagKeywords->setAttrib("class", "valid");
    $this->addElement($oAdditionalSeoTagKeywords);

    $oAddAdditionalSeoTagKeywords = new Zend_Form_Element_Button("add_additional_seo_tag_keywords");
    $oAddAdditionalSeoTagKeywords->setLabel("Dodaj");
    $oAddAdditionalSeoTagKeywords->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oAddAdditionalSeoTagKeywords);

    $oSeoTagKeywords = new Zend_Form_Element_Multiselect("keywords");
    $oSeoTagKeywords->addMultiOptions($this->_aAllSiteSeoKeywords);
    $oSeoTagKeywords->setRequired(FALSE);
    $oSeoTagKeywords->setLabel("Słowa kluczowe (meta keywords):");
    $oSeoTagKeywords->setAttrib("class", "chosen");
    $this->addElement($oSeoTagKeywords);

    $oSeoTagDescription = new Zend_Form_Element_Textarea("description");
    $oSeoTagDescription->setLabel("Opis strony (meta description):")->setFilters($this->_aFilters);
    $oSeoTagDescription->setRequired(FALSE);
    $this->addElement($oSeoTagDescription);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("admin");
    $oViewScript->setViewScript("_forms/siteseo.phtml");
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
