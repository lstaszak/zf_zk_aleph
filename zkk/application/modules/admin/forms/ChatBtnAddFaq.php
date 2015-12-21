<?php

class Admin_Form_ChatBtnAddFaq extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aAllFaqQuestions = array();
  private $_aAllFaqAnswers = array();

  public function __construct($options = null)
  {
    $oModelFaq = new Admin_Model_Faq();
    $oAllFaq = $oModelFaq->getAll();
    if (isset($oAllFaq)) {
      foreach ($oAllFaq as $oValue) {
        $this->_aAllFaqQuestions[$oValue->id] = stripcslashes($oValue->question);
        $this->_aAllFaqAnswers[$oValue->id] = stripcslashes($oValue->answer);
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

    $oFaqSelect = new Zend_Form_Element_Select("faq_select");
    $oFaqSelect->setLabel("Pytanie:");
    $oFaqSelect->setRequired(FALSE);
    $oFaqSelect->addMultiOptions($this->_aAllFaqQuestions);
    $this->addElement($oFaqSelect);

    $oFaqAnswer = new Zend_Form_Element_Textarea("faq_answer");
    $oFaqAnswer->setLabel("Odpowiedź:")->setFilters($this->_aFilters);
    $oFaqAnswer->setRequired(TRUE);
    $oFaqAnswer->setAttrib("class", "valid");
    $this->addElement($oFaqAnswer);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 7200));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmit = new Zend_Form_Element_Submit("submit_add_faq");
    $oSubmit->setLabel("Dodaj");
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

  public function getAllAddressAnswers()
  {
    return $this->_aAllFaqAnswers;
  }

}

?>
