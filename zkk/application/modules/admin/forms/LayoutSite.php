<?php

class Admin_Form_LayoutSite extends AppCms2_Controller_Plugin_FormLayoutAbstract
{

  public function __construct()
  {
    parent::__construct();

    $this->setName(get_class());
    $this->getElement("form_name")->setValue(get_class());

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);
  }

  public function getSiteFields()
  {
    return array_merge(parent::getStandardSiteFields(), array());
  }

}

?>
