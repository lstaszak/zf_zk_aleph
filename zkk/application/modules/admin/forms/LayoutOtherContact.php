<?php

class Admin_Form_LayoutOtherContact extends AppCms2_Controller_Plugin_FormLayoutAbstract
{

  public function __construct()
  {
    parent::__construct();

    $this->setName(get_class());
    $this->getElement("form_name")->setValue(get_class());

    $oContactForm = new Zend_Form_Element_Select("site_field_show_contact_form");
    $oContactForm->setLabel("Formularz kontaktowy:");
    $oContactForm->addValidator(new Zend_Validate_GreaterThan(-1));
    $oContactForm->addValidator(new Zend_Validate_LessThan(2));
    $oContactForm->setRequired(FALSE);
    $oContactForm->addMultiOptions(array(0 => "NIE", 1 => "TAK"));
    $this->addElement($oContactForm);

    $oGoogleMaps = new Zend_Form_Element_Select("site_field_show_google_maps");
    $oGoogleMaps->setLabel("Mapa Google Maps:");
    $oGoogleMaps->addValidator(new Zend_Validate_GreaterThan(-1));
    $oGoogleMaps->addValidator(new Zend_Validate_LessThan(2));
    $oGoogleMaps->setRequired(FALSE);
    $oGoogleMaps->addMultiOptions(array(0 => "NIE", 1 => "TAK"));
    $this->addElement($oGoogleMaps);

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);
  }

  public function getSiteFields()
  {
    return array_merge(parent::getStandardSiteFields(), array(
        "site_field_show_contact_form",
        "site_field_show_google_maps")
    );
  }

}

?>
