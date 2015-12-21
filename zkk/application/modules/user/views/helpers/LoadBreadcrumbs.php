<?php

class Zend_View_Helper_LoadBreadcrumbs extends Zend_View_Helper_Abstract
{

  public function loadBreadcrumbs()
  {
    $oBreadcrumbs = $this->view->navigation()->breadcrumbs()->setLinkLast(false)->setMinDepth(0)->setSeparator("<span class=\"breadcrumbs_spacer\">»</span>")->render();
    $this->view->oBreadcrumbs = $oBreadcrumbs;
    return $this->view->render("_helpers/breadcrumbs.phtml");
  }

}

?>
