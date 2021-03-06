<?php

class Admin_Form_LayoutContact extends AppCms2_Controller_Plugin_FormLayoutAbstract
{

  public function __construct()
  {
    parent::__construct();

    $this->setName(get_class());
    $this->getElement("form_name")->setValue(get_class());

    $oCompanyName = new Zend_Form_Element_Text("site_field_company_name");
    $oCompanyName->setLabel("Nazwa firmy:");
    $oCompanyName->setRequired(FALSE);
    $this->addElement($oCompanyName);

    $oName = new Zend_Form_Element_Text("site_field_name");
    $oName->setLabel("Imię i nazwisko:");
    $oName->setRequired(FALSE);
    $this->addElement($oName);

    $oPhoneNumber = new Zend_Form_Element_Text("site_field_phone_number");
    $oPhoneNumber->setLabel("Numer telefonu:");
    $oPhoneNumber->addValidator(new AppCms2_Validate_CellPhone());
    $oPhoneNumber->setRequired(FALSE);
    $this->addElement($oPhoneNumber);

    $oEmailAddress = new Zend_Form_Element_Text("site_field_email_address");
    $oEmailAddress->setLabel("Adres e-mail:");
    $oEmailAddress->addValidator(new Zend_Validate_EmailAddress());
    $oEmailAddress->setRequired(FALSE);
    $this->addElement($oEmailAddress);

    $oSkype = new Zend_Form_Element_Text("site_field_skype");
    $oSkype->setLabel("Skype:");
    $oSkype->setRequired(FALSE);
    $this->addElement($oSkype);

    $oAddress1 = new Zend_Form_Element_Text("site_field_address1");
    $oAddress1->setLabel("Adres 1:");
    $oAddress1->setRequired(FALSE);
    $this->addElement($oAddress1);

    $oAddress2 = new Zend_Form_Element_Text("site_field_address2");
    $oAddress2->setLabel("Adres 2:");
    $oAddress2->setRequired(FALSE);
    $this->addElement($oAddress2);

    $oTaxNo = new Zend_Form_Element_Text("site_field_tax_no");
    $oTaxNo->setLabel("NIP:");
    $oTaxNo->setRequired(FALSE);
    $this->addElement($oTaxNo);

    $oRegon = new Zend_Form_Element_Text("site_field_regon");
    $oRegon->setLabel("REGON:");
    $oRegon->setRequired(FALSE);
    $this->addElement($oRegon);

    $oWebSite = new Zend_Form_Element_Text("site_field_web_site");
    $oWebSite->setLabel("Strona WWW:");
    $oWebSite->setRequired(FALSE);
    $this->addElement($oWebSite);

    $oGoldenLine = new Zend_Form_Element_Text("site_field_golden_line");
    $oGoldenLine->setLabel("Profil GoldenLine:");
    $oGoldenLine->setRequired(FALSE);
    $this->addElement($oGoldenLine);

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
        "site_field_company_name",
        "site_field_name",
        "site_field_phone_number",
        "site_field_email_address",
        "site_field_web_site",
        "site_field_address1",
        "site_field_address2",
        "site_field_tax_no",
        "site_field_regon",
        "site_field_skype",
        "site_field_golden_line",
        "site_field_show_contact_form",
        "site_field_show_google_maps")
    );
  }

}

?>
