<?php
namespace Opencart\Admin\Model\Extension\PayPal\Payment;
class PayPal extends \Opencart\System\Engine\Model {
		
	public function getTotalSales(): float {
		$implode = [];

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		
		if (VERSION >= '4.0.2.0') {
			$query = $this->db->query("SELECT SUM(total) AS paypal_total FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND payment_method LIKE '%paypal%'");
		} else {
			$query = $this->db->query("SELECT SUM(total) AS paypal_total FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND payment_code = 'paypal'");
		}

		return (float)$query->row['paypal_total'];
	}
	
	public function getTotalSalesByDay(): array {
		$implode = [];

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sale_data = [];

		for ($i = 0; $i < 24; $i++) {
			$sale_data[$i] = [
				'hour'  		=> $i,
				'total' 		=> 0,
				'paypal_total' 	=> 0
			];
		}

		if (VERSION >= '4.0.2.0') {
			$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_method LIKE '%paypal%', total, 0)) AS paypal_total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");
		} else {
			$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_code = 'paypal', total, 0)) AS paypal_total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");
		}
		
		foreach ($query->rows as $result) {
			$sale_data[$result['hour']] = [
				'hour'  		=> $result['hour'],
				'total' 		=> $result['total'],
				'paypal_total'  => $result['paypal_total']
			];
		}

		return $sale_data;
	}

	public function getTotalSalesByWeek(): array {
		$implode = [];

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sale_data = [];

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$sale_data[date('w', strtotime($date))] = [
				'day'   		=> date('D', strtotime($date)),
				'total' 		=> 0,
				'paypal_total' 	=> 0
			];
		}

		if (VERSION >= '4.0.2.0') {
			$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_method LIKE '%paypal%', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");
		} else {
			$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_code = 'paypal', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");
		}
		
		foreach ($query->rows as $result) {
			$sale_data[date('w', strtotime($result['date_added']))] = [
				'day'   		=> date('D', strtotime($result['date_added'])),
				'total' 		=> $result['total'],
				'paypal_total'  => $result['paypal_total']
			];
		}

		return $sale_data;
	}

	public function getTotalSalesByMonth(): array {
		$implode = [];

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sale_data = [];

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$sale_data[date('j', strtotime($date))] = [
				'day'   		=> date('d', strtotime($date)),
				'total' 		=> 0,
				'paypal_total' 	=> 0
			];
		}

		if (VERSION >= '4.0.2.0') {
			$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_method LIKE '%paypal%', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");
		} else {
			$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_code = 'paypal', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");
		}
		
		foreach ($query->rows as $result) {
			$sale_data[date('j', strtotime($result['date_added']))] = [
				'day'   => date('d', strtotime($result['date_added'])),
				'total' 		=> $result['total'],
				'paypal_total'  => $result['paypal_total']
			];
		}

		return $sale_data;
	}

	public function getTotalSalesByYear(): array {
		$implode = [];

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sale_data = [];

		for ($i = 1; $i <= 12; $i++) {
			$sale_data[$i] = [
				'month' 		=> date('M', mktime(0, 0, 0, $i)),
				'total' 		=> 0,
				'paypal_total' 	=> 0
			];
		}

		if (VERSION >= '4.0.2.0') {
			$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_method LIKE '%paypal%', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");
		} else {
			$query = $this->db->query("SELECT SUM(total) AS total, SUM(IF (payment_code = 'paypal', total, 0)) AS paypal_total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(',', $implode) . ") AND YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");
		}
		
		foreach ($query->rows as $result) {
			$sale_data[date('n', strtotime($result['date_added']))] = [
				'month' => date('M', strtotime($result['date_added'])),
				'total' 		=> $result['total'],
				'paypal_total'  => $result['paypal_total']
			];
		}

		return $sale_data;
	}
		
	public function getCountryByCode(string $code): array|bool {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($code) . "'");
				
		return $query->row;
	}
	
	public function deletePayPalCustomerTokens(int $customer_id): void {
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "paypal_checkout_integration_customer_token` WHERE `customer_id` = '" . (int)$customer_id . "'");
	}
	
	public function editPayPalOrder(array $data): void {
		$sql = "UPDATE `" . DB_PREFIX . "paypal_checkout_integration_order` SET";

		$implode = [];
		
		if (!empty($data['paypal_order_id'])) {
			$implode[] = "`paypal_order_id` = '" . $this->db->escape($data['paypal_order_id']) . "'";
		}
		
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
		
		if (!empty($data['card_type'])) {
			$implode[] = "`card_type` = '" . $this->db->escape($data['card_type']) . "'";
		}
		
		if (!empty($data['card_nice_type'])) {
			$implode[] = "`card_nice_type` = '" . $this->db->escape($data['card_nice_type']) . "'";
		}
		
		if (!empty($data['card_last_digits'])) {
			$implode[] = "`card_last_digits` = '" . $this->db->escape($data['card_last_digits']) . "'";
		}
		
		if (!empty($data['card_expiry'])) {
			$implode[] = "`card_expiry` = '" . $this->db->escape($data['card_expiry']) . "'";
		}
		
		if (!empty($data['total'])) {
			$implode[] = "`total` = '" . (float)$data['total'] . "'";
		}
				
		if (!empty($data['currency_code'])) {
			$implode[] = "`currency_code` = '" . $this->db->escape($data['currency_code']) . "'";
		}
		
		if (!empty($data['environment'])) {
			$implode[] = "`environment` = '" . $this->db->escape($data['environment']) . "'";
		}
		
		if (isset($data['tracking_number'])) {
			$implode[] = "`tracking_number` = '" . $this->db->escape($data['tracking_number']) . "'";
		}
		
		if (isset($data['carrier_name'])) {
			$implode[] = "`carrier_name` = '" . $this->db->escape($data['carrier_name']) . "'";
		}
				
		if ($implode) {
			$sql .= implode(", ", $implode);
		}

		$sql .= " WHERE `order_id` = '" . (int)$data['order_id'] . "'";
		
		$this->db->query($sql);
	}
		
	public function deletePayPalOrder(int $order_id): void {
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "paypal_checkout_integration_order` WHERE `order_id` = '" . (int)$order_id . "'");
	}
	
	public function getPayPalOrder(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "paypal_checkout_integration_order` WHERE `order_id` = '" . (int)$order_id . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}
			
	public function setAgreeStatus(): void {
		$this->db->query("UPDATE " . DB_PREFIX . "country SET status = '0' WHERE (iso_code_2 = 'CU' OR iso_code_2 = 'IR' OR iso_code_2 = 'SY' OR iso_code_2 = 'KP')");
		$this->db->query("UPDATE " . DB_PREFIX . "zone SET status = '0' WHERE country_id = '220' AND (`code` = '43' OR `code` = '14' OR `code` = '09')");
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
	
	public function addOrderHistory(string $order_history_token, int $order_id, int $order_status_id, string $comment = '', bool $notify = false): bool {
		if (VERSION >= '4.0.2.0') {
			$separator = '.';
		} else {
			$separator = '|';
		}
		
		$data = array(
			'order_id' => $order_id,
			'order_status_id' => $order_status_id,
			'notify' => $notify,
			'comment' => $comment
		);
			
		$curl = curl_init();
			
		curl_setopt($curl, CURLOPT_URL, HTTP_CATALOG . 'index.php?route=extension/paypal/payment/paypal' . $separator . 'addOrderHistory&order_history_token=' . $order_history_token);
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
			
		$result = json_decode($response, true);
		
		if (!empty($result['success'])) {
			return true;
		} else {
			return false;
		} 		
	}
	
	public function checkVersion(string $opencart_version, string $paypal_version): array|bool {
		$curl = curl_init();
			
		curl_setopt($curl, CURLOPT_URL, 'https://www.opencart.com/index.php?route=api/promotion/paypalCheckoutIntegration&opencart=' . $opencart_version . '&paypal=' . $paypal_version);
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
	
	public function sendContact($data): void {
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
	
	public function install(): void {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paypal_checkout_integration_customer_token` (`customer_id` INT(11) NOT NULL, `payment_method` VARCHAR(20) NOT NULL, `vault_id` VARCHAR(50) NOT NULL, `vault_customer_id` VARCHAR(50) NOT NULL, `card_type` VARCHAR(40) NOT NULL, `card_nice_type` VARCHAR(40) NOT NULL, `card_last_digits` VARCHAR(4) NOT NULL, `card_expiry` VARCHAR(20) NOT NULL, `main_token_status` TINYINT(1) NOT NULL, PRIMARY KEY (`customer_id`, `payment_method`, `vault_id`), KEY `vault_customer_id` (`vault_customer_id`), KEY `main_token_status` (`main_token_status`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order` (`order_id` INT(11) NOT NULL, `paypal_order_id` VARCHAR(20) NOT NULL, `transaction_id` VARCHAR(20) NOT NULL, `transaction_status` VARCHAR(20) NOT NULL, `payment_method` VARCHAR(20) NOT NULL, `vault_id` VARCHAR(50) NOT NULL, `vault_customer_id` VARCHAR(50) NOT NULL, `card_type` VARCHAR(40) NOT NULL, `card_nice_type` VARCHAR(40) NOT NULL, `card_last_digits` VARCHAR(4) NOT NULL, `card_expiry` VARCHAR(20) NOT NULL, `total` DECIMAL(15,2) NOT NULL, `currency_code` VARCHAR(3) NOT NULL, `environment` VARCHAR(20) NOT NULL, `tracking_number` VARCHAR(64) NOT NULL, `carrier_name` VARCHAR(64) NOT NULL, PRIMARY KEY (`order_id`), KEY `paypal_order_id` (`paypal_order_id`), KEY `transaction_id` (`transaction_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
	}
	
	public function uninstall(): void {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "paypal_checkout_integration_customer_token`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order`");
	}
	
	public function update(): void {
		if ($this->config->get('paypal_version') < '3.1.0') {
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "paypal_checkout_integration_customer_token`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order`");
					
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paypal_checkout_integration_customer_token` (`customer_id` INT(11) NOT NULL, `payment_method` VARCHAR(20) NOT NULL, `vault_id` VARCHAR(50) NOT NULL, `vault_customer_id` VARCHAR(50) NOT NULL, `card_type` VARCHAR(40) NOT NULL, `card_nice_type` VARCHAR(40) NOT NULL, `card_last_digits` VARCHAR(4) NOT NULL, `card_expiry` VARCHAR(20) NOT NULL, `main_token_status` TINYINT(1) NOT NULL, PRIMARY KEY (`customer_id`, `payment_method`, `vault_id`), KEY `vault_customer_id` (`vault_customer_id`), KEY `main_token_status` (`main_token_status`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paypal_checkout_integration_order` (`order_id` INT(11) NOT NULL, `paypal_order_id` VARCHAR(20) NOT NULL, `transaction_id` VARCHAR(20) NOT NULL, `transaction_status` VARCHAR(20) NOT NULL, `payment_method` VARCHAR(20) NOT NULL, `vault_id` VARCHAR(50) NOT NULL, `vault_customer_id` VARCHAR(50) NOT NULL, `card_type` VARCHAR(40) NOT NULL, `card_nice_type` VARCHAR(40) NOT NULL, `card_last_digits` VARCHAR(4) NOT NULL, `card_expiry` VARCHAR(20) NOT NULL, `total` DECIMAL(15,2) NOT NULL, `currency_code` VARCHAR(3) NOT NULL, `environment` VARCHAR(20) NOT NULL, `tracking_number` VARCHAR(64) NOT NULL, `carrier_name` VARCHAR(64) NOT NULL, PRIMARY KEY (`order_id`), KEY `paypal_order_id` (`paypal_order_id`), KEY `transaction_id` (`transaction_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		}
	}
}
