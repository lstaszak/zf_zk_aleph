<?php

class Admin_Form_LayoutHome extends AppCms2_Controller_Plugin_FormLayoutAbstract
{

  private $_aFilters = array("StringTrim");
  private $_aAllMoreMenu = array();
  private $_aAllImage = array();

  public function __construct()
  {
    parent::__construct();
    $this->_aAllMoreMenu = parent::getAllMoreMenu();
    $this->_aAllImage = parent::getAllImage();

    $this->setName(get_class());
    $this->getElement("form_name")->setValue(get_class());

    $oImageBox1 = new Zend_Form_Element_Select("site_field_image_box1");
    $oImageBox1->setLabel("Zdjęcie box1:")->setFilters($this->_aFilters);
    $oImageBox1->setRequired(FALSE);
    $oImageBox1->addMultiOptions($this->_aAllImage);
    $this->addElement($oImageBox1);

    $oHeaderBox1 = new Zend_Form_Element_Text("site_field_header_box1");
    $oHeaderBox1->setLabel("Nagłówek box1:")->setFilters($this->_aFilters);
    $oHeaderBox1->setRequired(FALSE);
    $this->addElement($oHeaderBox1);

    $oBox1 = new Zend_Form_Element_Textarea("site_field_box1");
    $oBox1->setLabel("box1:")->setFilters($this->_aFilters);
    $oBox1->setRequired(FALSE)->setAttrib("class", "ckeditor");
    $this->addElement($oBox1);

    $oMoreBox1 = new Zend_Form_Element_Select("more_box1");
    $oMoreBox1->setLabel("Więcej box1:");
    $oMoreBox1->setRequired(FALSE);
    $oMoreBox1->addMultiOptions($this->_aAllMoreMenu);
    $this->addElement($oMoreBox1);

    $oImageBox2 = new Zend_Form_Element_Select("site_field_image_box2");
    $oImageBox2->setLabel("Zdjęcie box2:")->setFilters($this->_aFilters);
    $oImageBox2->setRequired(FALSE);
    $oImageBox2->addMultiOptions($this->_aAllImage);
    $this->addElement($oImageBox2);

    $oHeaderBox2 = new Zend_Form_Element_Text("site_field_header_box2");
    $oHeaderBox2->setLabel("Nagłówek box2:")->setFilters($this->_aFilters);
    $oHeaderBox2->setRequired(FALSE);
    $this->addElement($oHeaderBox2);

    $oBox2 = new Zend_Form_Element_Textarea("site_field_box2");
    $oBox2->setLabel("box2:")->setFilters($this->_aFilters);
    $oBox2->setRequired(FALSE)->setAttrib("class", "ckeditor");
    $this->addElement($oBox2);

    $oMoreBox2 = new Zend_Form_Element_Select("more_box2");
    $oMoreBox2->setLabel("Więcej box2:");
    $oMoreBox2->setRequired(FALSE);
    $oMoreBox2->addMultiOptions($this->_aAllMoreMenu);
    $this->addElement($oMoreBox2);

    $oImageBox3 = new Zend_Form_Element_Select("site_field_image_box3");
    $oImageBox3->setLabel("Zdjęcie box3:")->setFilters($this->_aFilters);
    $oImageBox3->setRequired(FALSE);
    $oImageBox3->addMultiOptions($this->_aAllImage);
    $this->addElement($oImageBox3);

    $oHeaderBox3 = new Zend_Form_Element_Text("site_field_header_box3");
    $oHeaderBox3->setLabel("Nagłówek box3:")->setFilters($this->_aFilters);
    $oHeaderBox3->setRequired(FALSE);
    $this->addElement($oHeaderBox3);

    $oBox3 = new Zend_Form_Element_Textarea("site_field_box3");
    $oBox3->setLabel("box3:")->setFilters($this->_aFilters);
    $oBox3->setRequired(FALSE)->setAttrib("class", "ckeditor");
    $this->addElement($oBox3);

    $oMoreBox3 = new Zend_Form_Element_Select("more_box3");
    $oMoreBox3->setLabel("Więcej box3:");
    $oMoreBox3->setRequired(FALSE);
    $oMoreBox3->addMultiOptions($this->_aAllMoreMenu);
    $this->addElement($oMoreBox3);

    $oSubmit = $this->createElement("submit", "submit");
    $oSubmit->setLabel("Zapisz");
    $this->addElement($oSubmit);
  }

  public function getSiteFields()
  {
    return array_merge(parent::getStandardSiteFields(), array(
        "site_field_box1",
        "site_field_box2",
        "site_field_box3",
        "site_field_image_box1",
        "site_field_image_box2",
        "site_field_image_box3",
        "site_field_header_box1",
        "site_field_header_box2",
        "site_field_header_box3",
        "more_box1",
        "more_box2",
        "more_box3")
    );
  }

}

?>
