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

class Webtex_Fba_Block_Adminhtml_Marketplace_Renderer_Pricerules extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml()
    {
        $element = $this;
        $id = $this->getId();
        $html = '<div class="grid"><table cellspacing="0" class="data" id="' . $id . '_table"><colgroup><col width="80px" /><col width="80px"/><col width="80px;"/><col /></colgroup><thead><tr class="headings"><th>Min weight</th><th>Max weight</th><th>Price</th><th class="last">Action</th></tr></thead>';
        $html .= '<tbody id="' . $id . '_container">';
        $html .= '<tr style="display:none"><td><input class="input-text" style="' . $element->getStyle() . '" type="text" name="' . $element->getName() . '[nun]" id="' . $id . '_nun_from" />0</td></tr>';

        $element->setStyle('width:63px;')
            ->setScope(false);

        $values = $element->getData('value');
        $from = array();
        $to = array();
        $price = array();

        if ($this->_getValue('from')) {
            foreach ($this->_getValue('from') as $i => $v) {
                if ($v != "") $from[] = $v;
            }
        }
        if ($this->_getValue('to')) {
            foreach ($this->_getValue('to') as $i => $v) {
                if ($v != "") $to[] = $v;
            }
        }
        if ($this->_getValue('price')) {
            foreach ($this->_getValue('price') as $i => $v) {
                if ($v != "") $price[] = $v;
            }
        }

        $html .= '</tbody><tfoot><tr><td class="a-right" colspan="4"><button id="btnFbaAddRule" type="button" class="scalable add" onclick="' . $id . 'Control.addItem();"><span>Add Rule</span></button></td></tr></tfoot>';
        $html .= '</table>';
        $html .= '<script type="text/javascript">
              //<![CDATA[
              var ' . $id . 'RowTemplate = ';
        $html .= '\'<tr><td><input class="input-text required-entry validate-number" style="' . $element->getStyle() . '" type="text" name="' . $element->getName() . '[from][{{index}}]" id="' . $id . '_{{index}}_from" /></td>';
        $html .= '<td><input class="input-text required-entry validate-number" style="' . $element->getStyle() . '" type="text" name="' . $element->getName() . '[to][{{index}}]" id="' . $id . '_{{index}}_to" /></td>';
        $html .= '<td><input class="input-text required-entry validate-number" style="' . $element->getStyle() . '" type="text" name="' . $element->getName() . '[price][{{index}}]" id="' . $id . '_{{index}}_price" /></td>';
        $html .= '<td class="last"><input type="hidden"  class="delete" value="" id="' . $id . '_{{index}}_delete" />';
        $html .= '<button title="' . Mage::helper("catalog")->__("Delete") . '" type="button" class="scalable delete icon-btn delete-product-option" id="' . $id . '_{{index}}_delete_button" onclick="return ' . $id . 'Control.deleteItem(event);">';
        $html .= '<span>' . Mage::helper("catalog")->__("Delete") . '</span></button></td>';
        $html .= '</tr>\';' . "\n";

        $html .= ' var ' . $id . 'Control = {' . "\n";
        $html .= ' template: new Template(' . $id . 'RowTemplate, new RegExp(\'(^|.|\\\r|\\\n)({{\\\s*(\\\w+)\\\s*}})\',"")),' . "\n";
        $html .= ' itemsCount: 0,' . "\n";

        $html .= ' addItem : function() {' . "\n";
        $html .= ' var data = {
              from: \'\',
              to: \'\',
              price: \'\',
              index: this.itemsCount++
        };

        if(arguments.length >= 3) {
            data.from = arguments[0];
            data.to      = arguments[1];
            data.price        = arguments[2];
        }
        ';

        $html .= " Element.insert($('" . $id . "_container'), {
            bottom : this.template.evaluate(data)
        });

        $('" . $id . "_' + data.index + '_from').value = data.from;
        $('" . $id . "_' + data.index + '_to').value = data.to;
        $('" . $id . "_' + data.index + '_price').value    = data.price;
    },

    disableElement: function(el) {
        el.disabled = true;
        el.addClassName('disabled');
    },

    deleteItem: function(event) {
        var tr = Event.findElement(event, 'tr');
        if (tr) {
            Element.remove(tr);
        }
        return false;
    }
    };\n";
        for ($i = 0; $i < sizeof($from); $i++) {
            $html .= $id . "Control.addItem(" . $from[$i] . "," . $to[$i] . "," . $price[$i] . ");\n";
        }
        $html .= "//]]
    </script>
    </div>";

        return $html;
    }

    public function getLabelHtml($idSuffix = '')
    {
        if (!is_null($this->getLabel())) {
            $html = '<label for="' . $this->getHtmlId() . $idSuffix . '" style="' . $this->getLabelStyle() . '">' . $this->getLabel()
                . ($this->getRequired() ? ' <span class="required">*</span>' : '') . '</label>' . "\n";
        } else {
            $html = '';
        }
        return $html;
    }

    protected function _getValue($key)
    {
        return $this->getData('value/' . $key);
    }
}