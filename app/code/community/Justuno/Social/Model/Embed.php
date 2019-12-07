<?php
class Justuno_Social_Model_Embed extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
    		
			$fdata = array();
			foreach ($this->groups['register']['fields'] as $name=>$field) {
				$fdata[$name] = $field['value'];
			}

			if($fdata['email'] && $fdata['password']){
				$fdata['guid'] = "";
				include_once dirname(__FILE__) . '/JustunoAccess.php';
				$params = array('apiKey'=>JUSTUNO_KEY,
					'email'=>$fdata['email'],
					'domain'=>$fdata['domain'],
					'guid'=>$fdata['guid'],
					'phone'=>$fdata['phone']);
				if($fdata['password'])
					$params['password'] = $fdata['password'];
				$jAccess = new JustunoAccess($params);
				try {
					$justuno = $jAccess->getWidgetConfig();
					$jusdata = array();
					$jusdata['dashboard'] = (string)$jAccess->getDashboardLink();
					$jusdata['guid'] = (string)$justuno['guid'];
					$jusdata['embed'] = (string)$justuno['embed'];
					$embed_db = (string)json_encode($jusdata);
					$store = Mage::app()->getStore();
					$config = Mage::getModel('core/config_data');
					$config->setValue($embed_db);
					$config->setPath('justuno/account/embed');
					$config->setScope('default');
					$config->setScopeId($store->getId());
					$config->save();
					$config2 = Mage::getModel('core/config_data');
					$config2->setValue($fdata['email']);
					$config2->setPath('justuno/account/email');
					$config2->setScope('default');
					$config2->setScopeId($store->getId());
					$config2->save();
					
				}
				catch(JustunoAccessException $e) {
					Mage::throwException($e->getMessage());
				}
			}
			$flogindata = array();
			foreach ($this->groups['account']['fields'] as $name=>$field) {
				$flogindata[$name] = $field['value'];
			}
		
		if($flogindata['email'] && $flogindata['password']){
			if ($flogindata['embed']) {
				$obj = json_decode($flogindata['embed']);
				$flogindata['embed'] = $obj->embed;
				$flogindata['guid'] = $obj->guid;
			}
			include_once dirname(__FILE__) . '/JustunoAccess.php';

			$domain = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			$login_params = array('apiKey'=>JUSTUNO_KEY,'email'=>$flogindata['email'],'domain'=>$domain,'guid'=>$flogindata['guid']);
			
			if($flogindata['password'])
				$login_params['password'] = $flogindata['password'];
			$jAccess = new JustunoAccess($login_params);
			try {
				$jusdata = array();
				$jusdata['dashboard'] = (string)$jAccess->getDashboardLink();
				$justuno = $jAccess->getWidgetConfig();
				$jusdata['guid'] = (string)$justuno['guid'];
				$jusdata['embed'] = (string)$justuno['embed'];
				$flogin_embed_db = (string)json_encode($jusdata);
				
				$storeCode = 'default';
				$store = Mage::getModel('core/store')->load($storeCode);
				$path = 'justuno/account/embed';
				/*$config = Mage::getModel('core/config_data');
				$config->setValue($flogin_embed_db);
				$config->setPath($path);
				$config->setScope('default');
				$config->setScopeId($store->getId());
				$config->save(); */
				Mage::getConfig()->saveConfig('justuno/account/embed', $flogin_embed_db, 'default', 0);
				Mage::getConfig()->saveConfig('justuno/account/email', $flogindata['email'], 'default', 0);
				Mage::getConfig()->saveConfig('justuno/account/password', $flogindata['password'], 'default', 0);
				Mage::getConfig()->saveConfig('justuno/account/domain', $flogindata['domain'], 'default', 0);
			}
			catch(JustunoAccessException $e) {
				Mage::throwException($e->getMessage());
			}
		}
		//return parent::save();
	}
}
