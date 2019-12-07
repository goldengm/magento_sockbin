<?php

/**
 * Class GetresponseIntegration_Getresponse_Model_Webforms
 */
class GetresponseIntegration_Getresponse_Model_Webforms extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init('getresponse/webforms');
	}

	/**
	 * @param $data
	 * @param $shop_id
	 *
	 * @return bool
	 */
	public function updateWebforms($data, $shop_id)
	{
		$model = $this->load($shop_id)->addData($data);

		try {
			$model->save();
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 *
	 */
	public function disconnectWebforms($shop_id)
	{
		$data = array(
			'webform_id' => '',
			'active_subscription' => '0',
			'url' => '',
			'webform_title' => 'Webform',
		);
		$this->updateWebforms($data, $shop_id);
	}
}