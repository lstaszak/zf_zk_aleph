<?php

class Zend_View_Helper_LoadSubMenu extends Zend_View_Helper_Abstract
{
  public function loadSubMenu($nDepth, $sName)
  {
    echo $this->view->navigation()->menu()->setUlClass($sName)->renderMenu(null, array("minDepth" => 1, "maxDepth" => $nDepth, "onlyActiveBranch" => true, "renderParents" => false));
  }
}

?>
