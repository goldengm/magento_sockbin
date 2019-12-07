<?php

/**
 * Class GetresponseIntegration_Getresponse_Model_Settings
 */
class GetresponseIntegration_Getresponse_Model_Settings extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init('getresponse/settings');
	}

	/**
	 * @param $shop_id
	 *
	 * @return bool
	 */
	public function disconnectSettings($shop_id)
	{
		$model = $this->load($shop_id)->addData(array(
			'api_key' => '',
			'api_url' => '',
			'api_domain' => '',
			'active_subscription' => '0',
			'update_address' => '0',
			'campaign_id' => '',
			'cycle_day' => '0'
		));

		try {
			$model->save();
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * @param $data
	 * @param $shop_id
	 *
	 * @return bool
	 */
	public function updateSettings($data, $shop_id)
	{
		$model = $this->load($shop_id)->addData($data);

		try {
			$model->save();
		} catch (Exception $e) {
			return false;
		}

		return true;
	}
}