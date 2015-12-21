<?php

class Zend_View_Helper_LoadMenu extends Zend_View_Helper_Abstract
{
  public function loadMenu($sName)
  {
    echo $this->view->navigation()->menu()->setUlClass($sName)->renderMenu(null, array("minDepth" => null, "maxDepth" => 0, "onlyActiveBranch" => true, "renderParents" => false));
  }
}

?>
