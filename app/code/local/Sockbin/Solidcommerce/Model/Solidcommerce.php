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
 * @package     Sockbin_Solidcommerce
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Solidcommerce model
 *
 * @method Sockbin_Solidcommerce_Model_Resource_Solidcommerce _getResource()
 * @method Sockbin_Solidcommerce_Model_Resource_Solidcommerce getResource()
 * @method string getSolidcommerceType()
 * @method Sockbin_Solidcommerce_Model_Solidcommerce setSolidcommerceType(string $value)
 * @method string getSolidcommerceFilename()
 * @method Sockbin_Solidcommerce_Model_Solidcommerce setSolidcommerceFilename(string $value)
 * @method string getSolidcommercePath()
 * @method Sockbin_Solidcommerce_Model_Solidcommerce setSolidcommercePath(string $value)
 * @method string getSolidcommerceTime()
 * @method Sockbin_Solidcommerce_Model_Solidcommerce setSolidcommerceTime(string $value)
 * @method int getStoreId()
 * @method Sockbin_Solidcommerce_Model_Solidcommerce setStoreId(int $value)
 *
 * @category    Sockbin
 * @package     Sockbin_Solidcommerce
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sockbin_Solidcommerce_Model_Solidcommerce extends Mage_Core_Model_Abstract
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
        $this->_init('solidcommerce/solidcommerce');
    }

    protected function _beforeSave()
    {
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath(Mage::getBaseDir() . '/' . $this->getSolidcommercePath());

        /**
         * Check path is allow
         */
        if (!$io->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException(Mage::helper('solidcommerce')->__('Please define correct path'));
        }
        /**
         * Check exists and writeable path
         */
        if (!$io->fileExists($realPath, false)) {
            Mage::throwException(Mage::helper('solidcommerce')->__('Please create the specified folder "%s" before saving the solidcommerce.', Mage::helper('core')->escapeHtml($this->getSolidcommercePath())));
        }

        if (!$io->isWriteable($realPath)) {
            Mage::throwException(Mage::helper('solidcommerce')->__('Please make sure that "%s" is writable by web-server.', $this->getSolidcommercePath()));
        }
        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getSolidcommerceFilename())) {
            Mage::throwException(Mage::helper('solidcommerce')->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }
        if (!preg_match('#\.txt$#', $this->getSolidcommerceFilename())) {
            $this->setSolidcommerceFilename($this->getSolidcommerceFilename() . '.txt');
        }

        $this->setSolidcommercePath(rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $realPath), '/') . '/');

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
                $this->getSolidcommercePath());
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
        return $this->getPath() . $this->getSolidcommerceFilename();
    }

    /**
     * Generate XML file
     *
     * @return Sockbin_Solidcommerce_Model_Solidcommerce
     */
    public function generateXml()
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));

        if ($io->fileExists($this->getSolidcommerceFilename()) && !$io->isWriteable($this->getSolidcommerceFilename())) {
            Mage::throwException(Mage::helper('solidcommerce')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSolidcommerceFilename(), $this->getPath()));
        }

        $io->streamOpen($this->getSolidcommerceFilename());

        $io->streamWrite("Item #\tItem name\tBrand\tUPC Code\tMSRP\tprice\timage\textraimage1\textraimage2\textraimage3\tsku\tdescription\tinventory\tattribute_name\tAttribute Color\n");

        $storeId = $this->getStoreId();
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        /**
         * Generate products solidcommerce
         */
        $collection = Mage::getResourceModel('solidcommerce/catalog_product')->getCollection($storeId);
        $products = new Varien_Object();
        $products->setItems($collection);
        Mage::dispatchEvent('solidcommerce_products_generating_before', array(
            'collection' => $products
        ));
        foreach ($products->getItems() as $item) {
            $prodAttributeColor = $item->getAttributeColor();
            $attrValues = array(0 => '');
            if (!empty($prodAttributeColor)) {
                $attrValues = $prodAttributeColor;
            }
            foreach ($attrValues as $k => $attrValue) {
                $csvRow = sprintf(
                    "%s\t%s\t%s\t%.1f\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\n",
                    $item->getId(),
                    $item->getName(),
                    'Sockbin',
                    $item->getUpc(),
                    $item->getMsrp(),
                    $item->getPrice(),
                    ($item->getImage()) ? $baseUrl: '' . $item->getImage(),
                    ($item->getImage1()) ? $baseUrl: '' . $item->getImage1(),
                    ($item->getImage2()) ? $baseUrl: '' . $item->getImage2(),
                    ($item->getImage3()) ? $baseUrl: '' . $item->getImage3(),
                    $item->getSku(),
                    str_replace(array("\r","\n"), "", $item->getDescription()),
                    $item->getInventory(),
                    $item->getAttributeName(),
                    $attrValue
                );
                $io->streamWrite($csvRow);
            }
        }
        unset($collection);

        $io->streamClose();

        $this->setSolidcommerceTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }
}
