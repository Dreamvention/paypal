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
				$option_data['paypal'] = [
					'code' => 'paypal.paypal',
					'name' => $this->language->get('text_paypal_title')
				];
				
				$option_data['paylater'] = [
					'code' => 'paypal.paylater',
					'name' => $this->language->get('text_paypal_paylater_title')
				];

				$method_data = [
					'code'       => 'paypal',
					'name'       => $this->language->get('text_paypal'),
					'option'     => $option_data,
					'sort_order' => $this->config->get('payment_paypal_sort_orderr')
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
}