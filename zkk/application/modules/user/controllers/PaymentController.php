<?php

class User_PaymentController extends Zend_Controller_Action
{

  private $_oAuth;
  private $_nUserId = null;
  private $_sRoleName = null;
  private $_sSiteUrl = null;

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

  public function init()
  {
    $this->_helper->layout()->setLayout("user/layout");
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
    $oModelVOrderPaymentHistory = new User_Model_VOrderPaymentHistory();
    $oRowset = $oModelVOrderPaymentHistory->getUserPayments($aFilter, $nNumRowPerPage, ($nCurrPage - 1) * $nNumRowPerPage, $sSortColumn . " " . $sSortMethod);
    $nNumRows = $oModelVOrderPaymentHistory->getUserPayments($aFilter)->count();
    $aJson = array("rowset" => $oRowset->toArray(), "num_rows" => $nNumRows);
    header("Content-type: application/json");
    echo Zend_Json::encode($aJson);
    exit;
  }

}

?>
