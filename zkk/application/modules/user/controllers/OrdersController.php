<?php

class User_OrdersController extends Zend_Controller_Action
{

  private $_nUserId = null;
  private $_oAuth;
  private $_sPath = null;
  private $_sRoleName = null;
  private $_sSiteUrl = null;

  private function getFileUploadScript()
  {
    $this->_sPath = APPLICATION_PATH . "/../files_scanned/";
  }

  public function addfileAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $this->getFileUploadScript();
    if ($this->_request->isPost()) {
      $oModelOrderFile = new User_Model_OrderFile();
      $oUploadHandler = new AppCms2_UploadHandlerScannedFile();
      $aResult = $oUploadHandler->post();
      if (!isset($aResult["files"][0]->error)) {
        $sUserName = $aResult["files"][0]->name;
        $sGenName = $aResult["files"][0]->gen_name;
        if (!is_dir($this->_sPath)) {
          if (!mkdir($this->_sPath, 0777)) {
            throw new Zend_Exception();
          }
        }
        if (!rename($this->_sPath . $sUserName, $this->_sPath . $sGenName)) {
          unlink($this->_sPath . $sUserName);
        }
        if (file_exists($this->_sPath . $sUserName)) {
          throw new Zend_Exception();
        }
        $aData = array("name" => $sGenName, "user_name" => $sUserName, "upload_date" => time());
        $oModelOrderFile->addRow($aData);
      }
    }
    return 0;
  }

  public function deleteorderAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "id" => array(new Zend_Validate_Digits())
    );
    $oModelOrderJournal = new User_Model_OrderJournal();
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $bJson = false;
    $nOrderId = $oInput->getUnescaped("id");
    if ($this->_nUserId == $oModelOrderJournal->getOrderUserId($nOrderId)) {
      if ($oModelOrderJournal->deleteRow($nOrderId))
        $bJson = true;
    } else {
      if ($this->_oAuth->hasIdentity() && ($this->_sRoleName == "librarian" || $this->_sRoleName == "administrator" || $this->_sRoleName == "superadministrator"))
        if ($oModelOrderJournal->deleteRow($nOrderId))
          $bJson = true;
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function indexAction()
  {
    $oModelOrderStatus = new User_Model_OrderStatus();
    $oModelVOrderJournal = new User_Model_VOrderJournal();
    $oScannedFile = new User_Form_UploadScannedFile();
    $aAllStatuses = $oModelOrderStatus->getAll()->toArray();
    if (count($aAllStatuses)) {
      foreach ($aAllStatuses as $nKey => $aValue) {
        $aAllStatuses[$nKey]["count"] = $oModelVOrderJournal->getUserCount(null, $aValue["id"]);
      }
    }
    $this->view->oScannedFile = $oScannedFile;
    $this->view->aAllStatuses = $aAllStatuses;
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("user/layout_orders");
  }

  public function loaddataordersnewAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "num_row_per_page" => array(new Zend_Validate_Digits()),
      "curr_page" => array(new Zend_Validate_Digits()),
      "sort_column" => array(new AppCms2_Validate_SpecialAlpha()),
      "sort_method" => array(new Zend_Validate_Alpha()),
      "filter_order_status_id" => array(new Zend_Validate_Digits()),
      "filter_call_id" => array("allowEmpty" => true),
      "filter_journal_title" => array("allowEmpty" => true),
      "filter_amount" => array("allowEmpty" => true),
      "filter_id" => array(new Zend_Validate_Digits())
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nNumRowPerPage = $oInput->getUnescaped("num_row_per_page");
    $nCurrPage = $oInput->getUnescaped("curr_page");
    $sSortColumn = $oInput->getUnescaped("sort_column");
    $sSortMethod = $oInput->getUnescaped("sort_method");
    $aFilter = array(
      "order_status_id" => $oInput->getEscaped("filter_order_status_id") != NULL ? $oInput->getUnescaped("filter_order_status_id") : NULL,
      "call_id" => $oInput->getEscaped("filter_call_id") != NULL ? $oInput->getUnescaped("filter_call_id") : NULL,
      "journal_title" => $oInput->getEscaped("filter_journal_title") != NULL ? $oInput->getUnescaped("filter_journal_title") : NULL,
      "amount" => $oInput->getEscaped("filter_amount") != NULL ? (((float)$oInput->getUnescaped("filter_amount")) * 100) : NULL,
      "id" => $oInput->getUnescaped("filter_id")
    );
    foreach ($aFilter as $sKey => $sValue) {
      if (!isset($sValue))
        unset($aFilter[$sKey]);
    }
    $oModelVOrderJournal = new User_Model_VOrderJournal();
    $oRowset = $oModelVOrderJournal->getUserOrders(null, $aFilter, $nNumRowPerPage, ($nCurrPage - 1) * $nNumRowPerPage, $sSortColumn . " " . $sSortMethod);
    $nNumRows = $oModelVOrderJournal->getUserOrders(null, $aFilter)->count();
    $aRowset = $oRowset->toArray();
    foreach ($aRowset as $nKey => $aValue) {
      $oOrderRelationships = $oModelVOrderJournal->getOrderRelationships($aValue["id"], $aValue["item_id"]);
      $aRowset[$nKey]["relationships_count"] = $oOrderRelationships->count();
      $aRowset[$nKey]["relationships"] = $oOrderRelationships->toArray();
    }
    $aJson = array("rowset" => $aRowset, "num_rows" => $nNumRows);
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function orderformAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oFormOrderSettings = new User_Form_OrderSettings();
    $sJson = $oFormOrderSettings->render();
    header("Content-type: application/json");
    echo Zend_Json::encode($sJson);
    exit;
  }

  public function preDispatch()
  {
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_sRoleName = $this->_oAuth->getStorage()->read()->role_name;
      $this->_sSiteUrl = $this->_oAuth->getStorage()->read()->site_url;
    }
    if ($this->_oAuth->hasIdentity() && ($this->_sSiteUrl != str_replace("/", "", $this->view->baseUrl())))
      $this->_oAuth->clearIdentity();
  }

  public function printAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    require_once("../library/zkk/AppCms2/tcpdf/tcpdf.php");
    $nOrderId = (int)$this->_getParam("no");
    if (is_numeric($nOrderId) && $nOrderId > 0 && in_array($this->_sRoleName, array("librarian", "administrator", "superadministrator"))) {
      $oModelOrderJournal = new User_Model_VOrderJournal();
      $oModelOrderChangeLog = new User_Model_OrderChangeLog();
      $oModelOrderJournalOrderChangeLog = new User_Model_OrderJournalOrderChangeLog();
      $oOrderJournal = $oModelOrderJournal->getOne($nOrderId);
      $oPdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false);
      $oPdf->setFont("freesans");
      $oPdf->setPrintHeader(false);
      $oPdf->addPage();
      $oPdf->SetFontSize(12);
      $oPdf->writeHTMLCell(60, 10, "", 5, "<strong>$oOrderJournal->call_id</strong>", 0, 0, false, true, "L", true);
      $oPdf->writeHTMLCell(120, 10, "", "", "ZAKŁADKA ZAMÓWIENIA KOPII", 0, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Numer zamówienia: <strong>$oOrderJournal->id</strong>", 0, 0, false, true, "L", true);
      $oPdf->writeHTMLCell(75, 5, "", "", "Sygnatura: <strong>$oOrderJournal->call_id</strong><br />Sygnatura lokalna: <strong>$oOrderJournal->csa_call_id</strong><br />Kolekcja: <strong>$oOrderJournal->collection</strong>", 0, 1, false, true, "R", true);
      $oPdf->writeHTMLCell(190, 5, "", "", "", 0, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(190, 5, "", "", "", 0, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 30, "", "", "Tytuł książki / czasopisma:<br /><strong>$oOrderJournal->journal_title</strong>", 1, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Rocznik: <strong>$oOrderJournal->journal_year_publication</strong>", 1, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Numeracja: <strong>$oOrderJournal->journal_number</strong>", 1, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Strony od: <strong>$oOrderJournal->page_from</strong>", 1, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Strony do: <strong>$oOrderJournal->page_until</strong>", 1, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Autor: <strong>$oOrderJournal->article_author</strong>", 1, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 30, "", "", "Nazwa rozdziału / artykułu:<br /><strong>$oOrderJournal->article_title</strong>", 1, 1, false, true, "L", true);
      $sCurrTime = date("Y-m-d H:i:s", time());
      $sHtml1 = "
        Data zamówienia: <strong>$sCurrTime</strong><br /><br />
        Zamówione dla: <br /><strong>$oOrderJournal->user_first_name $oOrderJournal->user_last_name</strong><br /><br />
        Przekazać do: <br /><strong></strong>
      ";
      $oPdf->writeHTMLCell(95, 5, 110, 75, $sHtml1, 0, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(60, 10, "", 150, "<strong>$oOrderJournal->call_id</strong>", 0, 0, false, true, "L", true);
      $oPdf->writeHTMLCell(120, 10, "", "", "ZAKŁADKA ZAMÓWIENIA KOPII", 0, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Numer zamówienia: <strong>$oOrderJournal->id</strong>", 0, 0, false, true, "L", true);
      $oPdf->writeHTMLCell(75, 5, "", "", "Sygnatura: <strong>$oOrderJournal->call_id</strong><br />Sygnatura lokalna: <strong>$oOrderJournal->csa_call_id</strong><br />Kolekcja: <strong>$oOrderJournal->collection</strong>", 0, 1, false, true, "R", true);
      $oPdf->writeHTMLCell(190, 5, "", "", "", 0, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(190, 5, "", "", "", 0, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 30, "", "", "Tytuł książki / czasopisma:<br /><strong>$oOrderJournal->journal_title</strong>", 1, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Rocznik: <strong>$oOrderJournal->journal_year_publication</strong>", 1, 1, false, true, "L", true);
      $oPdf->writeHTMLCell(95, 5, "", "", "Numeracja: <strong>$oOrderJournal->journal_number</strong>", 1, 1, false, true, "L", true);
      $sHtml2 = "
        Data zamówienia: <strong>$sCurrTime</strong><br /><br />
        Zamówione dla: <br /><strong></strong>
      ";
      $oPdf->writeHTMLCell(95, 5, 110, 190, $sHtml2, 0, 1, false, true, "L", true);
      $oPdf->Output($nOrderId . ".pdf", "I");
      $nOrderChangeLogId = $oModelOrderChangeLog->addRow(array("order_change_type_id" => 3, "user_id" => $this->_oAuth->getStorage()->read()->user_id, "date" => time()));
      $oModelOrderJournalOrderChangeLog->addRow(array("order_journal_id" => $nOrderId, "order_change_log_id" => $nOrderChangeLogId));
    }
    exit;
  }

  public function tableAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $sJson = $this->view->render("orders/table.phtml");
    header("Content-type: application/json");
    echo Zend_Json::encode($sJson);
    exit;
  }

}

?>
