<?php

class Admin_Form_Portfolio extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllLang = array();
  private $_aAllImage = array();
  private $_aAllGallery = array();

  public function __construct($options = null)
  {
    $this->_aAllLang = array("lang_pl" => "język polski", "lang_en" => "język angielski");
    $oModelImage = new Admin_Model_Image();
    $oModelImageGallery = new Admin_Model_ImageGallery();
    $aImage = $oModelImage->getAll()->toArray();
    $this->_aAllImage[0] = "-";
    foreach ($aImage as $nKey => $aValue) {
      $this->_aAllImage[$aValue["id"]] = $aValue["user_name"];
    }
    $aGallery = $oModelImageGallery->getAll()->toArray();
    $this->_aAllGallery[0] = "-";
    foreach ($aGallery as $nKey => $aValue) {
      $this->_aAllGallery[$aValue["id"]] = $aValue["name"];
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

    $oPortfolioEditId = new Zend_Form_Element_Hidden("portfolio_edit_id");
    $oPortfolioEditId->setValue(0);
    $oPortfolioEditId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oPortfolioEditId);

    $oTranslation = new Zend_Form_Element_Select("translation");
    $oTranslation->setLabel("Tłumaczenie:");
    $oTranslation->setRequired(TRUE)->setAttrib("class", "valid");
    $oTranslation->addMultiOptions($this->_aAllLang);
    $this->addElement($oTranslation);

    $oImagePreview = new Zend_Form_Element_Hidden("image_preview");
    $oImagePreview->setValue(0);
    $oImagePreview->setRequired(FALSE)->removeDecorator("Label");
    $this->addElement($oImagePreview);

    $oImageGalleryPreview = new Zend_Form_Element_Hidden("image_gallery_preview");
    $oImageGalleryPreview->setValue(0);
    $oImageGalleryPreview->setRequired(FALSE)->removeDecorator("Label");
    $this->addElement($oImageGalleryPreview);

    $oImageId = new Zend_Form_Element_Select("image_id");
    $oImageId->setLabel("Zdjęcie:")->setFilters($this->_aFilters);
    $oImageId->setRequired(FALSE);
    $oImageId->addMultiOptions($this->_aAllImage);
    $this->addElement($oImageId);

    $oImageGalleryId = new Zend_Form_Element_Select("image_gallery_id");
    $oImageGalleryId->setLabel("Galeria:");
    $oImageGalleryId->setRequired(TRUE);
    $oImageGalleryId->addMultiOptions($this->_aAllGallery);
    $oImageGalleryId->setAttrib("class", "valid");
    $this->addElement($oImageGalleryId);

    $oCreatedDate = new Zend_Form_Element_Text("created_date");
    $oCreatedDate->setLabel("Data utworzenia:");
    $oCreatedDate->setRequired(TRUE);
    $oCreatedDate->setAttrib("class", "valid");
    $this->addElement($oCreatedDate);

    $oName = new Zend_Form_Element_Text("portfolio_title");
    $oName->setLabel("Tytuł:");
    $oName->addValidator(new Zend_Validate_StringLength(array("min" => 3, "max" => 255)));
    $oName->setRequired(TRUE)->setAttrib("class", "valid");
    $this->addElement($oName);

    $oContent = new Zend_Form_Element_Textarea("portfolio_content");
    $oContent->setLabel("Treść:");
    $oContent->setRequired(FALSE);
    $oContent->setAttrib("class", "ckeditor");
    $this->addElement($oContent);

    $oAdditionalCategoryTag = new Zend_Form_Element_Text("additional_category_tag");
    $oAdditionalCategoryTag->setLabel("Dodaj nowe tagi:");
    $oAdditionalCategoryTag->setRequired(FALSE);
    $oAdditionalCategoryTag->addValidator(new Zend_Validate_StringLength(array("min" => 1, "max" => 45)));
    $oAdditionalCategoryTag->setAttrib("class", "valid");
    $this->addElement($oAdditionalCategoryTag);

    $oAddAdditionalCategoryTag = new Zend_Form_Element_Button("add_additional_category_tag");
    $oAddAdditionalCategoryTag->setLabel("Dodaj");
    $oAddAdditionalCategoryTag->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oAddAdditionalCategoryTag);

    $oCategoryTag = new Zend_Form_Element_Multiselect("category_tag");
    $oCategoryTag->addMultiOptions(array());
    $oCategoryTag->setRequired(FALSE);
    $oCategoryTag->setLabel("Tagi:");
    $oCategoryTag->setAttrib("class", "chosen");
    $this->addElement($oCategoryTag);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
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
