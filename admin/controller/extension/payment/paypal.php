<?php
class ControllerExtensionPaymentPayPal extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('extension/payment/paypal');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_paypal', $this->request->post);
														
			$this->session->data['success'] = $this->language->get('success_save');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}
			
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/paypal', 'user_token=' . $this->session->data['user_token'], true)
		);
						
		$data['action'] = $this->url->link('extension/payment/paypal', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
		$data['partner_url'] = str_replace('&amp;', '%26', $this->url->link('extension/payment/paypal', 'user_token=' . $this->session->data['user_token'], true));
		$data['callback_url'] = str_replace('&amp;', '&', $this->url->link('extension/payment/paypal/callback', 'user_token=' . $this->session->data['user_token'], true));
		$data['configure_smart_button_url'] = $this->url->link('extension/payment/paypal/configureSmartButton', 'user_token=' . $this->session->data['user_token'], true);
		
		// Setting 		
		$_config = new Config();
		$_config->load('paypal');
		
		$data['setting'] = $_config->get('paypal_setting');
		
		if (isset($this->request->post['payment_paypal_environment'])) {
			$data['environment'] = $this->request->post['payment_paypal_environment'];
		} elseif ($this->config->get('payment_paypal_environment')) {
			$data['environment'] = $this->config->get('payment_paypal_environment');
		} else {
			$data['environment'] = 'sandbox';
		}
				
		$data['seller_nonce'] = token(50);
		
		$data['configure_url'] = array(
			'production' => array(
				'ppcp' => 'https://www.sandbox.paypal.com/bizsignup/partner/entry?partnerId=' . $data['setting']['partner']['production']['partner_id'] . '&partnerClientId=' . $data['setting']['partner']['production']['client_id'] . '&features=PAYMENT,REFUND&product=ppcp&integrationType=FO&returnToPartnerUrl=' . $data['partner_url'] . '&displayMode=minibrowser&sellerNonce=' . $data['seller_nonce'],
				'express_checkout' => 'https://www.sandbox.paypal.com/bizsignup/partner/entry?partnerId=' . $data['setting']['partner']['production']['partner_id'] . '&partnerClientId=' . $data['setting']['partner']['production']['client_id'] . '&features=PAYMENT,REFUND&product=EXPRESS_CHECKOUT&integrationType=FO&returnToPartnerUrl=' . $data['partner_url'] . '&displayMode=minibrowser&sellerNonce=' . $data['seller_nonce']
			),
			'sandbox' => array(
				'ppcp' => 'https://www.sandbox.paypal.com/bizsignup/partner/entry?partnerId=' . $data['setting']['partner']['sandbox']['partner_id'] . '&partnerClientId=' . $data['setting']['partner']['sandbox']['client_id'] . '&features=PAYMENT,REFUND&product=ppcp&integrationType=FO&returnToPartnerUrl=' . $data['partner_url'] . '&displayMode=minibrowser&sellerNonce=' . $data['seller_nonce'],
				'express_checkout' => 'https://www.sandbox.paypal.com/bizsignup/partner/entry?partnerId=' . $data['setting']['partner']['sandbox']['partner_id'] . '&partnerClientId=' . $data['setting']['partner']['sandbox']['client_id'] . '&features=PAYMENT,REFUND&product=EXPRESS_CHECKOUT&integrationType=FO&returnToPartnerUrl=' . $data['partner_url'] . '&displayMode=minibrowser&sellerNonce=' . $data['seller_nonce']
			)
		);
		
		$data['help_checkout_express'] = sprintf($this->language->get('help_checkout_express'), $data['configure_url'][$data['environment']]['express_checkout']);
			
		if (isset($this->session->data['authorization_code']) && isset($this->session->data['shared_id']) && isset($this->session->data['seller_nonce']) && isset($this->request->get['merchantIdInPayPal'])) {
			$shared_id = $this->session->data['shared_id'];
			
			$token_info = array(
				'grant_type' => 'authorization_code',
				'code' => $this->session->data['authorization_code'],
				'code_verifier' => $this->session->data['seller_nonce']
			);
						
			require_once DIR_SYSTEM .'library/paypal/paypal.php';
					
			$paypal = new PayPal($shared_id);
			
			$paypal->setAccessToken($token_info);
											
			$result = $paypal->getSellerCredentials($data['setting']['partner'][$data['environment']]['partner_id']);
			
			if (isset($result['client_id']) && isset($result['client_secret'])) {
				$client_id = $result['client_id'];
				$secret = $result['client_secret'];
			}
			
			if ($paypal->hasErrors()) {
				$this->error['warning'] = implode(' ', $paypal->getErrors());
			}
			
			$token_info = array(
				'grant_type' => 'client_credentials'
			);
			
			$merchant_id = $this->request->get['merchantIdInPayPal'];
						
			unset($this->session->data['authorization_code']);
			unset($this->session->data['shared_id']);
			unset($this->session->data['seller_nonce']);
		}
		
		if (isset($client_id)) {
			$data['client_id'] = $client_id;
		} elseif (isset($this->request->post['payment_paypal_client_id'])) {
			$data['client_id'] = $this->request->post['payment_paypal_client_id'];
		} else {
			$data['client_id'] = $this->config->get('payment_paypal_client_id');
		}

		if (isset($secret)) {
			$data['secret'] = $secret;
		} elseif (isset($this->request->post['payment_paypal_secret'])) {
			$data['secret'] = $this->request->post['payment_paypal_secret'];
		} else {
			$data['secret'] = $this->config->get('payment_paypal_secret');
		}
		
		if (isset($merchant_id)) {
			$data['merchant_id'] = $merchant_id;
		} elseif (isset($this->request->post['payment_paypal_merchant_id'])) {
			$data['merchant_id'] = $this->request->post['payment_paypal_merchant_id'];
		} else {
			$data['merchant_id'] = $this->config->get('payment_paypal_merchant_id');
		}
				
		$data['text_connect'] = sprintf($this->language->get('text_connect'), $data['client_id'], $data['secret'], $data['merchant_id']);
								
		if (isset($this->request->post['payment_paypal_transaction_method'])) {
			$data['transaction_method'] = $this->request->post['payment_paypal_transaction_method'];
		} else {
			$data['transaction_method'] = $this->config->get('payment_paypal_transaction_method');
		}

		if (isset($this->request->post['payment_paypal_total'])) {
			$data['total'] = $this->request->post['payment_paypal_total'];
		} else {
			$data['total'] = $this->config->get('payment_paypal_total');
		}

		if (isset($this->request->post['payment_paypal_order_status_id'])) {
			$data['order_status_id'] = $this->request->post['payment_paypal_order_status_id'];
		} else {
			$data['order_status_id'] = $this->config->get('payment_paypal_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_paypal_geo_zone_id'])) {
			$data['geo_zone_id'] = $this->request->post['payment_paypal_geo_zone_id'];
		} else {
			$data['geo_zone_id'] = $this->config->get('payment_paypal_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_paypal_status'])) {
			$data['status'] = $this->request->post['payment_paypal_status'];
		} else {
			$data['status'] = $this->config->get('payment_paypal_status');
		}

		if (isset($this->request->post['payment_paypal_sort_order'])) {
			$data['sort_order'] = $this->request->post['payment_paypal_sort_order'];
		} else {
			$data['sort_order'] = $this->config->get('payment_paypal_sort_order');
		}
						
		if (isset($this->request->post['payment_paypal_setting'])) {
			$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->request->post['payment_paypal_setting']);
		} else {
			$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('payment_paypal_setting'));
		}
		
		$token_info = array(
			'grant_type' => 'client_credentials'
		);	
										
		require_once DIR_SYSTEM .'library/paypal/paypal.php';
		
		$paypal = new PayPal($data['client_id'], $data['secret'], $data['environment']);
				
		$paypal->setAccessToken($token_info);
		
		$data['client_token'] = $paypal->getClientToken();
						
		if ($paypal->hasErrors()) {
			$this->error['warning'] = implode(' ', $paypal->getErrors());
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
					
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/paypal', $data));
	}
		
	public function callback() {
		if (isset($this->request->post['authorization_code']) && isset($this->request->post['shared_id']) && isset($this->request->post['seller_nonce'])) {
			$this->session->data['authorization_code'] = $this->request->post['authorization_code'];
			$this->session->data['shared_id'] = $this->request->post['shared_id'];
			$this->session->data['seller_nonce'] = $this->request->post['seller_nonce'];
		}
		
		$data['error'] = $this->error;
				
		$this->response->setOutput(json_encode($data));
    }
	
	public function configureSmartButton() {
		$this->load->model('extension/payment/paypal');
		
		$this->model_extension_payment_paypal->configureSmartButton();
		
		$this->response->redirect($this->url->link('extension/module/paypal_smart_button', 'user_token=' . $this->session->data['user_token'], true));
	}
				
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/paypal')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$token_info = array(
			'grant_type' => 'client_credentials'
		);	
		
		require_once DIR_SYSTEM .'library/paypal/paypal.php';
					
		$paypal = new PayPal($this->request->post['payment_paypal_client_id'], $this->request->post['payment_paypal_secret'], $this->request->post['payment_paypal_environment']);			
		$paypal->setAccessToken($token_info);
				
		if ($paypal->hasErrors()) {
			$this->error['warning'] = implode(' ', $paypal->getErrors());
		}
		
		return !$this->error;
	}
}