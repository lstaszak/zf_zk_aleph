<?php

class Admin_Form_AddFaq extends Zend_Form
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

    $oFaqEditId = new Zend_Form_Element_Hidden("faq_edit_id");
    $oFaqEditId->setValue(0);
    $oFaqEditId->setIgnore(FALSE)->removeDecorator("Label");
    $this->addElement($oFaqEditId);

    $oQuestion = new Zend_Form_Element_Text("question");
    $oQuestion->setLabel("Pytanie:");
    $oQuestion->addValidator(new Zend_Validate_StringLength(array("min" => 3, "max" => 255)));
    $oQuestion->setRequired(TRUE);
    $oQuestion->setAttrib("class", "valid");
    $this->addElement($oQuestion);

    $oAnswer = new Zend_Form_Element_Textarea("answer");
    $oAnswer->setLabel("Odpowiedź:");
    $oAnswer->setRequired(FALSE);
    $oAnswer->setAttrib("class", "ckeditor");
    $this->addElement($oAnswer);

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
