<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information,
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_Fba
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

class Webtex_Fba_Adminhtml_MarketplaceController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_PageController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('fba_tab');
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_title($this->__('Amazon Marketplaces'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('fba/adminhtml_marketplace_grid ')->toHtml()
        );
    }

    public function editAction()
    {
        $this->_title('Amazon')
            ->_title('Marketplaces')
            ->_title('Manage Settings');

        // 1. Get marketplace id and create marketplace model

        $marketplaceId = $this->getRequest()->getParam('id');
        $marketplaceModel = Mage::getModel('mws/marketplace');

        // 2. Initial checking

        if ($marketplaceId) {
            $marketplaceModel->load($marketplaceId);
            if (!$marketplaceModel->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fba')->__('This marketplace no longer exist'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // 3. Set entered data if was error when we do save

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data))
            $marketplaceModel->setData($data);

        // 4. Register model to use later in blocks

        Mage::register('mws_marketplace', $marketplaceModel);

        // 5. Build edit Form
        $this->_initAction()
            ->_addBreadcrumb(
                $marketplaceId ? Mage::helper('fba')->__('Edit Marketplace')
                    : Mage::helper('fba')->__('New Marketplace'),
                $marketplaceId ? Mage::helper('fba')->__('Edit Marketplace')
                    : Mage::helper('fba')->__('New Marketplace'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                /** @var $marketplaceModel Webtex_Fba_Model_Mws_Marketplace */
                $marketplaceModel = Mage::getModel('mws/marketplace');

                $data = $this->getRequest()->getPost();

                if (isset($data['id']))
                    $marketplaceModel->load($data['id']);

                $shippingData = $data['shipping'];

                $marketplaceModel->setData($data)->save();

                //1 - edit shipping methods
                if (isset($shippingData)) {
                    foreach (Mage::getModel('fba/config_source_shippingType')->getMethods() as $method) {
                        /** @var $methodModel Webtex_Fba_Model_Mws_Shipping */
                        $methodModel = $marketplaceModel->getShippingSettings($method['code']);
                        if (!$methodModel)
                            $methodModel = Mage::getModel('mws/shipping');

                        if (isset($shippingData[$method['code']])) {
                            if (isset($shippingData[$method['code']]['rules']))
                                $shippingData[$method['code']]['rules'] = serialize($shippingData[$method['code']]['rules']);
                            $methodModel->setData($shippingData[$method['code']]);
                            unset($data['shipping'][$method['code']]);

                        }

                        if (!$methodModel->getId())
                            $methodModel = $methodModel->setFbaMarketplaceId($marketplaceModel->getId())->setType($method['code']);
                        $methodModel->save();
                    }
                    unset($data['shipping']);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fba')->__('The Marketplace has been saved.')
                );
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $marketplaceModel->getId()));
                    if ($activeTabId = (string)$this->getRequest()->getParam('active_tab')) {
                        Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
                    }
                    return;
                }
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('fba')->__('An error occurred while saving the marketplace data. Please review the log and try again.')
                );
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('mws/marketplace');
                $model->load($id);


                foreach ($productCollection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect('fba_marketplace_id')
                    ->addAttributeToFilter('fba_marketplace_id', $model->getId()) as $product) {
                    $product->setFbaMarketplaceId(0);
                    $product->getResource()->saveAttribute($product, 'fba_marketplace_id');
                }


                $model->delete();


                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fba')->__('The marketplace has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('fba')->__('An error occurred while deleting the marketplace. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fba')->__('Unable to find a marketplace to delete.')
        );
        $this->_redirect('*/*/');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function duplicateAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('mws/marketplace');
                $model->load($id);
                if ($model->getId())
                    $newMarketplace = $model->duplicate();
                else
                    $this->_getSession()->addError(
                        Mage::helper('fba')->__('Unable to find a marketplace to duplicate.')
                    );

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fba')->__('The marketplace has been duplicated.')
                );
                $this->_redirect('*/*/edit', array('id' => $newMarketplace->getId()));
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('fba')->__('An error occurred while duplicating the marketplace. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fba')->__('Unable to find a marketplace to duplicate.')
        );
        $this->_redirect('*/*/');
    }

    public function massSyncInventoryAction()
    {
        $marketplacesIds = (array)$this->getRequest()->getParam('marketplace');

        if (count($marketplacesIds))
            Mage::getResourceModel('mws/marketplace')->syncInventoryByMarketplace($marketplacesIds);


        Mage::app()->getResponse()->setRedirect($this->getUrl('*/*/'));
    }

    public function massDeleteAction()
    {
        $marketplacesIds = (array)$this->getRequest()->getParam('marketplace');
        if (count($marketplacesIds)) {
            try {
                $marketplaceCollection = Mage::getModel('mws/marketplace')->getCollection()->addFieldToFilter('id', array('in' => $marketplacesIds));
                foreach ($marketplaceCollection as $marketplace) {
                    foreach ($productCollection = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('fba_marketplace_id')
                        ->addAttributeToFilter('fba_marketplace_id', $marketplace->getId()) as $product) {
                        $product->setFbaMarketplaceId(0);
                        $product->getResource()->saveAttribute($product, 'fba_marketplace_id');
                    }

                    foreach (Mage::getModel('mws/product')->getCollection()->addFieldToFilter('fba_marketplace_id', $marketplace->getId()) as $product)
                        $product->delete();

                    $marketplace->delete();
                }


                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fba')->__('The marketplaces has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('fba')->__('An error occurred while deleting the marketplace. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/');
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fba')->__('Unable to find a marketplaces to delete.')
        );
        $this->_redirect('*/*/');
    }

    public function massDisableAction()
    {
        $marketplacesIds = (array)$this->getRequest()->getParam('marketplace');
        if (count($marketplacesIds)) {
            try {
                $marketplaceCollection = Mage::getModel('mws/marketplace')->getCollection()->addFieldToFilter('status', array('neq' => 0))->addFieldToFilter('id', array('in' => $marketplacesIds));
                foreach ($marketplaceCollection as $marketplace)
                    $marketplace->setStatus(0)->save();


                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fba')->__('The marketplaces has been disabled.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('fba')->__('An error occurred while disabling the marketplace. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/');
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fba')->__('Unable to find a marketplaces to disable.')
        );
        $this->_redirect('*/*/');
    }

    public function massEnableAction()
    {
        $marketplacesIds = (array)$this->getRequest()->getParam('marketplace');
        if (count($marketplacesIds)) {
            try {
                $marketplaceCollection = Mage::getModel('mws/marketplace')->getCollection()->addFieldToFilter('status', array('neq' => 1))->addFieldToFilter('id', array('in' => $marketplacesIds));
                foreach ($marketplaceCollection as $marketplace)
                    $marketplace->setStatus(1)->save();


                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fba')->__('The marketplaces has been enabled.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('fba')->__('An error occurred while enabling the marketplace. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/');
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fba')->__('Unable to find a marketplaces to enable.')
        );
        $this->_redirect('*/*/');
    }

    public function massSyncOrdersAction()
    {
        $marketplacesIds = (array)$this->getRequest()->getParam('marketplace');

        if (count($marketplacesIds))
            Mage::getResourceModel('mws/marketplace')->syncOrdersByMarketplace($marketplacesIds);


        Mage::app()->getResponse()->setRedirect($this->getUrl('*/*/'));
    }

    public function massRecalculateBlockedQtyAction()
    {
        $marketplacesIds = (array)$this->getRequest()->getParam('marketplace');

        foreach ($marketplacesIds as $marketplacesId) {
            Mage::getModel('mws/marketplace')->load($marketplacesId)->recalculateBlockedQty();
        }

        Mage::app()->getResponse()->setRedirect($this->getUrl('*/*/'));
    }

}