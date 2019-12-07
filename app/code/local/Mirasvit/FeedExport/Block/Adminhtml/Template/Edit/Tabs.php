<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.11
 * @build     742
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Block_Adminhtml_Template_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('feedexport')->__('Template Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => Mage::helper('feedexport')->__('Template Information'),
            'title'   => Mage::helper('feedexport')->__('Template Information'),
            'content' => $this->getLayout()->createBlock('feedexport/adminhtml_template_edit_tab_general')->toHtml(),
        ));

        $this->addTab('csv_section', array(
            'label'   => Mage::helper('feedexport')->__('Content Settings'),
            'title'   => Mage::helper('feedexport')->__('Content Settings'),
            'content' => $this->getLayout()->createBlock('feedexport/adminhtml_template_edit_tab_content_csv')->toHtml(),
        ));

        $this->addTab('xml_section', array(
            'label'   => Mage::helper('feedexport')->__('Content Settings'),
            'title'   => Mage::helper('feedexport')->__('Content Settings'),
            'content' => $this->getLayout()->createBlock('feedexport/adminhtml_template_edit_tab_content_xml')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}