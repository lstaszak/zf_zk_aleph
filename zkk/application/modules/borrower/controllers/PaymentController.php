<?php

class Borrower_PaymentController extends Zend_Controller_Action
{
  private $_oAuth;
  private $_nUserId = null;
  private $_sRoleName = null;
  private $_sSiteUrl = null;
  private $_sAuthKey2 = "W8L07TDIWTG89HTEEXKQO5GSN6BO3VCTO9B4MHRKPCGOATOOWTZP9U49VTNICMK031792G7H4DHDFWZA6FV1VW00Q4E366Q1WL5UKMRKVEVDXNGFLN8TW8MNSUZSM9TZK7PAWG28BLB2G1ES4T94DQTH0I34WRTKM02A5A4EMULSK2EA0UTX69XJB30TJT3N6X1HLU5BTZCQEP342W7HJTTWG10ARZLVKOB9XZ0E2495MV72XXZITWVHEDAN1JLH";
  private $_sUserName = null;

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

  public function init()
  {
    $this->_helper->layout()->setLayout("borrower/layout");
  }

  public function indexAction()
  {
  }

  public function loaddataorderspaymentAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $aInputFilters = array("*" => array(new Zend_Filter_StringTrim()));
    $aInputValidators = array(
      "num_row_per_page" => array(new Zend_Validate_Digits()),
      "curr_page" => array(new Zend_Validate_Digits()),
      "sort_column" => array(new AppCms2_Validate_SpecialAlpha()),
      "sort_method" => array(new Zend_Validate_Alpha())
    );
    $oInput = new Zend_Filter_Input($aInputFilters, $aInputValidators, $_POST);
    $nNumRowPerPage = $oInput->getUnescaped("num_row_per_page");
    $nCurrPage = $oInput->getUnescaped("curr_page");
    $sSortColumn = $oInput->getUnescaped("sort_column");
    $sSortMethod = $oInput->getUnescaped("sort_method");
    $aFilter = array();
    foreach ($aFilter as $sKey => $sValue) {
      if (!isset($sValue))
        unset($aFilter[$sKey]);
    }
    $oModelVOrderPaymentHistory = new Borrower_Model_VOrderPaymentHistory();
    $oRowset = $oModelVOrderPaymentHistory->getUserPayments($this->_nUserId, $aFilter, $nNumRowPerPage, ($nCurrPage - 1) * $nNumRowPerPage, $sSortColumn . " " . $sSortMethod);
    $nNumRows = $oModelVOrderPaymentHistory->getUserPayments($this->_nUserId, $aFilter)->count();
    $aJson = array("rowset" => $oRowset->toArray(), "num_rows" => $nNumRows);
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

