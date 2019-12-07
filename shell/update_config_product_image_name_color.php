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
class Mage_Shell_ConfigurableProductImageName extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        // connect to the database as user
        // with privileges for sockbin and dbo
//        $link = mysql_connect('127.0.0.1', 'sockbin', 'sockbin');
        $link = mysql_connect('127.0.0.1', 'wwwsockb_magento', 'dbD7s@QKXh=T');
        if (!$link) {
            die('Could not connect 1: ' . mysql_error());
        }
//        $link2 = mysql_connect('127.0.0.1', 'sockbin', 'sockbin', false);
//        if (!$link2) {
//            die('Could not connect 2: ' . mysql_error());
//        }
//        mysql_select_db('sockbin', $link);
        mysql_select_db('wwwsockb_magento', $link);
        
        //DELETE all images for configurable products before updating
        $query_delete = str_replace(array("\t","\n","\r"), ' ', "
            DELETE cpemg, cpemgv 
            FROM catalog_product_entity_media_gallery cpemg 
                LEFT JOIN catalog_product_entity_media_gallery_value cpemgv ON cpemg.`value_id` = cpemgv.`value_id` 
                LEFT JOIN catalog_product_entity cpe ON cpemg.entity_id = cpe.entity_id 
            WHERE cpe.type_id = 'configurable' 
        ");
        
        $result_delete = mysql_query($query_delete);
        if (!$result_delete) {
            $message_delete  = 'Invalid query: ' . mysql_error() . "\n";
            $message_delete .= 'Whole query: ' . $query_delete;
            die($message_delete);
        }
        
        //select all old products with options
        $query = str_replace(array("\t","\n","\r"), ' ', "
        SELECT DISTINCT 
            cpemg.attribute_id, 
            cpsl.parent_id, 
            cpsl.product_id, 
            cpemg.`value` AS 'image_file_name', 
            cpemgv.`label`, 
            cpemgv.`position` ,
            cpe.sku, 
            eaov.`value` AS 'color' 
        FROM catalog_product_entity_media_gallery cpemg 
            LEFT JOIN catalog_product_entity_media_gallery_value cpemgv ON cpemg.value_id = cpemgv.value_id 
            INNER JOIN catalog_product_super_link cpsl ON cpemg.entity_id = cpsl.product_id 
            LEFT JOIN catalog_product_entity cpe ON cpsl.product_id = cpe.entity_id 
            LEFT JOIN catalog_product_entity_int cpei ON cpe.entity_id = cpei.entity_id 
            LEFT JOIN eav_attribute_option_value eaov ON cpei.`value` = eaov.option_id
        WHERE
            cpe.type_id = 'simple' 
            AND cpei.attribute_id = 150 
        ORDER BY 
            cpsl.parent_id ASC, 
            cpsl.product_id ASC 
        ");
        $result = mysql_query($query);
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $query;
            die($message);
        }
        
        mysql_select_db('sockbin', $link2);
        $cnt = 0;
        $sort_order = 1;
        while ($product = mysql_fetch_assoc($result)) {
            echo "\nsku: " . $product['sku'];
            $parent_id = $product['parent_id'];
            $product_id = $product['product_id'];
            $attribute_id = $product['attribute_id'];
            $product_color = str_replace(" ", "_", strtolower($product['color']));
            $product_image = $product['image_file_name'];
            $label = $product['label'];
//            $position = $product['position'];
            $sku = $product['sku'];
            
            $product_image_renamed = $product_image;
            $color_image_exists = false;

            if (strpos(strtolower($product_image), $product_color) === false) {
                $file_parts = pathinfo($product_image);
                $product_image_renamed = $file_parts['dirname'] . "/" . $file_parts['filename'] . "_" . $product_color . "." . $file_parts['extension'];
                if (file_exists($product_image) && !file_exists($product_image_renamed)) {
                    copy($product_image, $product_image_renamed);
                    echo "Image " . $product_image . " copied to image " . $product_image_renamed . ".";
                }
            }

            $query_insert1 = "
                INSERT INTO catalog_product_entity_media_gallery 
                    SET 
                        attribute_id=" . $attribute_id . ", 
                        entity_id=" . $parent_id . ", 
                        `value`='" . mysql_real_escape_string($product_image_renamed, $link) . "' ";
            $cnt++;
            echo "\nConfigurable product image insert: \n" . $query_insert1;
            if ($previous_parent_id === $parent_id) {
                $sort_order++;
            } else {
                $sort_order = 1;
            }
            $previous_parent_id = $parent_id;
            $result2a = mysql_query($query_insert1);

            if (!$result2a) {
                $message2a  = 'Invalid query: ' . mysql_error() . "\n";
                $message2a .= 'Whole query: ' . $query_insert1;
                die($message2a);
            }

            $image_value_id = mysql_insert_id();

            if ($image_value_id > 0) {
                $query_insert2 = "
                    INSERT INTO catalog_product_entity_media_gallery_value 
                        SET 
                            `value_id`=" . $image_value_id . ", 
                            `store_id`=0, 
                            `label`='" . mysql_real_escape_string($label, $link) . "', 
                            `position`=" . $sort_order . ", 
                            `sku`='" . $sku . "', 
                            `disabled`=0";
                echo "\nConfigurable product image insert: \n" . $query_insert2;
                $result2b = mysql_query($query_insert2);
                if (!$result2b) {
                    $message2b  = 'Invalid query: ' . mysql_error() . "\n";
                    $message2b .= 'Whole query: ' . $query_insert2;
                    die($message2b);
                }
            }
        }
        mysql_free_result($result);
        echo "\nTotal inserts: " . $cnt . "\n";
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

$shell = new Mage_Shell_ConfigurableProductImageName();
$shell->run();
