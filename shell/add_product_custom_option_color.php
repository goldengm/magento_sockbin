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
 * @package     Mage_Shell
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_CustomOptionColor extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        // connect to the database as user
        // with privileges for sockbin and dbo
        $link = mysql_connect('127.0.0.1', 'sockbin', 'sockbin');
        if (!$link) {
            die('Could not connect 1: ' . mysql_error());
        }
        $link2 = mysql_connect('127.0.0.1', 'sockbin', 'sockbin', false);
        if (!$link2) {
            die('Could not connect 2: ' . mysql_error());
        }
        mysql_select_db('dbo', $link);
        //select all old products with options
        $query = str_replace(array("\t","\n","\r"), ' ', "
        SELECT DISTINCT p.ProductSKU AS sku, GROUP_CONCAT(DISTINCT(pa.ProductAttributeVal)) AS old_product_colors 
        FROM utb_products p
            LEFT JOIN dbo.utb_productattributes pa ON p.ProductID = pa.ProductID 
            LEFT JOIN dbo.utb_productattributevalues pav ON p.ProductID = pav.ProductID 
        WHERE pa.ProductAttributeVal  NOT LIKE '%L | XL%' 
            AND pa.ProductAttributeVal NOT LIKE '%L/XL%' 
            AND pa.ProductAttributeVal NOT LIKE '%Large%' 
            AND pa.ProductAttributeVal NOT LIKE '%M/L%' 
            AND pa.ProductAttributeVal NOT LIKE '%Medium%' 
            AND pa.ProductAttributeVal NOT LIKE '%S | M%' 
            AND pa.ProductAttributeVal NOT LIKE '%S/M%' 
            AND pa.ProductAttributeVal NOT LIKE '%Small%' 
            AND LTRIM(RTRIM(pa.ProductAttributeVal)) <> 'S' 
            AND LTRIM(RTRIM(pa.ProductAttributeVal)) <> 'M' 
            AND LTRIM(RTRIM(pa.ProductAttributeVal)) <> 'L' 
            AND LTRIM(RTRIM(pa.ProductAttributeVal)) <> 'XL' 
            AND pa.ProductAttributeVal NOT IN (4,6,8,10,12,14,16) 
            AND pa.ProductAttributeVal NOT LIKE '%Plus Size%' 
            AND pa.ProductAttributeVal NOT LIKE '%Queen%' 
            AND pa.ProductAttributeVal NOT LIKE '%-%' 
            AND pa.ProductAttributeVal NOT LIKE '%Inventory%'
            GROUP BY p.ProductID");
        $result = mysql_query($query);
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $query;
            die($message);
        }
        
        mysql_select_db('sockbin', $link2);
        $cnt = 0;
        while ($product = mysql_fetch_assoc($result)) {
            $cnt++;
            echo "\nsku: " . $product['sku'];
            //if product option exists for old product but not new, create it
            $query2 = str_replace(array("\t","\n","\r"), ' ', "
            SELECT cpe.entity_id, cpo.option_id, GROUP_CONCAT(cpott.title) as 'product_colors' 
            FROM catalog_product_entity cpe 
                LEFT JOIN catalog_product_option cpo ON cpe.entity_id = cpo.product_id 
                LEFT JOIN catalog_product_option_title cpot ON cpo.option_id = cpot.option_id 
                LEFT JOIN catalog_product_option_type_value cpotv ON cpo.option_id = cpotv.option_id 
                LEFT JOIN catalog_product_option_type_title cpott ON cpotv.option_type_id = cpott.option_type_id 
            WHERE cpe.sku = '" . $product['sku'] . "' 
            GROUP BY cpe.entity_id 
            ");
            
            $result2 = mysql_query($query2, $link2);
            if (!$result2) {
                $message2  = 'Invalid query: ' . mysql_error() . "\n";
                $message2 .= 'Whole query: ' . $query2;
                die($message2);
            }
            $old_product_colors = explode(',', $product['old_product_colors']);
            
            while ($mag_product = mysql_fetch_assoc($result2)) {
                $product_option_id = $mag_product['option_id'];
                
                if (empty($product_option_id)) {
                    echo 'Mag Product option_id being created...:';
                    //Insert product option color
                    $query_insert1 = "INSERT INTO catalog_product_option SET product_id=" . $mag_product['entity_id'] . ", `type`='drop_down', sort_order='2' ";
                    $result2a = mysql_query($query_insert1);
                    if (!$result2a) {
                        $message2a  = 'Invalid query: ' . mysql_error() . "\n";
                        $message2a .= 'Whole query: ' . $query_insert1;
                        die($message2a);
                    }
                    $product_option_id = mysql_insert_id();
                    
                    if ($product_option_id > 0) {
                        $query_insert2 = "INSERT INTO catalog_product_option_title SET option_id=" . $product_option_id . ", store_id=0, `title`='Color' ";
                        $result2b = mysql_query($query_insert2);
                        if (!$result2b) {
                            $message2b  = 'Invalid query: ' . mysql_error() . "\n";
                            $message2b .= 'Whole query: ' . $query_insert2;
                            die($message2b);
                        }
                    }
                }
                echo "\nMag Product option_id: " . $product_option_id;
                
                echo "\nMag Product colors: \n";
                var_dump($mag_product['product_colors']);
                if (sizeof($mag_product['product_colors']) < sizeof($old_product_colors) && $product_option_id > 0) {
                    $new_product_colors = (!empty($mag_product['product_colors'])) ? explode(',', $mag_product['product_colors']) : array();
                    $colors = array_diff($old_product_colors, $new_product_colors);
                    foreach ($colors as $color) {
                        $query_insert3 = "INSERT INTO catalog_product_option_type_value SET option_id=" . $product_option_id . ", `sku`='" . $product['sku'] . "-" . $color . "', `qty`=0, sort_order='2' ";
                        $result2c = mysql_query($query_insert3);
                        $option_type_id = mysql_insert_id();
                        if (!$result2c) {
                            $message2c  = 'Invalid query: ' . mysql_error() . "\n";
                            $message2c .= 'Whole query: ' . $query_insert3;
                            die($message2c);
                        }

                        if ($option_type_id > 0) {
                            $query_insert4 = "INSERT INTO catalog_product_option_type_title SET option_type_id=" . $option_type_id . ", store_id=0, `title`='" . $color . "' ";
                            $result2d = mysql_query($query_insert4);
                            if (!$result2d) {
                                $message2d  = 'Invalid query: ' . mysql_error() . "\n";
                                $message2d .= 'Whole query: ' . $query_insert4;
                                die($message2d);
                            }
                        }
                    }
                }
            }
        }
        echo "\nTotal Old Prods: " . $cnt;
        mysql_free_result($result);
        
        mysql_close($link);
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f indexer.php -- [options]

  --status <indexer>            Show Indexer(s) Status
  --mode <indexer>              Show Indexer(s) Index Mode
  --mode-realtime <indexer>     Set index mode type "Update on Save"
  --mode-manual <indexer>       Set index mode type "Manual Update"
  --reindex <indexer>           Reindex Data
  info                          Show allowed indexers
  reindexall                    Reindex Data by all indexers
  help                          This help

  <indexer>     Comma separated indexer codes or value "all" for all indexers

USAGE;
    }
}

$shell = new Mage_Shell_CustomOptionColor();
$shell->run();