  public function newpaymentAction()
  {
    $oFormNewPayment = new Borrower_Form_NewPayment();
    if ($this->getRequest()->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oFormNewPayment->isValid($aPostData)) {
        $oSessionNewPayment = new Zend_Session_Namespace("new_payment");
        $oPayU = new AppCms2_Payments_PayU();
        $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
        $oModelOrderCart = new Borrower_Model_OrderCart();
        $oModelOrderJournal = new User_Model_OrderJournal();
        $oModelOrderJournalOrderCart = new Borrower_Model_OrderJournalOrderCart();
        $oModelOrderPayment = new User_Model_OrderPayment();
        $oModelOrderPaymentHistory = new User_Model_OrderPaymentHistory();
        $oModelUserLog = new Admin_Model_UserLog();
        $oModelUser = new Admin_Model_User();
        $oGenereteSessionId = new AppCms2_GenereteSessionId();
        if ($this->_oAuth->hasIdentity()) {
          $oDb = Zend_Db_Table::getDefaultAdapter();
          try {
            $oDb->beginTransaction();
            $oUserOrderJournal = $oModelVOrderJournal->getUserOrderJournal($this->_nUserId);
            $nOrderCartId = $oModelOrderCart->getOrderCartId($this->_nUserId); //pobiera id koszyka użytkownika
            foreach ($oUserOrderJournal as $oValue) { //sprawdza czy na pewno wszystkie zamówienia znajdują się w koszyku
              $bIsExists = $oModelOrderJournalOrderCart->findOrderJournal($oValue->id, $nOrderCartId);
              if (!$bIsExists)
                $oModelOrderJournalOrderCart->addOrderJournalOrderCart(array("order_journal_id" => $oValue->id, "order_cart_id" => $nOrderCartId));
            }
            $oCart = $oModelOrderJournalOrderCart->getCartJournals($nOrderCartId); //pobiera id czasopism znajdujących się w koszyku
            $nCartCount = $oCart->count();
            foreach ($oCart as $oValue) {
              $sPaymentDesc .= $oValue->order_journal_id;
              if ($nCartCount > 1)
                $sPaymentDesc .= "; ";
            }
            $sPaymentDesc = trim($sPaymentDesc);
            $aPaymentParam = $oGenereteSessionId->generatePaymentParam();
            $oPayU->setUserId($this->_nUserId);
            $oPayU->setOrderId($aPaymentParam["order_id"]);
            $oPayU->setSessionId($aPaymentParam["session_id"]);
            $oPayU->setAmount($oModelVOrderJournal->getCartTotalAmount($this->_nUserId));
            $oPayU->setFirstName($aPostData["first_name"]);
            $oPayU->setLastName($aPostData["last_name"]);
            $oPayU->setEmailAddress($aPostData["email_address"]);
            $oPayU->setStreet("");
            $oPayU->setPostCode("");
            $oPayU->setCity("");
            $oPayU->setPhone("");
            $oPayU->setClientIP($oModelUserLog->getRealIpAddr());
            $oPayU->setDesc("Zamówienie nr " . $sPaymentDesc);
            $oPayU->setSig();
            $aUserParam = $oModelUser->findUser($this->_nUserId);
            if ($oSessionNewPayment->session_key !== md5($this->_nUserId . $aUserParam["email_address"] . $oModelVOrderJournal->getCartTotalAmount($this->_nUserId) . $oFormNewPayment->getKey3()))
              throw new Zend_Exception();
            $oFormPayU = new Borrower_Form_PayU($oPayU); //wypełniona klasa payu
            $nOrderPaymentId = $oModelOrderPayment->addRow($oPayU->getPaymentParam()); //tworzy nowy rekord w tabeli płatności i zwraca jego id
            $oModelOrderJournal->setOrderPaymentId($oCart, $nOrderPaymentId); //łączy czasopismo z płatnością
            $oModelOrderPaymentHistory->setOrderPaymentHistory($oCart, $nOrderPaymentId); //dodaje informacje do tabeli z historią płatności
            $this->view->oFormPayU = $oFormPayU;
            $oDb->commit();
          } catch (Zend_Exception $e) {
            $oDb->rollBack();
            $this->_redirect("/borrower/payment/newpayment");
          }
        }
      }
    } else {
      if ($oFormNewPayment->getIsInit())
        $this->view->oFormNewPayment = $oFormNewPayment;
    }
  }

  public function reportAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oPayU = new AppCms2_Payments_PayU();
    $oModelOrderPayment = new User_Model_OrderPayment();
    $oModelResponse = new User_Model_Response();
    $oModelOrderJournal = new User_Model_OrderJournal();
    $oModelOrderFile = new User_Model_OrderFile();
    $oModelOrderCart = new Borrower_Model_OrderCart();
    $oModelOrderJournalOrderCart = new Borrower_Model_OrderJournalOrderCart();
    $oModelVOrderPaymentHistory = new User_Model_VOrderPaymentHistory();
    $sPathOld = APPLICATION_PATH . "/../files_scanned/";
    $sPathNew = APPLICATION_PATH . "/../../public_html/files_scanned/";
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      if ($oPayU->getResponse($aPostData)) {
        $aPaymentStatus = $oPayU->getPaymentStatus();
        $sOrderId = $aPaymentStatus["order_id"];
        $sSessionId = $aPaymentStatus["session_id"];
        $nAmount = $aPaymentStatus["amount"];
        $oOrderPayment = $oModelOrderPayment->findOrderPayment($sOrderId, $sSessionId, $nAmount);
        if (isset($oOrderPayment)) {
          $nOrderPaymentId = $oOrderPayment->id;
          $nUserId = $oOrderPayment->user_id;
          $oModelResponse->setResponse($nOrderPaymentId, $aPaymentStatus);
          if ($aPaymentStatus["status"] == "1") { //nowa
            $oModelOrderPayment->setDateIsStarting($sOrderId, $sSessionId, $nAmount);
          } else if ($aPaymentStatus["status"] == "2") { //anulowana
          } else if ($aPaymentStatus["status"] == "3") { //odrzucona
          } else if ($aPaymentStatus["status"] == "4") { //rozpoczęta
          } else if ($aPaymentStatus["status"] == "5") { //oczekuje na odbiór
          } else if ($aPaymentStatus["status"] == "7") { //odrzucona
          } else if ($aPaymentStatus["status"] == "99") { //zakończona
            $oDb = Zend_Db_Table::getDefaultAdapter();
            try {
              if (!@is_dir($sPathNew))
                if (!@mkdir($sPathNew, 0777))
                  throw new Zend_Exception();
              if (!@is_dir($sPathOld))
                throw new Zend_Exception();
              $oDb->beginTransaction();
              if ($oModelOrderPayment->getDateIsEnding($sOrderId, $sSessionId, $nAmount) !== 1) {
                $oOrderJournal = $oModelVOrderPaymentHistory->getOrderJournal($nOrderPaymentId);
                foreach ($oOrderJournal as $oRow) {
                  $nOrderJournalId = $oRow->order_journal_id;
                  if (!is_dir($sPathNew . "user_id_" . $nUserId))
                    if (!mkdir($sPathNew . "user_id_" . $nUserId, 0777))
                      throw new Zend_Exception();
                  $oOrderFile = $oModelOrderFile->getRow($oRow->order_file_id);
                  if (!copy($sPathOld . $oOrderFile->name, $sPathNew . "user_id_" . $nUserId . "/" . $oOrderFile->name))
                    throw new Zend_Exception();
                  $oModelOrderJournal->setOrderPaymentSuccess($nOrderJournalId);
                  $oMail = new AppCms2_Controller_Plugin_Mail();
                  $oMail->sendBorrowerOrderStatusInfo($nOrderJournalId, 5);
                }
                $oModelOrderPayment->setDateIsEnding($sOrderId, $sSessionId, $nAmount);
                $nOrderCartId = $oModelOrderCart->getOrderCartId($nUserId);
                $oModelOrderJournalOrderCart->deleteCartJournals($nOrderCartId);
                $oDb->commit();
              }
            } catch (Zend_Exception $e) {
              $oDb->rollBack();
            }
          } else if ($aPaymentStatus["status"] == "888") { //błędny status
          }
        }
      }
    }
    echo "OK";
    exit;
  }

  public function ordernumberAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $oMail = new AppCms2_Controller_Plugin_Mail();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $sHash = md5($aPostData["merchant_id"] . $aPostData["amount"] . $aPostData["descr"] . $aPostData["order_id"] . $aPostData["product_id"] . $aPostData["user_id"] . $aPostData["url"] . $aPostData["restricted"] . $aPostData["time"] . $this->_sAuthKey2);
      if ($sHash === $aPostData["hash"]) {
        $oModelVOrderJournal = new Borrower_Model_VOrderJournal();
        $oModelOrderCart = new Borrower_Model_OrderCart();
        $oModelOrderJournalOrderCart = new Borrower_Model_OrderJournalOrderCart();
        $oModelOrderPayment = new User_Model_OrderPayment();
        $oModelOrderPaymentHistory = new User_Model_OrderPaymentHistory();
        $oModelOrderJournal = new User_Model_OrderJournal();
        $oGenereteSessionId = new AppCms2_GenereteSessionId();
        $oModelUser = new Admin_Model_User();
        $oDb = Zend_Db_Table::getDefaultAdapter();
        try {
          $oDb->beginTransaction();
          $nUserId = (int)$aPostData["user_id"];
          $nAmount = (int)$aPostData["amount"];
          $oUserOrderJournal = $oModelVOrderJournal->getUserOrderJournal($nUserId);
          $nOrderCartId = $oModelOrderCart->getOrderCartId($nUserId); //pobiera id koszyka użytkownika
          foreach ($oUserOrderJournal as $oValue) { //sprawdza czy na pewno wszystkie zamówienia znajdują się w koszyku
            $bIsExists = $oModelOrderJournalOrderCart->findOrderJournal($oValue->id, $nOrderCartId);
            if (!$bIsExists)
              $oModelOrderJournalOrderCart->addOrderJournalOrderCart(array("order_journal_id" => $oValue->id, "order_cart_id" => $nOrderCartId));
          }
          $oCart = $oModelOrderJournalOrderCart->getCartJournals($nOrderCartId); //pobiera id czasopism znajdujących się w koszyku
          $nCartCount = $oCart->count();
          foreach ($oCart as $oValue) {
            $sPaymentDescr .= $oValue->order_journal_id;
            if ($nCartCount > 1)
              $sPaymentDescr .= "; ";
          }
          $sPaymentDescr = trim($sPaymentDescr);
          $aUserParam = $oModelUser->findUser($nUserId);
          $aPaymentParam = $oGenereteSessionId->generatePaymentParam();
          $aPaymentParam["user_id"] = $nUserId;
          $aPaymentParam["amount"] = $nAmount;
          $aPaymentParam["descr"] = "Zamówienie nr " . $sPaymentDescr;
          $aPaymentParam["first_name"] = $aUserParam["first_name"];
          $aPaymentParam["last_name"] = $aUserParam["last_name"];
          $aPaymentParam["email_address"] = $aUserParam["email_address"];
          $aPaymentParam["is_starting"] = 1;
          $aPaymentParam["date_is_starting"] = time();
          $aPaymentParam["payment_type"] = 2;
          $nOrderPaymentId = $oModelOrderPayment->addRow($aPaymentParam); //tworzy nowy rekord w tabeli płatności i zwraca jego id
          $oModelOrderJournal->setOrderPaymentId($oCart, $nOrderPaymentId); //łączy czasopismo z płatnością
          $oModelOrderPaymentHistory->setOrderPaymentHistory($oCart, $nOrderPaymentId); //dodaje informacje do tabeli z historią płatności
          echo $nOrderPaymentId;
          $oDb->commit();
        } catch (Zend_Exception $e) {
          $oDb->rollBack();
        }
      }
    }
    exit;
  }

  public function statusAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    if ($this->_request->isPost()) {
      $aPostData = $this->_request->getPost();
      $sHash = md5($aPostData["transaction_id"] . $aPostData["merchant_id"] . $aPostData["order_id"] . $aPostData["product_id"] . $aPostData["user_id"] . $aPostData["amount"] . $aPostData["descr"] . $aPostData["client_ip"] . $aPostData["time"] . $this->_sAuthKey2);
      if ($sHash === $aPostData["hash"]) {
        $oModelOrderPayment = new User_Model_OrderPayment();
        $oModelOrderJournal = new User_Model_OrderJournal();
        $oModelOrderFile = new User_Model_OrderFile();
        $oModelOrderCart = new Borrower_Model_OrderCart();
        $oModelOrderJournalOrderCart = new Borrower_Model_OrderJournalOrderCart();
        $oModelVOrderPaymentHistory = new User_Model_VOrderPaymentHistory();
        $sPathOld = APPLICATION_PATH . "/../files_scanned/";
        $sPathNew = APPLICATION_PATH . "/../../public_html/files_scanned/";
        $nOrderPaymentId = (int)$aPostData["order_id"];
        $nUserId = (int)$aPostData["user_id"];
        $nAmount = (int)$aPostData["amount"];
        $oOrderPayment = $oModelOrderPayment->getRow($nOrderPaymentId);
        if ($oOrderPayment->user_id === $nUserId && $oOrderPayment->amount === $nAmount && $oOrderPayment->is_ending !== 1) {
          $oDb = Zend_Db_Table::getDefaultAdapter();
          try {
            if (!@is_dir($sPathNew))
              if (!@mkdir($sPathNew, 0777))
                throw new Zend_Exception();
            if (!@is_dir($sPathOld))
              throw new Zend_Exception();
            $oDb->beginTransaction();
            $oOrderJournal = $oModelVOrderPaymentHistory->getOrderJournal($nOrderPaymentId);
            foreach ($oOrderJournal as $oRow) {
              $nOrderJournalId = $oRow->order_journal_id;
              if (!@is_dir($sPathNew . "user_id_" . $nUserId))
                if (!@mkdir($sPathNew . "user_id_" . $nUserId, 0777))
                  throw new Zend_Exception();
              $oOrderFile = $oModelOrderFile->getRow($oRow->order_file_id);
              if (!@copy($sPathOld . $oOrderFile->name, $sPathNew . "user_id_" . $nUserId . "/" . $oOrderFile->name))
                throw new Zend_Exception();
              $oModelOrderJournal->setOrderPaymentSuccess($nOrderJournalId);
              $oMail = new AppCms2_Controller_Plugin_Mail();
              $oMail->sendBorrowerOrderStatusInfo($nOrderJournalId, 5);
            }
            $oModelOrderPayment->editRow($nOrderPaymentId, array("is_ending" => 1, "date_is_ending" => time()));
            $nOrderCartId = $oModelOrderCart->getOrderCartId($nUserId);
            $oModelOrderJournalOrderCart->deleteCartJournals($nOrderCartId);
            $oDb->commit();
            echo "ACK";
          } catch (Zend_Exception $e) {
            $oDb->rollBack();
          }
        }
      }
    }
    exit;
  }
}

?>
