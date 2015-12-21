<?php

class Zend_View_Helper_LoadSkin extends Zend_View_Helper_Abstract
{
  public function loadSkin($sSkin)
  {
    $oSkinData = new Zend_Config_Xml("./skins/" . $sSkin . "/style.xml");
    if ($oSkinData->stylesheets->stylesheet instanceof Zend_Config) {
      $aStylesheets = $oSkinData->stylesheets->stylesheet->toArray();
      if (is_array($aStylesheets))
        foreach ($aStylesheets as $sStylesheet)
          $this->view->headLink()->appendStylesheet($this->view->baseUrl("/skins/" . $sSkin . "/css/" . $sStylesheet));
    } else {
      $sStylesheet = $oSkinData->stylesheets->stylesheet;
      if (is_string($sStylesheet))
        $this->view->headLink()->appendStylesheet($this->view->baseUrl("/skins/" . $sSkin . "/css/" . $sStylesheet));
    }
  }
}

?>
