<?php

iconv_set_encoding("internal_encoding", "UTF-8");

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

  protected function __initSession()
  {

  }

  protected function _initAutoload()
  {
    $aDefaultResource = array(
      "namespace" => "",
      "basePath" => APPLICATION_PATH,
      "resourceTypes" => array(
        "forms" => array("path" => "forms/", "namespace" => "Form_"),
        "models" => array("path" => "models/", "namespace" => "Model_")
      )
    );
    $aBorrowerResource = array(
      "namespace" => "Borrower_",
      "basePath" => APPLICATION_PATH . "/modules/borrower",
      "resourceTypes" => array(
        "forms" => array("path" => "forms/", "namespace" => "Form_"),
        "models" => array("path" => "models/", "namespace" => "Model_"),
      )
    );
    $aUserResource = array(
      "namespace" => "User_",
      "basePath" => APPLICATION_PATH . "/modules/user",
      "resourceTypes" => array(
        "forms" => array("path" => "forms/", "namespace" => "Form_"),
        "models" => array("path" => "models/", "namespace" => "Model_"),
      )
    );
    $aAdminResource = array(
      "namespace" => "Admin_",
      "basePath" => APPLICATION_PATH . "/modules/admin",
      "resourceTypes" => array(
        "forms" => array("path" => "forms/", "namespace" => "Form_"),
        "models" => array("path" => "models/", "namespace" => "Model_"),
      )
    );
    //new Zend_Loader_Autoloader_Resource($aDefaultResource);
    new Zend_Loader_Autoloader_Resource($aBorrowerResource);
    new Zend_Loader_Autoloader_Resource($aUserResource);
    new Zend_Loader_Autoloader_Resource($aAdminResource);
    $oAutoLoader = Zend_Loader_Autoloader::getInstance();
    $oAutoLoader->registerNamespace("AppCms2_");
    $oAutoLoader->registerNamespace("Facebook_");
    return $oAutoLoader;
  }

  protected function _initPlugins()
  {
    $oFrontController = Zend_Controller_Front::getInstance();
    $oFrontController->registerPlugin(new AppCms2_Controller_Plugin_ModuleNavigation());
    //$oFrontController->registerPlugin(new AppCms2_Controller_Plugin_Site());
  }

  protected function _initRegistry()
  {
    Zend_Registry::set("db", $this->getResource("db"));
    Zend_Registry::set("config", new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV));
  }

  protected function _initRoutes()
  {
    $oFrontController = Zend_Controller_Front::getInstance();
    $oRouter = $oFrontController->getRouter();
    $oRouter->addRoute("lmca", new Zend_Controller_Router_Route(
        "/:lang/:module/:controller/:action/*", array("lang" => ":lang", "module" => ":module", "controller" => ":controller", "action" => ":action"), array("lang" => "[a-z]{2}", "module" => "(default|admin|borrower|user)"))
    );
    $oRouter->addRoute("mca", new Zend_Controller_Router_Route(
        "/:module/:controller/:action/*", array("module" => ":module", "controller" => ":controller", "action" => ":action"), array("module" => "(default|admin|borrower|user)"))
    );
    $oRouter->addRoute("lmc", new Zend_Controller_Router_Route(
        "/:lang/:module/:controller", array("lang" => ":lang", "module" => ":module", "controller" => ":controller", "action" => "index"), array("lang" => "[a-z]{2}", "module" => "(default|admin|borrower|user)"))
    );
    $oRouter->addRoute("mc", new Zend_Controller_Router_Route(
        "/:module/:controller", array("module" => ":module", "controller" => ":controller", "action" => "index"), array("module" => "(default|admin|borrower|user)"))
    );
    $oRouter->addRoute("ls", new Zend_Controller_Router_Route(
        "/:lang/:site", array("lang" => ":lang", "site" => ":site", "module" => "default", "controller" => "index", "action" => "index"), array("lang" => "[a-z]{2}", "site" => "[a-zA-Z0-9\-\,\.]{3,}"))
    );
    $oRouter->addRoute("lm", new Zend_Controller_Router_Route(
        "/:lang/:module", array("lang" => ":lang", "module" => ":module", "controller" => "index", "action" => "index"), array("lang" => "[a-z]{2}", "module" => "(default|admin|borrower|user)"))
    );
    $oRouter->addRoute("l", new Zend_Controller_Router_Route(
        "/:lang", array("lang" => ":lang", "module" => "default", "controller" => "index", "action" => "index"), array("lang" => "[a-z]{2}"))
    );
    $oRouter->addRoute("s", new Zend_Controller_Router_Route(
        "/:site", array("site" => ":site", "module" => "default", "controller" => "index", "action" => "index"), array("site" => "[a-zA-Z0-9\-\,\.]{3,}"))
    );
    $oRouter->addRoute("m", new Zend_Controller_Router_Route(
        "/:module", array("module" => ":module", "controller" => "index", "action" => "index"), array("module" => "(default|admin|borrower|user)"))
    );
    $oRouter->addRoute("default", new Zend_Controller_Router_Route(
        "/", array("module" => "default", "controller" => "index", "action" => "index"))
    );
  }

  protected function _initTranslation()
  {
    $oSiteSession = new Zend_Session_Namespace("site");
    $sRequestLang = null;
    if (!isset($sRequestLang)) {
      if ($oSiteSession->lang == "en"):
        $sRequestLang = $oSiteSession->lang;
      else:
        $oLocale = new Zend_Locale();
        $sLang = $oLocale->getLanguage();
        if (in_array($sLang, array("pl", "en"))):
          $this->_sBrowserLanguage = $sLang;
          $sRequestLang = $sLang;
        else:
          $sRequestLang = "pl";
        endif;
      endif;
      $oSiteSession->lang = $sRequestLang;
    } else if (in_array($sRequestLang, array("pl", "en"))) {
      $oSiteSession->lang = $sRequestLang;
    } else {
      $sRequestLang = "pl";
      $oSiteSession->lang = $sRequestLang;
    }
    if ($sRequestLang == "pl") {
      $sArrayFilePath = APPLICATION_PATH . "/resources/languages/" . $sRequestLang . "/Zend_Validate.php";
      if (file_exists($sArrayFilePath)) {
        $aTranslate = array(
          "adapter" => "array",
          "content" => $sArrayFilePath,
          "locale" => $sRequestLang,
          "scan" => Zend_Translate::LOCALE_DIRECTORY
        );
        $oTranslator = new Zend_Translate($aTranslate);
        Zend_Validate_Abstract::setDefaultTranslator($oTranslator);
      }
    }
    if ($sRequestLang == "en") {
      $sArrayFilePath = APPLICATION_PATH . "/resources/languages/" . $sRequestLang . "/" . $sRequestLang . "_" . strtoupper($sRequestLang) . ".php";
      if (file_exists($sArrayFilePath)) {
        $aTranslate = array(
          "adapter" => "array",
          "content" => $sArrayFilePath,
          "locale" => $sRequestLang,
          "scan" => Zend_Translate::LOCALE_DIRECTORY
        );
        $oTranslator = new Zend_Translate($aTranslate);
        Zend_Registry::set("Zend_Translate", $oTranslator);
        Zend_Form::setDefaultTranslator($oTranslator);
        Zend_Validate_Abstract::setDefaultTranslator(null);
      }
    }
    return $sRequestLang;
  }

  protected function _initView()
  {
    $sOptions = $this->getOptions();
    $aConfig = array(
      "baseHost" => $sOptions["resources"]["frontController"]["baseHost"],
      "baseUrl" => $sOptions["resources"]["frontController"]["baseUrl"],
      "subDomain" => $sOptions["resources"]["frontController"]["subDomain"]
    );
    $this->bootstrap("layout");
    $layout = $this->getResource("layout");
    $oView = $layout->getView();
    $oView->sHost = $aConfig["baseHost"];
    $oView->sBaseUrl = $aConfig["baseUrl"];
    $oView->sSubDomain = $aConfig["subDomain"];
    $oView->doctype("HTML5");
    $oView->setEncoding("UTF-8");
    $oView->headMeta()->setCharset("UTF-8");
    $oView->headMeta()->appendName("robots", "all");
    $oView->headMeta()->appendName("author", "lstaszak");
    $oView->headLink()->headLink(array("rel" => "shortcut icon", "href" => "http://bg.pw.edu.pl/templates/favourite/favicon.ico", "type" => "image/x-icon"));

    $oView->headScript()->appendFile("//code.jquery.com/jquery-2.0.0.js");
    $oView->headScript()->appendFile("//code.jquery.com/jquery-migrate-1.1.0.min.js");
    $oView->headScript()->appendFile("//code.jquery.com/ui/1.10.3/jquery-ui.js");
    $oView->headScript()->appendFile("//www.yetipay.pl/payments/js/244/yetixd.js");
    $oView->headLink()->appendStylesheet("//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.min.css");

    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery/jquery.md5.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery/jquery.actual.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery/jquery.nivo.slider.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery/jquery.prettyPhoto.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery/jquery.transit.min.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery/jquery.zend.jsonrpc.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery/json2.js");

    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery-ui/select2.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery-ui/chosen.jquery.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery-ui/jquery.ui.selectmenu.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery-ui/jquery.ui.multiselect.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/jquery-ui/jquery.ui.datepicker-pl.js");

    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/ckeditor/ckeditor.js");

    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/syntaxhighlighter/shCore.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/syntaxhighlighter/shAutoloader.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/syntaxhighlighter/shBrushSql.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/syntaxhighlighter/shBrushCss.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/syntaxhighlighter/shBrushPhp.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/syntaxhighlighter/shBrushXml.js");

    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/translator.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/table_image/table_image.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/table_user/table_user.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/table_order/table_order_borrower.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/table_order/table_order_user.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/table_payment/table_payment.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/rpc_order_settings/rpc_order_settings.js");

    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/form_pos_left.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/validator_ajax.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/ajax_overlay.js");
    $oView->headScript()->appendFile($oView->sBaseUrl . "/js/html5shiv.js");

    $aDefaultStylesheet = array("breadcrumbs.css", "chosen.css", "jquery.ui.fileupload.css", "jquery.ui.selectmenu.css", "main.css", "prettyPhoto.css", "jquery.ui.multiselect.css", "select2.css");
    $sFolderName = "globalCss";
    foreach ($aDefaultStylesheet as $sValue) {
      $oView->headLink()->appendStylesheet($oView->sBaseUrl . "/skins/default/css/$sFolderName/" . $sValue);
    }
  }

  protected function _initViews()
  {

  }

}
