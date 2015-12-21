<?php

class User_OrdersettingsController extends Zend_Controller_Action
{

  private $_nUserId = null;
  private $_oAuth;
  private $_sRoleName = null;
  private $_sSiteUrl = null;

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
        $aJson = "";
      }
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
    $oFormOrderSettings = new User_Form_OrderSettings();
    $oModelOrderJournal = new User_Model_VOrderJournal();
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

  public function getscannedfileslistAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oModelOrderFile = new User_Model_OrderFile();
    if ($this->_request->isPost()) {
      $aAllOrderFile = array();
      $aAllOrderFile[0] = "-";
      $aTempAllOrderFile = $oModelOrderFile->getNotExists();
      if (count($aTempAllOrderFile)) {
        foreach ($aTempAllOrderFile as $aValue) {
          $aAllOrderFile[$aValue["id"]] = $aValue["user_name"];
        }
      }
      $aJson = $aAllOrderFile;
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
    $oModelVOrderJournal = new User_Model_VOrderJournal();
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
    $oModelVOrderJournal = new User_Model_VOrderJournal();
    if ($this->_request->isPost()) {
      $aAllStatuses = $oModelOrderStatus->getAll()->toArray();
      if (count($aAllStatuses)) {
        foreach ($aAllStatuses as $nKey => $aValue) {
          $aAllStatuses[$nKey]["count"] = $oModelVOrderJournal->getUserCount(null, $aValue["id"]);
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
    $oModelVOrderPaymentHistory = new User_Model_VOrderPaymentHistory();
    $oModelOrderPayment = new User_Model_OrderPayment();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderPaymentId = $aPostData["order_payment_id"];
      if ($oModelOrderPayment->getUserPayment($nOrderPaymentId)) {
        $aJson = $oModelVOrderPaymentHistory->getUserPaymentDetails($nOrderPaymentId)->toArray();
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function init()
  {
    $this->_helper->layout()->setLayout("user/layout_orders");
  }

  public function makeactionAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oModelOrderJournal = new User_Model_OrderJournal();
    //$oModelSybase = new User_Model_Sybase();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderStatusId = (int)$aPostData["order_status_id"];
      $nOrderId = (int)$aPostData["order_id"];
      $bIsCanceled = $aPostData["is_canceled"];
      $aParam = $aPostData["param"];
      $nNewOrderStatusId = $nOrderStatusId + 1;
      if ($nOrderStatusId == 2) {
        if ($bIsCanceled === "true") {
          $nNewOrderStatusId = 7;
          $nItemId = $oModelOrderJournal->getOrderItemId($nOrderId);
          //$oModelSybase->setItemStatusAndRequestable($nItemId, "s", "0");
          $aJson = $oModelOrderJournal->changeStatus($nOrderId, $nNewOrderStatusId);
        } else if ($aParam["outer_magazine"] === "true") {
          $nNewOrderStatusId = 2;
          $aJson = $oModelOrderJournal->changeStatus($nOrderId, $nNewOrderStatusId);
        } else {
          $aJson = $oModelOrderJournal->changeStatus($nOrderId, $nNewOrderStatusId);
        }
      } else if ($nOrderStatusId == 4) {
        $oModelOrderCart = new Borrower_Model_OrderCart();
        $oModelOrderJournalOrderCart = new Borrower_Model_OrderJournalOrderCart();
        $nUserId = $oModelOrderJournal->getOrderUserId($nOrderId);
        $nOrderCartId = $oModelOrderCart->addOrderCart($nUserId);
        if (isset($nOrderCartId)) {
          $nItemId = $oModelOrderJournal->getOrderItemId($nOrderId);
          //$oModelSybase->setItemStatusAndRequestable($nItemId, "s", "0");
          $oModelOrderJournalOrderCart->addOrderJournalOrderCart(array("order_journal_id" => $nOrderId, "order_cart_id" => $nOrderCartId));
          $aJson = $oModelOrderJournal->changeStatus($nOrderId, $nNewOrderStatusId);
        }
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
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
    }
    if ($this->_oAuth->hasIdentity() && ($this->_sSiteUrl != str_replace("/", "", $this->view->baseUrl())))
      $this->_oAuth->clearIdentity();
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
          if (($this->_sRoleName == "superadministrator") || ($this->_sRoleName == "administrator") || ($this->_sRoleName == "librarian")) {
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

  public function setsettingsAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aJson = null;
    $oModelOrderJournal = new User_Model_OrderJournal();
    $oFormOrderSettings = new User_Form_OrderSettings();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $nOrderId = (int)$aPostData["order_id"];
      $aData = $aPostData["param"];
      if (is_numeric($nOrderId)) {
        $oOrderJournal = $oModelOrderJournal->getRow($nOrderId);
        $aOrderFields = $oFormOrderSettings->getOrderFields($oOrderJournal->order_status_id, $oOrderJournal->is_journal_collection);
        foreach ($aData as $sKey => $sValue) {
          if (!in_array($sKey, $aOrderFields["write_able"])) {
            unset($aData[$sKey]);
          }
        }
        if ($oOrderJournal->order_status_id == 2) {
          $sValue = $aData["amount"];
          $nValue = ((float)preg_replace(array("/\,/"), array("."), $sValue)) * 100;
          if (is_numeric($nValue))
            $aData["amount"] = $nValue;
          else
            $aData["amount"] = null;
        }
        $aData["order_status_id_is_finish"] = 1;
        $aData["modified_date"] = time();
        $aJson = $oModelOrderJournal->saveOrder($nOrderId, $aData);
      }
    }
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

}

?>
