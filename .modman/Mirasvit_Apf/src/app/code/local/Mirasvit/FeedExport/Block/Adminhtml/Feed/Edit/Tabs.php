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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('feedexport')->__('Feed Information'));
    }

    protected function _beforeToHtml()
    {
        if (Mage::registry('current_model')->getId() > 0) {
            $this->addTab('general_section', array(
                'label'   => Mage::helper('feedexport')->__('Feed Information'),
                'title'   => Mage::helper('feedexport')->__('Feed Information'),
                'content' => $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_general')->toHtml(),
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

            $this->addTab('filter_section', array(
                'label'   => Mage::helper('feedexport')->__('Filters'),
                'title'   => Mage::helper('feedexport')->__('Filters'),
                'content' => $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_rules')->toHtml(),
            ));

            $this->addTab('ga_section', array(
                'label'   => Mage::helper('feedexport')->__('Google Analytics'),
                'title'   => Mage::helper('feedexport')->__('Google Analytics'),
                'content' => $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_ga')->toHtml(),
            ));

            $this->addTab('cron_section', array(
                'label'   => Mage::helper('feedexport')->__('Scheduled Task'),
                'title'   => Mage::helper('feedexport')->__('Scheduled Task'),
                'content' => $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_cron')->toHtml(),
            ));

            $this->addTab('ftp_section', array(
                'label'   => Mage::helper('feedexport')->__('FTP Settings'),
                'title'   => Mage::helper('feedexport')->__('FTP Settings'),
                'content' => $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_ftp')->toHtml(),
            ));

            $this->addTab('additional_section', array(
                'label'   => Mage::helper('feedexport')->__('Additional'),
                'title'   => Mage::helper('feedexport')->__('Additional'),
                'content' => $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_additional')->toHtml(),
            ));

            $this->addTab('history_section', array(
                'label'   => Mage::helper('feedexport')->__('History'),
                'title'   => Mage::helper('feedexport')->__('History'),
                'content' => $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_history')->toHtml(),
            ));

        } else {
            $this->addTab('general_section', array(
                'label'   => Mage::helper('feedexport')->__('Settings'),
                'title'   => Mage::helper('feedexport')->__('Settings'),
                'content' => $this->getLayout()->createBlock('feedexport/adminhtml_feed_edit_tab_new')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }
}