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

class Webtex_Fba_Model_Config_Source_AmazonQtyField
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'in_stock_qty', 'label' => 'Received by Amazon Qty'),
            array('value' => 'total_qty', 'label' => 'Total qty (Received and Shipped To Amazon)'),

        );
    }

    public function toArray()
    {
        return array(
            'in_stock_qty' => 'Received by Amazon Qty',
            'total_qty' => 'Total qty (Received and Shipped To Amazon)'
        );
    }
}
