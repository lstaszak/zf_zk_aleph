<?php

class Borrower_OrdersettingsController extends Zend_Controller_Action
{
  private $_nUserId = null;
  private $_oAuth;
  private $_sRoleName = null;
  private $_sSiteUrl = null;
  private $_sUserName = null;

  public function clearsessionsuccessAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oSuccessSession = new Zend_Session_Namespace("success");
    $oSuccessSession->bIsSave = false;
    $oSuccessSession->bIsNew = false;
    $oSuccessSession->nNewOrder = 0;
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function getbtnactionnameAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderStatusId = $aPostData["order_status_id"];
      if ($nOrderStatusId == 1) {
        $aJson = "";
      } else if ($nOrderStatusId == 2) {
        $aJson = "";
      } else if ($nOrderStatusId == 3) {
        $aJson = "";
      } else if ($nOrderStatusId == 4) {
        $aJson = "";
      } else if ($nOrderStatusId == 5) {
        $aJson = "";
      } else if ($nOrderStatusId == 6) {
        $aJson = "Pobierz plik PDF";
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function getcartjournalsAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oModelVOrderPaymentHistory = new Borrower_Model_VOrderPaymentHistory();
    $oModelOrderPayment = new User_Model_OrderPayment();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderPaymentId = $aPostData["order_payment_id"];
      if ($this->_nUserId == $oModelOrderPayment->getUserPayment($nOrderPaymentId)) {
        $aCartJournals = $oModelVOrderPaymentHistory->getCartJournals($nOrderPaymentId)->toArray();
        $nCartCount = count($aCartJournals);
        $sPaymentDesc = '';
        foreach ($aCartJournals as $aValue) {
          $sPaymentDesc .= $aValue["order_journal_id"];
          if ($nCartCount > 1)
            $sPaymentDesc .= "; ";
        }
        $aJson = $sPaymentDesc;
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function getcarttotalamountAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    if ($this->_request->isPost()) {
      $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
      $aJson = $oModelVOrderJournal->getCartTotalAmount($this->_nUserId);
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function getfieldsAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oFormOrderSettings = new Borrower_Form_OrderSettings();
    $oModelOrderJournal = new Borrower_Model_VOrderJournal();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderId = (int)$aPostData["order_id"];
      $oOrderJournal = $oModelOrderJournal->getRow($nOrderId);
      if (is_numeric($nOrderId)) {
        $aJson = $oFormOrderSettings->getOrderFields($oOrderJournal->order_status_id, $oOrderJournal->is_journal_collection);
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
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

  public function getsettingsAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderId = (int)$aPostData["order_id"];
      if (is_numeric($nOrderId) && $nOrderId > 0) {
        $aRow = $oModelVOrderJournal->getOne($nOrderId)->toArray();
        $aJson = array("success" => true, "row" => $aRow);
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function gettabscountAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oModelOrderStatus = new User_Model_OrderStatus();
    $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
    if ($this->_request->isPost()) {
      $aAllStatuses = $oModelOrderStatus->getAll()->toArray();
      if (count($aAllStatuses)) {
        foreach ($aAllStatuses as $nKey => $aValue) {
          $aAllStatuses[$nKey]["count"] = $oModelVOrderJournal->getUserCount($this->_nUserId, $aValue["id"]);
        }
        $aJson = $aAllStatuses;
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function getuserpaymentdetailsAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oModelVOrderPaymentHistory = new Borrower_Model_VOrderPaymentHistory();
    $oModelOrderPayment = new User_Model_OrderPayment();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderPaymentId = $aPostData["order_payment_id"];
      if ($this->_nUserId == $oModelOrderPayment->getUserPayment($nOrderPaymentId)) {
        $aJson = $oModelVOrderPaymentHistory->getUserPaymentDetails($this->_nUserId, $nOrderPaymentId)->toArray();
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("borrower/layout_orders");
  }

  public function makeactionAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oMail = new AppCms2_Controller_Plugin_Mail();
    $oModelOrderJournal = new User_Model_OrderJournal();
    //$oModelSybase = new User_Model_Sybase();
    $oModelOrderFile = new User_Model_OrderFile();
    $oModelOrderChangeLog = new User_Model_OrderChangeLog();
    $oModelOrderJournalOrderChangeLog = new User_Model_OrderJournalOrderChangeLog();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderStatusId = $aPostData["order_status_id"];
      $nOrderId = $aPostData["order_id"];
      $bIsCanceled = $aPostData["is_canceled"];
      $aParam = $aPostData["param"];
      $nNewOrderStatusId = $nOrderStatusId + 1;
      if ($nOrderStatusId == 1) {
        if ($this->_nUserId == $oModelOrderJournal->getOrderUserId($nOrderId)) {
          $aJson = $oModelOrderJournal->changeStatus($nOrderId, $nNewOrderStatusId);
        }
      } else if ($nOrderStatusId == 3) {
        if ($this->_nUserId == $oModelOrderJournal->getOrderUserId($nOrderId)) {
          if ($bIsCanceled == "true") {
            $nNewOrderStatusId = 7;
            $nItemId = $oModelOrderJournal->getOrderItemId($nOrderId);
            //$oModelSybase->setItemStatusAndRequestable($nItemId, "s", "0");
            $aJson = $oModelOrderJournal->changeStatusCancel($nOrderId, $nNewOrderStatusId);
          } else {
            $aJson = $oModelOrderJournal->changeStatus($nOrderId, $nNewOrderStatusId);
          }
        }
      } else if ($nOrderStatusId == 6) {
        if ($this->_nUserId == $oModelOrderJournal->getOrderUserId($nOrderId)) {
          $nOrderFileId = $oModelOrderJournal->getRow($nOrderId)->order_file_id;
          if (isset($nOrderFileId)) {
            $oOrderFileInfo = $oModelOrderFile->getRow($nOrderFileId);
            if (isset($oOrderFileInfo)) {
              $aJson = $this->_nUserId . "/" . $oOrderFileInfo->name;
              $nOrderChangeLogId = $oModelOrderChangeLog->addRow(array("order_change_type_id" => 8, "user_id" => $this->_oAuth->getStorage()->read()->user_id, "date" => time()));
              $oModelOrderJournalOrderChangeLog->addRow(array("order_journal_id" => $nOrderId, "order_change_log_id" => $nOrderChangeLogId));
            }
          }
        }
      } else if ($nOrderStatusId == 7) {
        $oMail->sendSendInvoice($aAdminEmailAddress, $aParam);
      }
      header("Content-type: application/json");
      echo Zend_Json::encode($aJson);
      exit;
    }
  }

  public function preDispatch()
  {
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

  public function renderinvoiceformAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oModelVOrderPaymentHistory = new Borrower_Model_VOrderPaymentHistory();
    $oFormOrderInvoice = new Borrower_Form_OrderInvoice();
    $oUser = new Admin_Model_User();
    $aJson = null;
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($aPostData["module"] == "borrower" && is_numeric($aPostData["order_payment_id"]) && isset($oFormOrderInvoice)) {
        $oRowset = $oModelVOrderPaymentHistory->getUserPaymentDetails($this->_nUserId, $aPostData["order_payment_id"]);
        if ($oRowset->count()):
          $aParam["id"] = $aPostData["order_payment_id"];
          $aParam["user_email_address"] = $oUser->findEmailAddress($this->_nUserId);
          $aParam["user_name"] = $this->_sUserName;
          foreach ($oRowset->toArray() as $nKey => $aValue):
            $aParam["amount"] = ($aValue["order_payment_total_amount"] / 100) . " PLN";
            $aParam["order_id"] .= $aValue["order_journal_id"] . "; ";
          endforeach;
        endif;
        $oFormOrderInvoice->populate($aParam);
        $aJson = $oFormOrderInvoice->render();
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function renderorderformAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderId = (int)$aPostData["order_id"];
      if ($nOrderId) {
        if ($aPostData["module"] == "borrower") {
          $oModelOrderJournal = new Borrower_Model_VOrderJournal();
          $oFormOrderSettings = new Borrower_Form_OrderSettings();
        } else if ($aPostData["module"] == "user") {
          if (($this->_sRoleName == "superadministrator") || ($this->_sRoleName == "administrator")) {
            $oModelOrderJournal = new User_Model_OrderJournal();
            $oFormOrderSettings = new User_Form_OrderSettings();
          }
        }
        if (isset($oFormOrderSettings)) {
          $oOrderJournal = $oModelOrderJournal->getRow($nOrderId);
          $oFormOrderSettings->getOrderFields($oOrderJournal->order_status_id, $oOrderJournal->is_journal_collection);
          $aJson = $oFormOrderSettings->render();
        }
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function sendinvoiceAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    if ($this->_request->isPost()) {
      $oOrderInvoiceForm = new Borrower_Form_OrderInvoice();
      $aPostData = $this->_request->getPost();
      if ($oOrderInvoiceForm->isValid($aPostData)) {
        $oMail = new AppCms2_Controller_Plugin_Mail();
        $oMail->sendInvoice($aPostData);
        $aJson = true;
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function setsettingsAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oModelOrderJournal = new User_Model_OrderJournal();
    //$oModelSybase = new User_Model_Sybase();
    $oFormOrderSettings = new Borrower_Form_OrderSettings();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderId = $aPostData["order_id"];
      $aData = $aPostData["param"];
      if (is_numeric($nOrderId)) {
        if ($this->_nUserId == $oModelOrderJournal->getOrderUserId($nOrderId)) {
          $oOrderJournal = $oModelOrderJournal->getRow($nOrderId);
          //$aItemInfo = $oModelSybase->getItemStatusAndRequestable($oOrderJournal->item_id);
          $aOrderFields = $oFormOrderSettings->getOrderFields($oOrderJournal->order_status_id, $oOrderJournal->is_journal_collection);
          if ($oOrderJournal->order_status_id == 1) { // && !in_array($aItemInfo["item_status"], array("csa", "o"))) {
            foreach ($aData as $sKey => $sValue) {
              if (!in_array($sKey, $aOrderFields["write_able"])) {
                unset($aData[$sKey]);
              }
            }
            $aData["order_status_id_is_finish"] = 1;
            $aData["modified_date"] = time();
            //$oModelSybase->setItemStatusAndRequestable($oOrderJournal->item_id, "article", "0");
            $aJson = $oModelOrderJournal->saveOrder($nOrderId, $aData);
          } else if ($oOrderJournal->order_status_id == 1) { // && in_array($aItemInfo["item_status"], array("csa", "o"))) {
            $nNewOrderStatusId = 7;
            $oModelOrderJournal->changeStatus($nOrderId, $nNewOrderStatusId);
            $aJson = "not_requestable";
          } else if ($oOrderJournal->order_status_id != 1) {
            $aOrderFields = $oFormOrderSettings->getOrderFields($oOrderJournal->order_status_id);
            foreach ($aData as $sKey => $sValue) {
              if (!in_array($sKey, $aOrderFields["write_able"])) {
                unset($aData[$sKey]);
              }
            }
            $aData["order_status_id_is_finish"] = 1;
            $aData["modified_date"] = time();
            $aJson = $oModelOrderJournal->saveOrder($nOrderId, $aData);
          }
        }
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }
}

?>
