<?php

class Admin_Form_ShowOnline extends Zend_Form
{

  private $_aFilters = array("StringTrim");
  private $_aChatUserRecipients = array();
  private $_aOnlineChatUserRecipients = array();

  public function __construct($options = null)
  {
    $oModelUser = new Admin_Model_User();
    $oChatUserRecipients = $oModelUser->findAskRecipients();
    if (isset($oChatUserRecipients)):
      foreach ($oChatUserRecipients as $oRow):
        $this->_aChatUserRecipients[$oRow->id] = trim($oRow->first_name . " " . $oRow->last_name);
      endforeach;
    endif;
    $oOnlineChatUserRecipients = $oModelUser->findAskOnline();
    if (isset($oOnlineChatUserRecipients)):
      foreach ($oOnlineChatUserRecipients as $oRow):
        $this->_aOnlineChatUserRecipients[$oRow->id] = $oRow->id;
      endforeach;
    endif;
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

    $oOnlineChatUserRecipients = new Zend_Form_Element_Multiselect("online_chat_user_recipients");
    $oOnlineChatUserRecipients->setLabel("Konsultanci on-line:");
    $oOnlineChatUserRecipients->setRequired(FALSE);
    $oOnlineChatUserRecipients->setAttrib("class", "multiselect");
    $oOnlineChatUserRecipients->addMultiOptions($this->_aChatUserRecipients);
    $oOnlineChatUserRecipients->setValue($this->_aOnlineChatUserRecipients);
    $this->addElement($oOnlineChatUserRecipients);

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
