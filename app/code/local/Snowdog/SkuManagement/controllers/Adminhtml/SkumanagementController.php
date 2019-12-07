<?php

/**
 * Class Snowdog_SkuManagement_Adminhtml_SkumanagementController
 */
class Snowdog_SkuManagement_Adminhtml_SkumanagementController
    extends Mage_Adminhtml_Controller_Action
{

    const INPUT_FILE_NAME = 'skumanagementfileskus';

    /**
     * Execute index
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save a CSV file and link sku management products by sku
     */
    public function saveAction()
    {
        if (
            isset($_FILES[self::INPUT_FILE_NAME]['name']) &&
            $_FILES[self::INPUT_FILE_NAME]['name'] != ''
        ) {
            $baseDir = Mage::getBaseDir('media') . DS;

            try {
                $uploader = new Varien_File_Uploader(self::INPUT_FILE_NAME);
                $uploader->setAllowedExtensions(array('csv'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $path = $baseDir . 'snowskumanagement' . DS;
                $ext = pathinfo($_FILES[self::INPUT_FILE_NAME]['name'], PATHINFO_EXTENSION);
                $uploader->save($path, $_FILES[self::INPUT_FILE_NAME]['name']);

                $filename = $uploader->getUploadedFileName();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError(
                        Mage::helper('adminhtml')->__('Error uplading file: ' . $e->getMessage()
                        )
                    );
                $this->_redirect('*/*/index');
                return;
            }

            if ($path) {
                /* @var $productModel Mage_Catalog_Model_Product */
                $productModel = Mage::getModel('catalog/product');
                $rows = array_filter($this->_getCsvToArray($path . $filename));
                $errors = '';

                foreach ($rows as $row) {
                    $parentSku = trim($row[0]);
                    $skumanagementSkus = trim($row[1]);
                    $skumanagementSkusToLink = array();
                    $product = $productModel->loadByAttribute('sku', $parentSku);

                    if ($product && $product->getId()) {
                        if ($skumanagementSkus) {
                            $skumanagementSkusExploded = explode(',', $skumanagementSkus);

                            foreach ($skumanagementSkusExploded as $sex) {
                                $productToLink = $productModel->loadByAttribute('sku', $sex);

                                if($productToLink && $productToLink->getId()) {
                                    $skumanagementSkusToLink[$productToLink->getId()] = array('position' => '');
                                }
                            }
                        }

                        try {
                            $product->setSkumanagementLinkData($skumanagementSkusToLink);
                            $product->save();
                        } catch(Exception $e) {
                            $errors .= Mage::helper('adminhtml')
                                ->__(
                                    "Error linking sku management products for {$parentSku}: "
                                    . $e->getMessage() . "</br>"
                                );
                        }
                    }
                }

                if ($errors) {
                    Mage::getSingleton('adminhtml/session')
                        ->addError($errors);
                } else {
                    Mage::getSingleton('adminhtml/session')
                        ->addSuccess("CSV file imported correctly.");
                }
            } else {
                Mage::getSingleton('adminhtml/session')
                    ->addError(Mage::helper('adminhtml')->__("File can't be found."));
            }
        } else {
            Mage::getSingleton('adminhtml/session')
                ->addError(
                    Mage::helper('adminhtml')->__("Please upload a valid CSV file.")
                );
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Get an array with all csv rows
     *
     * @param $csvFile
     * @return array
     */
    protected function _getCsvToArray($csvFile) {
        $file_handle = fopen($csvFile, 'r');
        $rows = [];

        while (!feof($file_handle) ) {
            $rows[] = fgetcsv($file_handle, 1024);
        }

        fclose($file_handle);

        return $rows;
    }
}