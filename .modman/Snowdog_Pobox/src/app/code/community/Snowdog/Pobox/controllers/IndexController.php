<?php

class Snowdog_Pobox_IndexController extends Mage_Core_Controller_Front_Action
{

    public function savepoboxAction()
    {
        $poBox = $this->getRequest()->getPost('poBox');
        Mage::getSingleton('core/session')->setPoBox($poBox);
    }
}