<?php
namespace Opencart\Catalog\Model\Extension\PayPal\Payment;
class PayPal extends \Opencart\System\Engine\Model {
	
	public function getMethod(array $address): array {
		$method_data = [];
		
		$agree_status = $this->getAgreeStatus();
		
		if ($this->config->get('payment_paypal_status') && $this->config->get('payment_paypal_client_id') && $this->config->get('payment_paypal_secret') && $agree_status) {
			$this->load->language('extension/paypal/payment/paypal');

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_paypal_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if ($this->cart->hasSubscription()) {
				$status = false;
			} elseif (!$this->config->get('payment_paypal_geo_zone_id')) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}

			if ($status) {
				$method_data = [
					'code'       => 'paypal',
					'title'      => $this->language->get('text_paypal_title'),
					'sort_order' => $this->config->get('payment_paypal_sort_order')
				];
			}
		}

		return $method_data;
	}
	
	public function getMethods(array $address = []): array {
		$method_data = [];

		$agree_status = $this->getAgreeStatus();
		
		if ($this->config->get('payment_paypal_status') && $this->config->get('payment_paypal_client_id') && $this->config->get('payment_paypal_secret') && $agree_status) {
			$this->load->language('extension/paypal/payment/paypal');
			
			if ($this->cart->hasSubscription()) {
				$status = false;
			} elseif (!$this->config->get('config_checkout_payment_address')) {
				$status = true;
			} elseif (!$this->config->get('payment_paypal_geo_zone_id')) {
				$status = true;
			} else {
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_paypal_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

				if ($query->num_rows) {
					$status = true;
				} else {
					$status = false;
				}
			}

			if ($status) {
				// Setting
				$_config = new \Opencart\System\Engine\Config();
				$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
				$_config->load('paypal');
			
				$config_setting = $_config->get('paypal_setting');
		
				$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
				
				$option_data['paypal'] = [
					'code' => 'paypal.paypal',
					'name' => $this->language->get('text_paypal_title')
				];
				
				if (!empty($setting['paylater_country'][$setting['general']['country_code']]) && ($setting['button']['checkout']['funding']['paylater'] != 2)) {
					$option_data['paylater'] = [
						'code' => 'paypal.paylater',
						'name' => $this->language->get('text_paypal_paylater_title')
					];
				}
				
				if ($setting['googlepay_button']['status']) {
					$option_data['googlepay'] = [
						'code' => 'paypal.googlepay',
						'name' => $this->language->get('text_paypal_googlepay_title')
					];
				}
				
				if ($setting['applepay_button']['status'] && $this->isApple()) {
					$option_data['applepay'] = [
						'code' => 'paypal.applepay',
						'name' => $this->language->get('text_paypal_applepay_title')
					];
				}

				$method_data = [
					'code'       => 'paypal',
					'name'       => $this->language->get('text_paypal'),
					'option'     => $option_data,
					'sort_order' => $this->config->get('payment_paypal_sort_order')
				];
			}
		}

		return $method_data;
	}
	
	public function hasProductInCart(int $product_id, array $option = [], int $subscription_plan_id = 0): int {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND subscription_plan_id = '" . (int)$subscription_plan_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
		
		return $query->row['total'];
	}
	
	public function getCountryByCode(string $code): array {
		$query = $this->db->query("SELECT *, c.name FROM " . DB_PREFIX . "country c LEFT JOIN " . DB_PREFIX . "address_format af ON (c.address_format_id = af.address_format_id) WHERE c.iso_code_2 = '" . $this->db->escape($code) . "' AND c.status = '1'");

		return $query->row;
	}
	
	public function getZoneByCode(int $country_id, string $code): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND (code = '" . $this->db->escape($code) . "' OR name = '" . $this->db->escape($code) . "') AND status = '1'");
		
		return $query->row;
	}
	
	public function addOrder(array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "paypal_checkout_integration_order` SET `order_id` = '" . (int)$data['order_id'] . "', `transaction_id` = '" . $this->db->escape($data['transaction_id']) . "', `transaction_status` = '" . $this->db->escape($data['transaction_status']) . "', `environment` = '" . $this->db->escape($data['environment']) . "'");
	}
		
	public function deleteOrder(int $order_id): void {
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "paypal_checkout_integration_order` WHERE `order_id` = '" . (int)$order_id . "'");
	}
	
	public function getOrder(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "paypal_checkout_integration_order` WHERE `order_id` = '" . (int)$order_id . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}
	
	public function getAgreeStatus(): bool {
		$agree_status = true;
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' AND (iso_code_2 = 'CU' OR iso_code_2 = 'IR' OR iso_code_2 = 'SY' OR iso_code_2 = 'KP')");
		
		if ($query->rows) {
			$agree_status = false;
		}
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '220' AND status = '1' AND (`code` = '43' OR `code` = '14' OR `code` = '09')");
		
		if ($query->rows) {
			$agree_status = false;
		}
		
		return $agree_status;
	}
	
	public function log(array $data = [], string $title = ''): void {
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
		$_config->load('paypal');
		
		$config_setting = $_config->get('paypal_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
		
		if ($setting['general']['debug']) {
			$log = new \Opencart\System\Library\Log('paypal.log');
			$log->write('PayPal debug (' . $title . '): ' . json_encode($data));
		}
	}
	
	public function update(): void {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order`");
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order` (`order_id` INT(11) NOT NULL, `transaction_id` VARCHAR(20) NOT NULL, `transaction_status` VARCHAR(20) NOT NULL, `environment` VARCHAR(20) NOT NULL, PRIMARY KEY (`order_id`, `transaction_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = 'paypal_order_info'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = 'paypal_header'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = 'paypal_extension_get_extensions_by_type'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = 'paypal_extension_get_extension_by_code'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = 'paypal_order_delete_order'");
		
		if (VERSION >= '4.0.2.0') {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_order_info', `description` = '', `trigger` = 'admin/view/sale/order_info/before', `action` = 'extension/paypal/payment/paypal.order_info_before', `status` = '1', `sort_order` = '1'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_header', `description` = '', `trigger` = 'catalog/controller/common/header/before', `action` = 'extension/paypal/payment/paypal.header_before', `status` = '1', `sort_order` = '2'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_order_delete_order', `description` = '', `trigger` = 'catalog/model/checkout/order/deleteOrder/before', `action` = 'extension/paypal/payment/paypal.order_delete_order_before', `status` = '1', `sort_order` = '3'");
		} elseif (VERSION >= '4.0.1.0') {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_order_info', `description` = '', `trigger` = 'admin/view/sale/order_info/before', `action` = 'extension/paypal/payment/paypal|order_info_before', `status` = '1', `sort_order` = '1'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_header', `description` = '', `trigger` = 'catalog/controller/common/header/before', `action` = 'extension/paypal/payment/paypal|header_before', `status` = '1', `sort_order` = '2'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_extension_get_extensions_by_type', `description` = '', `trigger` = 'catalog/model/setting/extension/getExtensionsByType/after', `action` = 'extension/paypal/payment/paypal|extension_get_extensions_by_type_after', `status` = '1', `sort_order` = '3'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_extension_get_extension_by_code', `description` = '', `trigger` = 'catalog/model/setting/extension/getExtensionByCode/after', `action` = 'extension/paypal/payment/paypal|extension_get_extension_by_code_after', `status` = '1', `sort_order` = '4'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_order_delete_order', `description` = '', `trigger` = 'catalog/model/checkout/order/deleteOrder/before', `action` = 'extension/paypal/payment/paypal|order_delete_order_before', `status` = '1', `sort_order` = '5'");
		} else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_order_info', `description` = '', `trigger` = 'admin/view/sale/order_info/before', `action` = 'extension/paypal/payment/paypal|order_info_before', `status` = '1', `sort_order` = '1'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_header', `description` = '', `trigger` = 'catalog/controller/common/header/before', `action` = 'extension/paypal/payment/paypal|header_before', `status` = '1', `sort_order` = '2'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_extension_get_extensions_by_type', `description` = '', `trigger` = 'catalog/model/setting/extension/getExtensionsByType/after', `action` = 'extension/paypal/payment/paypal|extension_get_extensions_by_type_after', `status` = '1', `sort_order` = '3'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_extension_get_extension_by_code', `description` = '', `trigger` = 'catalog/model/setting/extension/getExtensionByCode/after', `action` = 'extension/paypal/payment/paypal|extension_get_extension_by_code_after', `status` = '1', `sort_order` = '4'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = 'paypal_order_delete_order', `description` = '', `trigger` = 'catalog/model/checkout/order/deleteOrder/before', `action` = 'extension/paypal/payment/paypal|order_delete_order_before', `status` = '1', `sort_order` = '5'");
		}
								
		// Setting
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
		$_config->load('paypal');
		
		$config_setting = $_config->get('paypal_setting');
						
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = 'paypal_version'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'paypal_version', `key` = 'paypal_version', `value` = '" . $this->db->escape($config_setting['version']) . "'");
		
		$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = 'Lax', `serialized` = '0'  WHERE `code` = 'config' AND `key` = 'config_session_samesite' AND `store_id` = '0'");
	}
	
	private function isApple(): bool {
		if (!empty($this->request->server['HTTP_USER_AGENT'])) {
			$user_agent = $this->request->server['HTTP_USER_AGENT'];
			
			$apple_agents = ['ipod', 'iphone', 'ipad'];

            foreach ($apple_agents as $apple_agent){
                if (stripos($user_agent, $apple_agent)) {
                    return true;
                }
			}
        }
		
		return false;
	}
}