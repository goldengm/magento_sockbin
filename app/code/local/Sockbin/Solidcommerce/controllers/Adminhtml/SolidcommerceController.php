<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * solidcommerce controller
 *
 * @category   Mage
 * @package    Mage_Solidcommerce
 */
class Sockbin_Solidcommerce_Adminhtml_SolidcommerceController extends  Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Mage_Adminhtml_SolidcommerceController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('catalog/system_solidcommerce')
            ->_addBreadcrumb(
                Mage::helper('catalog')->__('Catalog'),
                Mage::helper('catalog')->__('Catalog'))
            ->_addBreadcrumb(
                Mage::helper('solidcommerce')->__('Solidcommerce'),
                Mage::helper('solidcommerce')->__('Solidcommerce'))
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Solid Commerce Product Feeds'));
        
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/solidcommerce'))
            ->renderLayout();
    }

    /**
     * Create new solidcommerce
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit solidcommerce
     */
    public function editAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Solid Commerce Product Feeds'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('solidcommerce_id');
        $model = Mage::getModel('solidcommerce/solidcommerce');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('solidcommerce')->__('This solid commerce feed no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getSolidcommerceFilename() : $this->__('New Solid Commerce Feed'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('solidcommerce_solidcommerce', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('solidcommerce')->__('Edit Solid Commerce Feed') : Mage::helper('solidcommerce')->__('New Solid Commerce Feed'),
                $id ? Mage::helper('solidcommerce')->__('Edit Solid Commerce Feed') : Mage::helper('solidcommerce')->__('New Solid Commerce Feed'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/solidcommerce_edit'))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // init model and set data
            $model = Mage::getModel('solidcommerce/solidcommerce');

            //validate path to generate
            if (!empty($data['solidcommerce_filename']) && !empty($data['solidcommerce_path'])) {
                $path = rtrim($data['solidcommerce_path'], '\\/')
                      . DS . $data['solidcommerce_filename'];
                /** @var $validator Mage_Core_Model_File_Validator_AvailablePath */
                $validator = Mage::getModel('core/file_validator_availablePath');
                /** @var $helper Mage_Adminhtml_Helper_Catalog */
                $helper = Mage::helper('solidcommerce/catalog');
                $validator->setPaths($helper->getSolidcommerceValidPaths());
                if (!$validator->isValid($path)) {
                    foreach ($validator->getMessages() as $message) {
                        Mage::getSingleton('adminhtml/session')->addError($message);
                    }
                    // save data in session
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    // redirect to edit form
                    $this->_redirect('*/*/edit', array(
                        'solidcommerce_id' => $this->getRequest()->getParam('solidcommerce_id')));
                    return;
                }
            }

            if ($this->getRequest()->getParam('solidcommerce_id')) {
                $model ->load($this->getRequest()->getParam('solidcommerce_id'));

                if ($model->getSolidcommerceFilename() && file_exists($model->getPreparedFilename())){
                    unlink($model->getPreparedFilename());
                }
            }

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('solidcommerce')->__('The feed has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('solidcommerce_id' => $model->getId()));
                    return;
                }
                // go to grid or forward to generate action
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('solidcommerce_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array(
                    'solidcommerce_id' => $this->getRequest()->getParam('solidcommerce_id')));
                return;
            }
        }
        $this->_redirect('*/*/');

    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('solidcommerce_id')) {
            try {
                // init model and delete
                $model = Mage::getModel('solidcommerce/solidcommerce');
                $model->setId($id);
                // init and load solidcommerce model

                /* @var $solidcommerce Mage_Solidcommerce_Model_Solidcommerce */
                $model->load($id);
                // delete file
                if ($model->getSolidcommerceFilename() && file_exists($model->getPreparedFilename())){
                    unlink($model->getPreparedFilename());
                }
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('solidcommerce')->__('The feed has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('solidcommerce_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('solidcommerce')->__('Unable to find a feed to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Generate solidcommerce
     */
    public function generateAction()
    {
        // init and load solidcommerce model
        $id = $this->getRequest()->getParam('solidcommerce_id');
        $solidcommerce = Mage::getModel('solidcommerce/solidcommerce');
        /* @var $solidcommerce Mage_Solidcommerce_Model_Solidcommerce */
        $solidcommerce->load($id);
        // if solidcommerce record exists
        if ($solidcommerce->getId()) {
            try {
                $solidcommerce->generateXml();

                $this->_getSession()->addSuccess(
                    Mage::helper('solidcommerce')->__('The feed "%s" has been generated.', $solidcommerce->getSolidcommerceFilename()));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('solidcommerce')->__('Unable to generate the feed.'));
            }
        } else {
            $this->_getSession()->addError(
                Mage::helper('solidcommerce')->__('Unable to find a feed to generate.'));
        }

        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/solidcommerce');
    }
}
