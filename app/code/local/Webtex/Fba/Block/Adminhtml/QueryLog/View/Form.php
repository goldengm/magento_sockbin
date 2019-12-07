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

class Webtex_Fba_Block_Adminhtml_QueryLog_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $queryId = $this->getRequest()->getParam('id');
        /** @var $query Webtex_Fba_Model_Mws_Query */
        $query = Mage::getModel('mws/query')->load($queryId);

        $view = new Varien_Object();

        $view->setRequest($query->getPlainRequest());
        $view->setResponse($query->getPlainResponse());
        $view->setMessage($query->getErrorMessage());


        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('query_info', array(
            'legend' => Mage::helper('fba')->__('Amazon Call Info')
        ));

        $fieldset->addType('webtex_xml', 'Webtex_Fba_Block_Adminhtml_QueryLog_View_Element_Xml');

        $fieldset->addField('request', 'textarea', array(
            'name' => 'request',
            'label' => Mage::helper('fba')->__('Request'),
            'style' => 'width:500px; height:250px;'
        ));
        $fieldset->addField('response', 'webtex_xml', array(
            'name' => 'response',
            'label' => Mage::helper('fba')->__('Response'),
            'style' => 'width:500px; height:250px;'
        ));
        $fieldset->addField('message', 'textarea', array(
            'name' => 'message',
            'label' => Mage::helper('fba')->__('Additional Message'),
        ));


        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setValues($view->getData());

        $this->setForm($form);
    }
}