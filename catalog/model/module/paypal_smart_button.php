<?php
class ModelModulePayPalSmartButton extends Model {
	
	public function hasProductInCart($product_id, $option = array(), $recurring_id = 0) {
		if (VERSION >= '2.1.0.0') {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND recurring_id = '" . (int)$recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
		
			return $query->row['total'];
		} else {
			$product['product_id'] = (int)$product_id;

			if ($option) {
				$product['option'] = $option;
			}

			if ($recurring_id) {
				$product['recurring_id'] = (int)$recurring_id;
			}

			$key = base64_encode(serialize($product));

			if (isset($this->session->data['cart'][$key])) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function getCountryByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($code) . "' AND status = '1'");

		return $query->row;
	}
	
	public function getZoneByCode($country_id, $code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND (code = '" . $this->db->escape($code) . "' OR name = '" . $this->db->escape($code) . "') AND status = '1'");
		
		return $query->row;
	}
	
	public function log($data, $title = null) {
		if ($this->config->get('paypal_debug')) {
			$log = new Log('paypal.log');
			$log->write('PayPal debug (' . $title . '): ' . json_encode($data));
		}
	}
}
