<?php

class Borrower_OrdersController extends Zend_Controller_Action
{
  private $_nUserId = null;
  private $_oAuth;
  private $_sRoleName = null;
  private $_sSiteUrl = null;
  private $_sUserName = null;

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
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($bJson);
    exit;
  }

  public function getexpirationAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $sPath = APPLICATION_PATH . "/../../public_html/files_scanned/";
    $oModelOrderJournal = new User_Model_OrderJournal();
    $oModelOrderVJournal = new User_Model_VOrderJournal();
    //$oModelSybase = new User_Model_Sybase();
    $oOrderExpiration = $oModelOrderVJournal->getOrderExpiration();
    foreach ($oOrderExpiration as $oRow) {
      $nUserId = $oRow->user_id;
      $nOrderId = $oRow->id;
      $nOrderStatusId = $oRow->order_status_id;
      $nOrderFileName = $oRow->order_file_name;
      $nItemId = $oModelOrderJournal->getOrderItemId($nOrderId);
      //$oModelSybase->setItemStatusAndRequestable($nItemId, "s", "0");
      if ($oModelOrderJournal->changeStatus($nOrderId, 7, 2)) {
        if ($nOrderStatusId === 6) {
          unlink($sPath . "user_id_" . $nUserId . "/" . $nOrderFileName);
        }
      }
    }
    exit;
  }

  public function getorderlastmonitexpirationAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oModelOrderVJournal = new User_Model_VOrderJournal();
    $oOrderExpirationLastMonit = $oModelOrderVJournal->getOrderLastMonitExpiration();
    foreach ($oOrderExpirationLastMonit as $oRow) {
      $nOrderId = $oRow->id;
      $nOrderStatusId = $oRow->order_status_id;
      $oMail = new AppCms2_Controller_Plugin_Mail();
      $oMail->sendBorrowerOrderStatusInfo($nOrderId, $nOrderStatusId);
    }
    exit;
  }

  public function getsessionsuccessAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oSuccessSession = new Zend_Session_Namespace("success");
    if ($oSuccessSession->bIsSave) {
      $nNewOrder = $oSuccessSession->nNewOrder;
      $oSuccessSession->bIsSave = false;
      $oSuccessSession->bIsNew = false;
      $oSuccessSession->nNewOrder = 0;
      $aJson = $nNewOrder;
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function indexAction()
  {
    $oXServer = new AppCms2_Controller_Plugin_XServer();
    //$oModelSybase = new Borrower_Model_Sybase();
    $oModelOrderStatus = new User_Model_OrderStatus();
    $oModelOrderJournal = new User_Model_OrderJournal();
    $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
    $oModelOrderChangeLog = new User_Model_OrderChangeLog();
    $oModelOrderJournalOrderChangeLog = new User_Model_OrderJournalOrderChangeLog();
    $oSuccessSession = new Zend_Session_Namespace("success");
    $aData = null;
    $nItemHashGet = $this->_request->getParam("new");
    $nOrderJournalId = $this->_request->getParam("open");
    $nItemHashSession = $oSuccessSession->bIsNew;
    if (isset($nItemHashGet)) {
      $nItemHash = $nItemHashGet;
    } else if (isset($nItemHashSession)) {
      $oSuccessSession->bIsNew = false;
      $nItemHash = $nItemHashSession;
    }
    if ($oSuccessSession->bIsSave === true) {
      $oSuccessSession->bIsSave = false;
      $oSuccessSession->bIsNew = false;
      $oSuccessSession->nNewOrder = 0;
    }
    if (isset($nItemHash) && is_numeric($nItemHash)) {
      $aJournalSettings = $oXServer->getJournalSettings($nItemHash);
      if ($aJournalSettings) {
        $aData["user_id"] = $this->_nUserId;
        $aData["order_status_id"] = 1;
        $aData["item_id"] = $aJournalSettings["item#"];
        $aData["call_id"] = $aJournalSettings["call"];
        $aData["csa_call_id"] = $aJournalSettings["csa_call"];
        $aData["location"] = $aJournalSettings["location"];
        $aData["is_journal_collection"] = 1;
        $aData["collection"] = null;
        $aData["journal_title"] = $aJournalSettings["processed"];
        $nTime = time();
        $aData["created_date"] = $nTime;
        $aData["modified_date"] = $nTime;
        $nOrderJournalId = $oModelOrderJournal->saveNewOrder($aData);
        if ($nOrderJournalId) {
          $oSuccessSession->bIsSave = true;
          $oSuccessSession->bIsNew = false;
          $oSuccessSession->nNewOrder = $nOrderJournalId;
          $nOrderChangeLogId = $oModelOrderChangeLog->addRow(array("order_change_type_id" => 1, "user_id" => $this->_nUserId, "date" => $nTime));
          $oModelOrderJournalOrderChangeLog->addRow(array("order_journal_id" => $nOrderJournalId, "order_change_log_id" => $nOrderChangeLogId));
        }
      }
    } else if (isset($nOrderJournalId) && is_numeric($nOrderJournalId)) {
      $oSuccessSession->bIsSave = true;
      $oSuccessSession->bIsNew = false;
      $oSuccessSession->nNewOrder = $nOrderJournalId;
    }
    $aAllStatuses = $oModelOrderStatus->getAll()->toArray();
    if (count($aAllStatuses)) {
      foreach ($aAllStatuses as $nKey => $aValue) {
        $aAllStatuses[$nKey]["count"] = $oModelVOrderJournal->getUserCount($this->_nUserId, $aValue["id"]);
      }
    }
    $this->view->aAllStatuses = $aAllStatuses;
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("borrower/layout_orders");
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
    $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
    $oRowset = $oModelVOrderJournal->getUserOrders($this->_nUserId, $aFilter, $nNumRowPerPage, ($nCurrPage - 1) * $nNumRowPerPage, $sSortColumn . " " . $sSortMethod);
    $nNumRows = $oModelVOrderJournal->getUserOrders($this->_nUserId, $aFilter)->count();
    $aJson = array("rowset" => $oRowset->toArray(), "num_rows" => $nNumRows);
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function orderformAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oFormOrderSettings = new Borrower_Form_OrderSettings();
    $sJson = $oFormOrderSettings->render();
    header("Content-type: application/json");
    echo Zend_Json::encode($sJson);
    exit;
  }

  public function preDispatch()
  {
    $oSuccessSession = new Zend_Session_Namespace("success");
    $nItemHash = $this->_request->getParam("new");
    if (isset($nItemHash)) {
      $oSuccessSession->bIsNew = $nItemHash;
    }
    $this->_oAuth = AppCms2_Controller_Plugin_ModuleAuth::getInstance();
    if ($this->_oAuth->hasIdentity()) {
      $this->_nUserId = $this->_oAuth->getStorage()->read()->user_id;
      $this->_sRoleName = $this->_oAuth->getStorage()->read()->role_name;
      $this->_sSiteUrl = $this->_oAuth->getStorage()->read()->site_url;
      $this->_sUserName = $this->_oAuth->getStorage()->read()->first_name . " " . $this->_oAuth->getStorage()->read()->last_name;
    }
    if ($this->_oAuth->hasIdentity() && ($this->_sSiteUrl != str_replace("/", "", $this->view->baseUrl()))) {
      $this->_oAuth->clearIdentity();
    }
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
