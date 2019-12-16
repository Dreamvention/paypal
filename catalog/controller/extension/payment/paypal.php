<?php
class ControllerExtensionPaymentPayPal extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('extension/payment/paypal');
		
		$this->load->model('localisation/country');
				
		$country = $this->model_localisation_country->getCountry($this->config->get('config_country_id'));
				
		// Setting
		$_config = new Config();
		$_config->load('paypal');
			
		$config_setting = $_config->get('paypal_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
						
		$data['client_id'] = $this->config->get('payment_paypal_client_id');
		$data['secret'] = $this->config->get('payment_paypal_secret');
		$data['merchant_id'] = $this->config->get('payment_paypal_merchant_id');
		$data['environment'] = $this->config->get('payment_paypal_environment');
		$data['partner_id'] = $setting['partner'][$data['environment']]['partner_id'];
		$data['transaction_method'] = $this->config->get('payment_paypal_transaction_method');
		$data['locale'] = preg_replace('/-(.+?)+/', '', $this->config->get('config_language')) . '_' . $country['iso_code_2'];
		$data['currency_code'] = $this->session->data['currency'];
		
		$data['express_status'] = $setting['checkout']['express']['status'];		
		$data['button_align'] = $setting['checkout']['express']['button_align'];
		$data['button_size'] = $setting['checkout']['express']['button_size'];
		$data['button_color'] = $setting['checkout']['express']['button_color'];
		$data['button_shape'] = $setting['checkout']['express']['button_shape'];
		$data['button_label'] = $setting['checkout']['express']['button_label'];

		$data['button_width'] = $setting['button_width'][$data['button_size']];
						
		$data['card_status'] = $setting['checkout']['card']['status'];
		$data['form_align'] = $setting['checkout']['card']['form_align'];
		$data['form_size'] = $setting['checkout']['card']['form_size'];
		$data['form_width'] = $setting['form_width'][$data['form_size']];
		$data['secure_status'] = $setting['checkout']['card']['secure_status'];
				
		$data['order_id'] = $this->session->data['order_id'];

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

		return $this->load->view('extension/payment/paypal', $data);
	}
		
	public function createOrder() {					
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$total_price = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$currency_code = $order_info['currency_code'];

		$client_id = $this->config->get('payment_paypal_client_id');
		$secret = $this->config->get('payment_paypal_secret');
		$merchant_id = $this->config->get('payment_paypal_merchant_id');
		$environment = $this->config->get('payment_paypal_environment');
		$transaction_method = $this->config->get('payment_paypal_transaction_method');	
		
		$shipping_info = array();

		if ($this->cart->hasShipping()) {
			$shipping_info['name']['full_name'] = (isset($this->session->data['shipping_address']['firstname']) ? $this->session->data['shipping_address']['firstname'] : '');
			$shipping_info['name']['full_name'] .= (isset($this->session->data['shipping_address']['lastname']) ? (' ' . $this->session->data['shipping_address']['lastname']) : '');			
			$shipping_info['address']['address_line_1'] = (isset($this->session->data['shipping_address']['address_1']) ? $this->session->data['shipping_address']['address_1'] : '');
			$shipping_info['address']['address_line_2'] = (isset($this->session->data['shipping_address']['address_2']) ? $this->session->data['shipping_address']['address_2'] : '');			
			$shipping_info['address']['admin_area_1'] = (isset($this->session->data['shipping_address']['zone']) ? $this->session->data['shipping_address']['zone'] : '');
			$shipping_info['address']['admin_area_2'] = (isset($this->session->data['shipping_address']['city']) ? $this->session->data['shipping_address']['city'] : '');
			$shipping_info['address']['postal_code'] = (isset($this->session->data['shipping_address']['postcode']) ? $this->session->data['shipping_address']['postcode'] : '');
			
			if (isset($this->session->data['shipping_address']['country_id'])) {
				$this->load->model('localisation/country');
				
				$country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_address']['country_id']);
			
				if ($country_info) {
					$shipping_info['address']['country_code'] = $country_info['iso_code_2'];
				}
			}
		}
			
		$token_info = array(
			'grant_type' => 'client_credentials'
		);	
				
		$order_info = array(
			'intent' => strtoupper($transaction_method),
			'purchase_units' => array(
				array(
					'reference_id' => 'default',
					'amount' => array(
						'currency_code' => $currency_code,
						'value' => $total_price
					),
					'shipping' => $shipping_info
				)
			)
		);
		$data['order_info'] = 	$order_info;	
		require_once DIR_SYSTEM .'library/paypal/paypal.php';
		
		$paypal = new PayPal($client_id, $secret, $environment);
				
		$paypal->setAccessToken($token_info);
		
		$result = $paypal->createOrder($order_info);
			
		$data['order_id'] = '';
		
		if (isset($result['id'])) {
			$data['order_id'] = $result['id'];
		}
		
		if ($paypal->hasErrors()) {
			$this->error['warning'] = implode(' ', $paypal->getErrors());
		}
				
		$data['error'] = $this->error;
				
		$this->response->setOutput(json_encode($data));
	}
	
	public function approveOrder() {
		$this->load->language('extension/payment/paypal');
		
		if (isset($this->request->post['order_id'])) {
			$order_id = $this->request->post['order_id'];
		}
		
		if (isset($this->request->post['payload'])) {
			$payload = json_decode(htmlspecialchars_decode($this->request->post['payload']), true);
			
			if (isset($payload['orderId'])) {
				$order_id = $payload['orderId'];
			
				// Setting
				$_config = new Config();
				$_config->load('paypal');
			
				$config_setting = $_config->get('paypal_setting');
		
				$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
			
				if ($setting['checkout']['card']['secure_status']) {					
					$secure_scenario_code = isset($payload['authenticationReason']) ? strtolower($payload['authenticationReason']) : 'undefined';
					
					if (isset($setting['secure_scenario'][$secure_scenario_code]) && isset($setting['checkout']['card']['secure_scenario'][$secure_scenario_code]) && !$setting['checkout']['card']['secure_scenario'][$secure_scenario_code]) {
						$this->error['warning'] = $this->language->get($setting['secure_scenario'][$secure_scenario_code]['error']);
					}
				}
		
				if ($this->error && isset($this->error['warning'])) {
					$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact'));
				}
			}
		}
					
		if (isset($order_id) && !$this->error) {
			$client_id = $this->config->get('payment_paypal_client_id');
			$secret = $this->config->get('payment_paypal_secret');
			$environment = $this->config->get('payment_paypal_environment');
			$transaction_method = $this->config->get('payment_paypal_transaction_method');
			
			$token_info = array(
				'grant_type' => 'client_credentials'
			);	
						
			require_once DIR_SYSTEM .'library/paypal/paypal.php';
		
			$paypal = new PayPal($client_id, $secret, $environment);
						
			$paypal->setAccessToken($token_info);
		
			if ($transaction_method == 'authorize') {
				$result = $paypal->setOrderAuthorize($order_id);
			} else {
				$result = $paypal->setOrderCapture($order_id);
			}
			
			if ($paypal->hasErrors()) {
				$this->error['warning'] = implode(' ', $paypal->getErrors());
			}
		
			if (!$this->error) {
				$this->load->model('checkout/order');

				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_paypal_order_status_id'));
			
				$data['success'] = $this->url->link('checkout/success');
			}
		}
		
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
}