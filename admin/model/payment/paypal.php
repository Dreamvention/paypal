<?php
namespace Opencart\Admin\Model\Extension\PayPal\Payment;
class PayPal extends \Opencart\System\Engine\Model {
		
	public function configureSmartButton(): void {
		$this->load->model('user/user_group');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `code` = 'paypal_smart_button'");
        		
        if (empty($query->row)) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `extension` = 'paypal', `type` = 'module', `code` = 'paypal_smart_button'");

            $user_group_id = $this->user->getGroupId();
       									
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/paypal/module/paypal_smart_button');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/paypal/module/paypal_smart_button');
			
			$this->load->controller('extension/paypal/module/paypal_smart_button|install');
        }
	}
	
	public function log(array $data = [], string $title = ''): void {
		if ($this->config->get('payment_paypal_debug')) {
			$log = new \Opencart\System\Library\Log('paypal.log');
			$log->write('PayPal debug (' . $title . '): ' . json_encode($data));
		}
	}
}
