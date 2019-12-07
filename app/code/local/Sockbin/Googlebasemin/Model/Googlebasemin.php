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
 * @category    Sockbin
 * @package     Sockbin_Googlebasemin
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Googlebasemin model
 *
 * @method Sockbin_Googlebasemin_Model_Resource_Googlebasemin _getResource()
 * @method Sockbin_Googlebasemin_Model_Resource_Googlebasemin getResource()
 * @method string getGooglebaseminType()
 * @method Sockbin_Googlebasemin_Model_Googlebasemin setGooglebaseminType(string $value)
 * @method string getGooglebaseminFilename()
 * @method Sockbin_Googlebasemin_Model_Googlebasemin setGooglebaseminFilename(string $value)
 * @method string getGooglebaseminPath()
 * @method Sockbin_Googlebasemin_Model_Googlebasemin setGooglebaseminPath(string $value)
 * @method string getGooglebaseminTime()
 * @method Sockbin_Googlebasemin_Model_Googlebasemin setGooglebaseminTime(string $value)
 * @method int getStoreId()
 * @method Sockbin_Googlebasemin_Model_Googlebasemin setStoreId(int $value)
 *
 * @category    Sockbin
 * @package     Sockbin_Googlebasemin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sockbin_Googlebasemin_Model_Googlebasemin extends Mage_Core_Model_Abstract
{
    /**
     * Real file path
     *
     * @var string
     */
    protected $_filePath;

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('googlebasemin/googlebasemin');
    }

    protected function _beforeSave()
    {
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath(Mage::getBaseDir() . '/' . $this->getGooglebaseminPath());

        /**
         * Check path is allow
         */
        if (!$io->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException(Mage::helper('googlebasemin')->__('Please define correct path'));
        }
        /**
         * Check exists and writeable path
         */
        if (!$io->fileExists($realPath, false)) {
            Mage::throwException(Mage::helper('googlebasemin')->__('Please create the specified folder "%s" before saving the googlebasemin.', Mage::helper('core')->escapeHtml($this->getGooglebaseminPath())));
        }

        if (!$io->isWriteable($realPath)) {
            Mage::throwException(Mage::helper('googlebasemin')->__('Please make sure that "%s" is writable by web-server.', $this->getGooglebaseminPath()));
        }
        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getGooglebaseminFilename())) {
            Mage::throwException(Mage::helper('googlebasemin')->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }
        if (!preg_match('#\.txt$#', $this->getGooglebaseminFilename())) {
            $this->setGooglebaseminFilename($this->getGooglebaseminFilename() . '.txt');
        }

        $this->setGooglebaseminPath(rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $realPath), '/') . '/');

        return parent::_beforeSave();
    }

    /**
     * Return real file path
     *
     * @return string
     */
    protected function getPath()
    {
        if (is_null($this->_filePath)) {
            $this->_filePath = str_replace('//', '/', Mage::getBaseDir() .
                $this->getGooglebaseminPath());
        }
        return $this->_filePath;
    }

    /**
     * Return full file name with path
     *
     * @return string
     */
    public function getPreparedFilename()
    {
        return $this->getPath() . $this->getGooglebaseminFilename();
    }

    /**
     * Generate XML file
     *
     * @return Sockbin_Googlebasemin_Model_Googlebasemin
     */
    public function generateXml()
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));

        if ($io->fileExists($this->getGooglebaseminFilename()) && !$io->isWriteable($this->getGooglebaseminFilename())) {
            Mage::throwException(Mage::helper('googlebasemin')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getGooglebaseminFilename(), $this->getPath()));
        }

        $io->streamOpen($this->getGooglebaseminFilename());

        $io->streamWrite("id\ttitle\tdescription\tgoogle_product_category\tproduct_type\tlink\timage_link\tadditional_image_link\tcondition\tinventory\tprice\tshipping\tshipping_weight\tsku\n");

        $storeId = $this->getStoreId();
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        /**
         * Generate products googlebasemin
         */
        $collection = Mage::getResourceModel('googlebasemin/catalog_product')->getCollection($storeId);
        $products = new Varien_Object();
        $products->setItems($collection);
        Mage::dispatchEvent('googlebasemin_products_generating_before', array(
            'collection' => $products
        ));
        foreach ($products->getItems() as $item) {
            $csvRow = sprintf(
                "%s\t%s\t%s\t%.1f\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\n",
                $item->getId(),
                $item->getName(),
                str_replace(array("\r","\n"), "", $item->getDescription()),
                '', 
                'Store' . $item->getParentCategory() . $item->getCategory(),
                htmlspecialchars($baseUrl . $item->getUrl()),
                ($item->getImage()) ? $baseUrl: '' . $item->getImage(),
                ($item->getImage1()) ? $baseUrl: '' . $item->getImage1(),
                'new', 
                'in stock', 
                $item->getPrice() . ' USD',
                ':::0.00',
                '1lb',
                $item->getSku()
            );
            $io->streamWrite($csvRow);
        }
        unset($collection);

        $io->streamClose();

        $this->setGooglebaseminTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }
}
