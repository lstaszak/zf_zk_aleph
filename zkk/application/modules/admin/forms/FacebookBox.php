<?php

class Admin_Form_FacebookBox extends Zend_Form
{

  private $_aFilters = array("StringTrim");

  public function __construct($options = null)
  {
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

    $oSelectShow = new Zend_Form_Element_Select("show");
    $oSelectShow->setLabel("Pokaż na stronie:");
    $oSelectShow->setRequired(TRUE);
    $oSelectShow->addMultiOptions(array(1 => "TAK", 0 => "NIE"));
    $this->addElement($oSelectShow);

    $oHref = new Zend_Form_Element_Text("href");
    $oHref->setLabel("Adres strony Facebook:");
    $oHref->setRequired(TRUE);
    $this->addElement($oHref);

    $oWidth = new Zend_Form_Element_Text("width");
    $oWidth->setLabel("Szerokość:");
    $oWidth->setRequired(TRUE);
    $this->addElement($oWidth);

    $oHeader = new Zend_Form_Element_Select("header");
    $oHeader->setLabel("Pokaż pasek nagłówka:");
    $oHeader->setRequired(TRUE);
    $oHeader->addMultiOptions(array("true" => "TAK", "false" => "NIE"));
    $this->addElement($oHeader);

    $oStream = new Zend_Form_Element_Select("stream");
    $oStream->setLabel("Pokaż strumień:");
    $oStream->setRequired(TRUE);
    $oStream->addMultiOptions(array("true" => "TAK", "false" => "NIE"));
    $this->addElement($oStream);

    $oShowFaces = new Zend_Form_Element_Select("show_faces");
    $oShowFaces->setLabel("Pokaż zdjęcia użytkowników:");
    $oShowFaces->addMultiOptions(array("true" => "TAK", "false" => "NIE"));
    $this->addElement($oShowFaces);

    $oColorScheme = new Zend_Form_Element_Select("color_scheme");
    $oColorScheme->setLabel("Kolor:");
    $oColorScheme->addMultiOptions(array("light" => "light", "dark" => "dark"));
    $this->addElement($oColorScheme);

    $oBorderColor = new Zend_Form_Element_Text("border_color");
    $oBorderColor->setLabel("Kolor obramowania:");
    $oBorderColor->setRequired(TRUE);
    $this->addElement($oBorderColor);

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

}

?>
