<?php
class Justuno_Social_Block_Widget  extends Mage_Core_Block_Template
{
    public function getEmbed()
    {
		$jusdata = Mage::getStoreConfig('justuno/account/embed');
		if ($jusdata) {
			$jusdata = json_decode($jusdata);
		}
        return $jusdata->embed;
    }
}
