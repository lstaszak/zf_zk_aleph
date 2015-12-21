<?php

class Zend_View_Helper_LoadDebug extends Zend_View_Helper_Abstract
{

  public function loadDebug()
  {
    $oDb = Zend_Db_Table::getDefaultAdapter();
    $nNumQueries = $oDb->getProfiler()->getTotalNumQueries();
    $oQueryProfiles = $oDb->getProfiler()->getQueryProfiles();
    $this->view->nNumQueries = $nNumQueries;
    $this->view->oQueryProfiles = $oQueryProfiles;
    return $this->view->render('_helpers/loaddebug.phtml');
  }

}

?>
