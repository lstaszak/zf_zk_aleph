<?php

class User_Form_OrderSettings extends Zend_Form
{

  private $_aAllOrderFile = array();
  private $_aFilters = array("StringTrim");

  public function __construct($options = null)
  {
    $oModelOrderFile = new User_Model_OrderFile();
    $oModelOrderFile->getAll();
    $this->_aAllOrderFile[0] = "-";
    $aAllOrderFile = $oModelOrderFile->getNotExists();
    if (count($aAllOrderFile)) {
      foreach ($aAllOrderFile as $aValue) {
        $this->_aAllOrderFile[$aValue["id"]] = $aValue["user_name"];
      }
    }
    parent::__construct($options);
  }

  public function getOrderFields($nOrderStatusId, $bIsJournalCollection = null)
  {
    $aFormOrderSettings = array();
    $aWriteAbleFields = array();
    $aRequiredFields = array();
    if (is_numeric($nOrderStatusId)) {
      if ($nOrderStatusId == 1) {
        $aFormOrderSettings = array(
          array(
            "element_name" => "id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_first_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_last_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_email_address",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => true
          ),
          array(
            "element_name" => "phone",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_file_id",
            "remove" => true
          ),
          array(
            "element_name" => "order_file_user_name",
            "remove" => true
          ),
          array(
            "element_name" => "amount",
            "remove" => true
          ),
          array(
            "element_name" => "location",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "csa_call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_group",
            "remove" => true,
          ),
          array(
            "element_name" => "collection",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_group",
            "remove" => true,
          ),
          array(
            "element_name" => "journal_year_publication",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_number",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_author",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_from",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_until",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "comment",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "footer_pdf",
            "attrib" => null,
            "remove" => true,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "user_created_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_modified_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_execution_date",
            "remove" => true
          ),
          array(
            "element_name" => "user_expiration_date",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_save_and_make_action",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_save_and_print",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_cancel",
            "remove" => true
          ),
          array(
            "element_name" => "order_add_file",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_outer_magazine",
            "remove" => true
          )
        );
      } else if ($nOrderStatusId == 2) {
        $aFormOrderSettings = array(
          array(
            "element_name" => "id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_first_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_last_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_email_address",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => true
          ),
          array(
            "element_name" => "phone",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_file_id",
            "remove" => true
          ),
          array(
            "element_name" => "order_file_user_name",
            "remove" => true
          ),
          array(
            "element_name" => "amount",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => true
          ),
          array(
            "element_name" => "location",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "csa_call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "collection",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_group",
            "remove" => true,
          ),
          array(
            "element_name" => "journal_year_publication",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "journal_number",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "article_author",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "article_title",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "page_from",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => true
          ),
          array(
            "element_name" => "page_until",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => true
          ),
          array(
            "element_name" => "comment",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "footer_pdf",
            "attrib" => null,
            "remove" => true,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "user_created_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_modified_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_execution_date",
            "remove" => true
          ),
          array(
            "element_name" => "user_expiration_date",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_save_and_make_action",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Wyślij do zatwierdzenia kwoty"
          ),
          array(
            "element_name" => "order_submit_save_and_print",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Wydrukuj zakładkę zamówienia"
          ),
          array(
            "element_name" => "order_submit_cancel",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Zamówienie nie może być zrealizowane"
          ),
          array(
            "element_name" => "order_submit_outer_magazine",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Zamówienie z magazynu zewnętrznego"
          ),
          array(
            "element_name" => "order_add_file",
            "remove" => true
          ),
        );
      } else if ($nOrderStatusId == 3) {
        $aFormOrderSettings = array(
          array(
            "element_name" => "id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_first_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_last_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_email_address",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => true
          ),
          array(
            "element_name" => "phone",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_file_id",
            "remove" => true
          ),
          array(
            "element_name" => "order_file_user_name",
            "remove" => true
          ),
          array(
            "element_name" => "amount",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "location",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "csa_call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "collection",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_group",
            "remove" => true,
          ),
          array(
            "element_name" => "journal_year_publication",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_number",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_author",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_from",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_until",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "comment",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "footer_pdf",
            "attrib" => null,
            "remove" => true,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "user_created_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_modified_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_execution_date",
            "remove" => true
          ),
          array(
            "element_name" => "user_expiration_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_submit_save_and_make_action",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_save_and_print",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Wydrukuj zakładkę zamówienia"
          ),
          array(
            "element_name" => "order_submit_cancel",
            "remove" => true
          ),
          array(
            "element_name" => "order_add_file",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_outer_magazine",
            "remove" => true
          )
        );
      } else if ($nOrderStatusId == 4) {
        $aFormOrderSettings = array(
          array(
            "element_name" => "id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_first_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_last_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_email_address",
            "attrib" => null,
            "remove" => true,
            "write_able" => false,
            "required" => true
          ),
          array(
            "element_name" => "phone",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_file_id",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => true
          ),
          array(
            "element_name" => "order_file_user_name",
            "remove" => true
          ),
          array(
            "element_name" => "amount",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "location",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "csa_call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "collection",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_group",
            "remove" => true,
          ),
          array(
            "element_name" => "journal_year_publication",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_number",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_author",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_from",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_until",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "comment",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "footer_pdf",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "user_created_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_modified_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_execution_date",
            "remove" => true
          ),
          array(
            "element_name" => "user_expiration_date",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_save_and_make_action",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Wyślij do zapłaty"
          ),
          array(
            "element_name" => "order_submit_save_and_print",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Wydrukuj zakładkę zamówienia"
          ),
          array(
            "element_name" => "order_submit_cancel",
            "remove" => true
          ),
          array(
            "element_name" => "order_add_file",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Dodaj zeskanowany plik"
          ),
          array(
            "element_name" => "order_submit_outer_magazine",
            "remove" => true
          )
        );
      } else if ($nOrderStatusId == 5) {
        $aFormOrderSettings = array(
          array(
            "element_name" => "id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_first_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_last_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_email_address",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => true
          ),
          array(
            "element_name" => "phone",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_file_id",
            "remove" => true
          ),
          array(
            "element_name" => "order_file_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "amount",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "location",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "csa_call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "collection",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_group",
            "remove" => true,
          ),
          array(
            "element_name" => "journal_year_publication",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_number",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_author",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_from",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_until",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "comment",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "footer_pdf",
            "attrib" => null,
            "remove" => true,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "user_created_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_modified_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_execution_date",
            "remove" => true
          ),
          array(
            "element_name" => "user_expiration_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_submit_save_and_make_action",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_save_and_print",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Wydrukuj zakładkę zamówienia"
          ),
          array(
            "element_name" => "order_submit_cancel",
            "remove" => true
          ),
          array(
            "element_name" => "order_add_file",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_outer_magazine",
            "remove" => true
          )
        );
      } else if ($nOrderStatusId == 6) {
        $aFormOrderSettings = array(
          array(
            "element_name" => "id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_first_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_last_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_email_address",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => true
          ),
          array(
            "element_name" => "phone",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_file_id",
            "remove" => true
          ),
          array(
            "element_name" => "order_file_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "amount",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "location",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "csa_call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "collection",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_group",
            "remove" => true,
          ),
          array(
            "element_name" => "journal_year_publication",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_number",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_author",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_from",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_until",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "comment",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "footer_pdf",
            "attrib" => null,
            "remove" => true,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "user_created_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_modified_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_execution_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_expiration_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_submit_save_and_make_action",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_save_and_print",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Wydrukuj zakładkę zamówienia"
          ),
          array(
            "element_name" => "order_submit_cancel",
            "remove" => true
          ),
          array(
            "element_name" => "order_add_file",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_outer_magazine",
            "remove" => true
          )
        );
      } else if ($nOrderStatusId == 7) {
        $aFormOrderSettings = array(
          array(
            "element_name" => "id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_first_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_last_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_email_address",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => true
          ),
          array(
            "element_name" => "phone",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_status_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_file_id",
            "remove" => true
          ),
          array(
            "element_name" => "order_file_user_name",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "amount",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "location",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "csa_call_id",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "collection",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => true,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_group",
            "remove" => true,
          ),
          array(
            "element_name" => "journal_year_publication",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "journal_number",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_author",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "article_title",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_from",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "page_until",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "comment",
            "attrib" => null,
            "remove" => false,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "footer_pdf",
            "attrib" => null,
            "remove" => true,
            "write_able" => true,
            "required" => false
          ),
          array(
            "element_name" => "user_created_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_modified_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_execution_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "user_expiration_date",
            "attrib" => array("name" => "disabled", "value" => "disabled"),
            "remove" => false,
            "write_able" => false,
            "required" => false
          ),
          array(
            "element_name" => "order_submit_save_and_make_action",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_save_and_print",
            "attrib" => null,
            "remove" => false,
            "write_able" => false,
            "required" => false,
            "label" => "Wydrukuj zakładkę zamówienia"
          ),
          array(
            "element_name" => "order_submit_cancel",
            "remove" => true
          ),
          array(
            "element_name" => "order_add_file",
            "remove" => true
          ),
          array(
            "element_name" => "order_submit_outer_magazine",
            "remove" => true
          )
        );
      }
      foreach ($aFormOrderSettings as $aValue) {
        $sElementName = $aValue["element_name"];
        if ($aValue["remove"]) {
          if ($sElementName === "journal_group" && $this->getDisplayGroup($sElementName) && !$bIsJournalCollection) {
            foreach ($this->getDisplayGroup($sElementName)->getElements() as $sKey => $aElement) {
              $this->removeElement($sKey);
            }
            $this->removeDisplayGroup($sElementName);
          }
          $this->removeElement($sElementName);
        } else {
          if ($this->getElement($sElementName)) {
            if (isset($aValue["attrib"])) {
              $this->getElement($sElementName)->setAttrib($aValue["attrib"]["name"], $aValue["attrib"]["value"]);
            }
            if ($aValue["write_able"]) {
              array_push($aWriteAbleFields, $sElementName);
            } else {
              $this->getElement($sElementName)->clearValidators();
            }
            if ($aValue["required"]) {
              $this->getElement($sElementName)->setRequired(TRUE);
              array_push($aRequiredFields, $sElementName);
            } else {
              $this->getElement($sElementName)->setRequired(FALSE);
            }
            if (is_string($aValue["label"])) {
              $this->getElement($sElementName)->setLabel($aValue["label"]);
            }
          }
        }
      }
      return array("write_able" => $aWriteAbleFields, "required" => $aRequiredFields);
    }
    return null;
  }

