<?php

class Admin_Form_FileTranslate extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllFile = array();

  public function __construct($options = null)
  {
    $this->_aAllFile = array(
      "en/en_EN.php" => "en/en_EN.php",
      "pl/Zend_Validate.php" => "pl/Zend_Validate.php"
    );
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

    $oFileName = new Zend_Form_Element_Select("file_name");
    $oFileName->setLabel("Plik językowy:");
    $oFileName->setRequired(TRUE);
    $oFileName->addValidator(new Zend_Validate_InArray(array_keys($this->_aAllFile)));
    $oFileName->addMultiOptions($this->_aAllFile);
    $oFileName->setAttrib("class", "valid");
    $this->addElement($oFileName);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Wczytaj plik");
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
