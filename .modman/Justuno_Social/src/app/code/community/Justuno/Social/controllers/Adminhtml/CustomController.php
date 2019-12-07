<?php
class Justuno_Social_Adminhtml_CustomController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        Mage::getConfig()->saveConfig('justuno/account/embed', '', 'default', 0);
        Mage::getConfig()->saveConfig('justuno/account/email', '', 'default', 0);
        Mage::getConfig()->saveConfig('justuno/account/password', '', 'default', 0);
        Mage::getConfig()->saveConfig('justuno/account/domain', '', 'default', 0);

        Mage::getConfig()->saveConfig('justuno/register/embed', '', 'default', 0);
        Mage::getConfig()->saveConfig('justuno/register/email', '', 'default', 0);
        Mage::getConfig()->saveConfig('justuno/register/password', '', 'default', 0);
        Mage::getConfig()->saveConfig('justuno/register/domain', '', 'default', 0);
        Mage::getConfig()->saveConfig('justuno/register/phone', '', 'default', 0);

        Mage::getConfig()->cleanCache();
        
        $url = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/justuno");
        $this->_redirect('adminhtml/system_config/edit/section/justuno');
    }
 
    public function listAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('mycustomtab')
            ->_title($this->__('List Action'));
 
        $this->renderLayout();
    }
}