  public function init()
  {
    $this->setName(strtolower(get_class()));
    $this->setMethod("post");

    $oFormName = new Zend_Form_Element_Hidden("form_name");
    $oFormName->setValue(get_class());
    $oFormName->setIgnore(FALSE)->removeDecorator("Label");

    $oOrderId = new Zend_Form_Element_Text("id");
    $oOrderId->setLabel("Numer zamówienia:");
    $oOrderId->addValidator(new Zend_Validate_Digits());
    $oOrderId->setRequired(TRUE);

    $oFirstName = new Zend_Form_Element_Text("user_first_name");
    $oFirstName->setLabel("Imię:");
    $oFirstName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oFirstName->setRequired(TRUE);
    $oFirstName->setAttrib("class", "valid");

    $oLastName = new Zend_Form_Element_Text("user_last_name");
    $oLastName->setLabel("Nazwisko:");
    $oLastName->addValidator(new Zend_Validate_Alpha(array("allowWhiteSpace" => true)));
    $oLastName->setRequired(TRUE);
    $oLastName->setAttrib("class", "valid");

    $oAddressEmail = new Zend_Form_Element_Text("user_email_address");
    $oAddressEmail->setLabel("Adres e-mail:");
    $oAddressEmail->addValidator(new Zend_Validate_EmailAddress());
    $oAddressEmail->setRequired(TRUE);
    $oAddressEmail->setAttrib("class", "valid");

    $oPhone = new Zend_Form_Element_Text("phone");
    $oPhone->setLabel("Telefon:");
    $oPhone->setRequired(TRUE);
    $oPhone->setAttrib("class", "valid");

    $oOrderStatusId = new Zend_Form_Element_Text("order_status_id");
    $oOrderStatusId->setLabel("Status zamówienia (ID):");
    $oOrderStatusId->addValidator(new Zend_Validate_Digits());
    $oOrderStatusId->setRequired(TRUE);
    $oOrderStatusId->setAttrib("class", "valid");

    $oOrderStatusUserName = new Zend_Form_Element_Text("order_status_user_name");
    $oOrderStatusUserName->setLabel("Status zamówienia:");
    $oOrderStatusUserName->addValidator(new Zend_Validate_StringLength(array("max" => 20)));
    $oOrderStatusUserName->setRequired(TRUE);

    $oOrderFileId = new Zend_Form_Element_Select("order_file_id");
    $oOrderFileId->setLabel("Nazwa pliku:");
    $oOrderFileId->addMultiOptions($this->_aAllOrderFile);
    $oValidateGreaterThan = new Zend_Validate_GreaterThan(0);
    $oValidateGreaterThan->setMessage("Nie wybrano pliku", Zend_Validate_GreaterThan::NOT_GREATER);
    $oOrderFileId->addValidator($oValidateGreaterThan);
    $oOrderFileId->setRequired(TRUE);
    $oOrderFileId->setAttrib("class", "valid");

    $oOrderFileUserName = new Zend_Form_Element_Text("order_file_user_name");
    $oOrderFileUserName->setLabel("Nazwa pliku:");
    $oOrderFileUserName->addValidator(new Zend_Validate_StringLength(array("max" => 100)));
    $oOrderFileUserName->setRequired(TRUE);

    $oAmount = new Zend_Form_Element_Text("amount");
    $oAmount->setLabel("Cena:");
    $oAmount->addValidator(new Zend_Validate_Float(array("locale" => "en_US")));
    $oAmount->setRequired(TRUE);
    $oAmount->setAttrib("class", "valid");

    $oLocation = new Zend_Form_Element_Text("location");
    $oLocation->setLabel("Lokalizacja:");
    $oLocation->addValidator(new Zend_Validate_StringLength(array("max" => 20)));
    $oLocation->setRequired(TRUE);

    $oCallId = new Zend_Form_Element_Text("call_id");
    $oCallId->setLabel("Sygnatura:");
    $oCallId->addValidator(new Zend_Validate_StringLength(array("max" => 45)));
    $oCallId->setRequired(TRUE);

    $oCsaCallId = new Zend_Form_Element_Text("csa_call_id");
    $oCsaCallId->setLabel("Sygnatura lokalna:");
    $oCsaCallId->addValidator(new Zend_Validate_StringLength(array("max" => 20)));
    $oCsaCallId->setRequired(TRUE);

    $oCollection = new Zend_Form_Element_Textarea("collection");
    $oCollection->setLabel("Kolekcja:");
    $oCollection->addValidator(new Zend_Validate_StringLength(array("max" => 20)));
    $oCollection->setRequired(TRUE);

    $oJournalTitle = new Zend_Form_Element_Textarea("journal_title");
    $oJournalTitle->setLabel("Tytuł książki / czasopisma:");
    $oJournalTitle->addValidator(new Zend_Validate_StringLength(array("max" => 255)));
    $oJournalTitle->setRequired(FALSE);

    $oJournalYearPublication = new Zend_Form_Element_Text("journal_year_publication");
    $oJournalYearPublication->setLabel("Rocznik:");
    $oJournalYearPublication->addValidator(new Zend_Validate_Date("YYYY"));
    $oJournalYearPublication->setRequired(FALSE);
    $oJournalYearPublication->setAttrib("class", "valid");

    $oJournalNumber = new Zend_Form_Element_Text("journal_number");
    $oJournalNumber->setLabel("Numeracja (tom, vol., nr):");
    $oJournalNumber->setRequired(FALSE);
    $oJournalNumber->setAttrib("class", "valid");

    $oArticleAuthor = new Zend_Form_Element_Text("article_author");
    $oArticleAuthor->setLabel("Autor artykułu:");
    $oArticleAuthor->setRequired(TRUE);
    $oArticleAuthor->setAttrib("class", "valid");

    $oArticleTitle = new Zend_Form_Element_Textarea("article_title");
    $oArticleTitle->setLabel("Tytuł artykułu:");
    $oArticleTitle->setRequired(TRUE);
    $oArticleTitle->setAttrib("class", "valid");

    $oPageFrom = new Zend_Form_Element_Text("page_from");
    $oPageFrom->setLabel("Strony od:");
    $oPageFrom->addValidator(new Zend_Validate_Digits());
    $oPageFrom->setRequired(TRUE);
    $oPageFrom->setAttrib("class", "valid");

    $oPageUntil = new Zend_Form_Element_Text("page_until");
    $oPageUntil->setLabel("Strony do:");
    $oPageUntil->addValidator(new Zend_Validate_Digits());
    $oPageUntil->addValidator(new AppCms2_Validate_PageGreaterThan());
    $oPageUntil->setRequired(TRUE);
    $oPageUntil->setAttrib("class", "valid");

    $oComment = new Zend_Form_Element_Textarea("comment");
    $oComment->setLabel("Uwagi do zamówienia:");
    //$oComment->addValidator(new AppCms2_Validate_SpecialAlpha());
    $oComment->setRequired(TRUE);
    $oComment->setAttrib("class", "valid");

    $oFooterPdf = new Zend_Form_Element_Textarea("footer_pdf");
    $oFooterPdf->setLabel("Stopka PDF:");
    $oFooterPdf->setRequired(FALSE);

    $oUserCreatedDate = new Zend_Form_Element_Text("user_created_date");
    $oUserCreatedDate->setLabel("Data utworzenia:");
    $oUserCreatedDate->addValidator(new Zend_Validate_Date("Y-m-d"));
    $oUserCreatedDate->setRequired(TRUE);

    $oUserModifiedDate = new Zend_Form_Element_Text("user_modified_date");
    $oUserModifiedDate->setLabel("Data modyfikacji:");
    $oUserModifiedDate->addValidator(new Zend_Validate_Date("Y-m-d"));
    $oUserModifiedDate->setRequired(TRUE);

    $oUserExecutionDate = new Zend_Form_Element_Text("user_execution_date");
    $oUserExecutionDate->setLabel("Data umieszczenia pliku na serwerze:");
    $oUserExecutionDate->addValidator(new Zend_Validate_Date("Y-m-d"));
    $oUserExecutionDate->setRequired(TRUE);

    $oUserExpirationDate = new Zend_Form_Element_Text("user_expiration_date");
    $oUserExpirationDate->setLabel("Data wygaśnięcia zamówienia:");
    $oUserExpirationDate->addValidator(new Zend_Validate_Date("Y-m-d"));
    $oUserExpirationDate->setRequired(TRUE);

    $this->addElement("hash", "csrf_token", array("ignore" => false, "timeout" => 1440));
    $this->getElement("csrf_token")->removeDecorator("Label");

    $oSubmitSaveAndMakeAction = new Zend_Form_Element_Button("order_submit_save_and_make_action");
    $oSubmitSaveAndMakeAction->setLabel("");

    $oSubmitSaveAndPrint = new Zend_Form_Element_Button("order_submit_save_and_print");
    $oSubmitSaveAndPrint->setLabel("");

    $oSubmitCancel = new Zend_Form_Element_Button("order_submit_cancel");
    $oSubmitCancel->setLabel("");

    $oSubmitAddFile = new Zend_Form_Element_Button("order_add_file");
    $oSubmitAddFile->setLabel("");

    $oSubmitOuterMagazine = new Zend_Form_Element_Button("order_submit_outer_magazine");
    $oSubmitOuterMagazine->setLabel("");

    $this->addElement($oFormName);
    $this->addElement($oOrderId);
    $this->addElement($oOrderStatusId);
    $this->addElement($oOrderStatusUserName);
    $this->addElement($oFirstName);
    $this->addElement($oLastName);
    $this->addElement($oAddressEmail);
    $this->addElement($oPhone);
    $this->addElement($oLocation);
    $this->addElement($oCallId);
    $this->addElement($oCsaCallId);
    $this->addElement($oCollection);
    $this->addElement($oJournalTitle);
    $this->addElement($oPageFrom);
    $this->addElement($oPageUntil);
    $this->addDisplayGroup(array($oJournalYearPublication, $oJournalNumber, $oArticleAuthor, $oArticleTitle), "journal_group", array("legend" => "Dotyczy tylko czasopism"));
    $this->addElement($oUserCreatedDate);
    $this->addElement($oUserModifiedDate);
    $this->addElement($oUserExecutionDate);
    $this->addElement($oUserExpirationDate);
    $this->addElement($oComment);
    $this->addElement($oFooterPdf);
    $this->addElement($oAmount);
    $this->addElement($oOrderFileId);
    $this->addElement($oOrderFileUserName);
    $this->addElement($oSubmitAddFile);
    $this->addElement($oSubmitSaveAndPrint);
    $this->addElement($oSubmitCancel);
    $this->addElement($oSubmitOuterMagazine);
    $this->addElement($oSubmitSaveAndMakeAction);

    $oViewScript = new Zend_Form_Decorator_ViewScript();
    $oViewScript->setViewModule("user");
    $oViewScript->setViewScript("_forms/ordersettings.phtml");
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
