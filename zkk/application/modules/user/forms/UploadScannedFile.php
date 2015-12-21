<?php

class User_Form_UploadScannedFile extends Zend_Form
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
    $this->setAction("user/files/addfile");

    $oFormName = new Zend_Form_Element_Hidden("form_name");
    $oFormName->setValue(get_class());
    $oFormName->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oFormName);

    $oOrderId = new Zend_Form_Element_Text("id");
    $oOrderId->setLabel("Numer zamówienia:");
    $oOrderId->addValidator(new Zend_Validate_Digits());
    $oOrderId->setRequired(TRUE);
    $this->addElement($oOrderId);

    $oScannedFile = new Zend_Form_Element_File("files");
    $oScannedFile->setLabel("Plik:");
    $oScannedFile->setRequired(TRUE);
    $this->addElement($oScannedFile);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("user");
    $oViewScript->setViewScript("_forms/uploadscannedfile.phtml");
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
