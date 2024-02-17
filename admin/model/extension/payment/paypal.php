<?php
class ModelExtensionPaymentPayPal extends Model {
		
	public function getTotalSales() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		
		$query = $this->db->query("SELECT SUM(total) AS paypal_total FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND payment_code = 'paypal'");

		return $query->row['paypal_total'];
	}
	
	public function getTotalSalesByDay() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sale_data = array();

		for ($i = 0; $i < 24; $i++) {
			$sale_data[$i] = array(
				'hour'  		=> $i,
				'total' 		=> 0,
				'paypal_total' 	=> 0
			);
		}

		$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_code = 'paypal', total, 0)) AS paypal_total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");

		foreach ($query->rows as $result) {
			$sale_data[$result['hour']] = array(
				'hour'  		=> $result['hour'],
				'total' 		=> $result['total'],
				'paypal_total'  => $result['paypal_total']
			);
		}

		return $sale_data;
	}

	public function getTotalSalesByWeek() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sale_data = array();

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$sale_data[date('w', strtotime($date))] = array(
				'day'   		=> date('D', strtotime($date)),
				'total' 		=> 0,
				'paypal_total' 	=> 0
			);
		}

		$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_code = 'paypal', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");

		foreach ($query->rows as $result) {
			$sale_data[date('w', strtotime($result['date_added']))] = array(
				'day'   		=> date('D', strtotime($result['date_added'])),
				'total' 		=> $result['total'],
				'paypal_total'  => $result['paypal_total']
			);
		}

		return $sale_data;
	}

	public function getTotalSalesByMonth() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sale_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$sale_data[date('j', strtotime($date))] = array(
				'day'   		=> date('d', strtotime($date)),
				'total' 		=> 0,
				'paypal_total' 	=> 0
			);
		}

		$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_code = 'paypal', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");

		foreach ($query->rows as $result) {
			$sale_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' 		=> $result['total'],
				'paypal_total'  => $result['paypal_total']
			);
		}

		return $sale_data;
	}

	public function getTotalSalesByYear() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sale_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$sale_data[$i] = array(
				'month' 		=> date('M', mktime(0, 0, 0, $i)),
				'total' 		=> 0,
				'paypal_total' 	=> 0
			);
		}

		$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_code = 'paypal', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");

		foreach ($query->rows as $result) {
			$sale_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' 		=> $result['total'],
				'paypal_total'  => $result['paypal_total']
			);
		}

		return $sale_data;
	}
		
	public function getCountryByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($code) . "'");
				
		return $query->row;
	}
		
	public function editPayPalOrder($data) {
		$sql = "UPDATE `" . DB_PREFIX . "paypal_checkout_integration_order` SET";

		$implode = array();
		
		if (!empty($data['transaction_id'])) {
			$implode[] = "`transaction_id` = '" . $this->db->escape($data['transaction_id']) . "'";
		}
					
		if (!empty($data['transaction_status'])) {
			$implode[] = "`transaction_status` = '" . $this->db->escape($data['transaction_status']) . "'";
		}
		
		if (!empty($data['payment_method'])) {
			$implode[] = "`payment_method` = '" . $this->db->escape($data['payment_method']) . "'";
		}
		
		if (!empty($data['vault_id'])) {
			$implode[] = "`vault_id` = '" . $this->db->escape($data['vault_id']) . "'";
		}
		
		if (!empty($data['vault_customer_id'])) {
			$implode[] = "`vault_customer_id` = '" . $this->db->escape($data['vault_customer_id']) . "'";
		}
		
		if (!empty($data['environment'])) {
			$implode[] = "`environment` = '" . $this->db->escape($data['environment']) . "'";
		}
				
		if ($implode) {
			$sql .= implode(", ", $implode);
		}

		$sql .= " WHERE `order_id` = '" . (int)$data['order_id'] . "'";
		
		$this->db->query($sql);
	}
		
	public function deletePayPalOrder($order_id) {
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "paypal_checkout_integration_order` WHERE `order_id` = '" . (int)$order_id . "'");
	}
	
	public function getPayPalOrder($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "paypal_checkout_integration_order` WHERE `order_id` = '" . (int)$order_id . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return array();
		}
	}
	
	public function editOrderRecurringStatus($order_recurring_id, $status) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order_recurring` SET `status` = '" . (int)$status . "' WHERE `order_recurring_id` = '" . (int)$order_recurring_id . "'");
	}
	
	public function setAgreeStatus() {
		$this->db->query("UPDATE " . DB_PREFIX . "country SET status = '0' WHERE (iso_code_2 = 'CU' OR iso_code_2 = 'IR' OR iso_code_2 = 'SY' OR iso_code_2 = 'KP')");
		$this->db->query("UPDATE " . DB_PREFIX . "zone SET status = '0' WHERE country_id = '220' AND (`code` = '43' OR `code` = '14' OR `code` = '09')");
	}
	
	public function getAgreeStatus() {
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
	
	public function checkVersion($opencart_version, $paypal_version) {
		$curl = curl_init();
			
		curl_setopt($curl, CURLOPT_URL, 'https://www.opencart.com/index.php?route=api/promotion/paypalCheckoutIntegration&opencart=' . $opencart_version . '&paypal=' . $paypal_version);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
							
		$response = curl_exec($curl);
			
		curl_close($curl);
			
		$result = json_decode($response, true);
		
		if ($result) {
			return $result;
		} else {
			return false;
		}
	}
		
	public function sendContact($data) {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8');
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

		$response = curl_exec($curl);

		curl_close($curl);
	}
	
	public function log($data, $title = null) {
		$_config = new Config();
		$_config->load('paypal');
			
		$config_setting = $_config->get('paypal_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
			
		if ($setting['general']['debug']) {
			$log = new Log('paypal.log');
			$log->write('PayPal debug (' . $title . '): ' . json_encode($data));
		}
	}
	
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order` (`order_id` INT(11) NOT NULL, `transaction_id` VARCHAR(20) NOT NULL, `transaction_status` VARCHAR(20) NULL, `payment_method` VARCHAR(20) NULL, `vault_id` VARCHAR(50) NULL, `vault_customer_id` VARCHAR(50) NULL, `environment` VARCHAR(20) NULL, PRIMARY KEY (`order_id`, `transaction_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order_recurring` (`paypal_order_recurring_id` INT(11) NOT NULL AUTO_INCREMENT, `order_id` INT(11) NOT NULL, `order_recurring_id` INT(11) NOT NULL, `date_added` DATETIME NOT NULL, `date_modified` DATETIME NOT NULL, `next_payment` DATETIME NOT NULL, `trial_end` DATETIME DEFAULT NULL, `subscription_end` DATETIME DEFAULT NULL, `currency_code` CHAR(3) NOT NULL, `total` DECIMAL(10, 2) NOT NULL, PRIMARY KEY (`paypal_order_recurring_id`), KEY (`order_id`), KEY (`order_recurring_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order_recurring`");
	}
}
