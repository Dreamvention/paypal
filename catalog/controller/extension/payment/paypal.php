<?php
class ControllerExtensionPaymentPayPal extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('extension/payment/paypal');
		
		$this->load->model('extension/payment/paypal');
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
										
		require_once DIR_SYSTEM .'library/paypal/paypal.php';
		
		$paypal = new PayPal($data['client_id'], $data['secret'], $data['environment']);
		
		$token_info = array(
			'grant_type' => 'client_credentials'
		);	
				
		$paypal->setAccessToken($token_info);
		
		$data['client_token'] = $paypal->getClientToken();
						
		if ($paypal->hasErrors()) {
			$error_title = array();
				
			$errors = $paypal->getErrors();
								
			foreach ($errors as $error) {
				$error_title[] = $error['title'];
					
				$this->model_extension_payment_paypal->log($error['data'], $error['title']);
			}
				
			$this->error['warning'] = implode(' ', $error_title);
		}			

		return $this->load->view('extension/payment/paypal', $data);
	}
		
	public function createOrder() {					
		$this->load->model('extension/payment/paypal');
		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$client_id = $this->config->get('payment_paypal_client_id');
		$secret = $this->config->get('payment_paypal_secret');
		$merchant_id = $this->config->get('payment_paypal_merchant_id');
		$environment = $this->config->get('payment_paypal_environment');
		$transaction_method = $this->config->get('payment_paypal_transaction_method');	
		
		require_once DIR_SYSTEM . 'library/paypal/paypal.php';
		
		$paypal = new PayPal($client_id, $secret, $environment);
		
		$token_info = array(
			'grant_type' => 'client_credentials'
		);	
		
		$paypal->setAccessToken($token_info);
		
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
			
			$shipping_preference = 'GET_FROM_FILE';
		} else {
			$shipping_preference = 'NO_SHIPPING';
		}
		
		$item_info = array();
				
		foreach ($this->cart->getProducts() as $product) {
			$item_info[] = array(
				'name' => $product['name'],
				'sku' => $product['model'],
				'url' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				'quantity' => $product['quantity'],
				'unit_amount' => array(
					'currency_code' => $order_info['currency_code'],
					'value' => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], false)
				)
			);
		}
				
		$sub_total = $this->cart->getSubTotal();
		$total = $this->cart->getTotal();
		$tax_total = $total - $sub_total;
						
		$discount_total = 0;
		$handling_total = 0;
		$shipping_total = 0;
		
		if (isset($this->session->data['shipping_method'])) {
			$shipping_total = $this->tax->calculate($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id'], $this->config->get('config_tax'));
		}
		
		$rebate = $sub_total + $tax_total + $shipping_total - $order_info['total'];
		
		if ($rebate > 0) {
			$discount_total = $rebate;
		} elseif ($rebate < 0) {
			$handling_total = -$rebate;
		}

		$amount_info = array(
			'currency_code' => $order_info['currency_code'],
			'value' => $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false),
			'breakdown' => array(
				'item_total' => array(
					'currency_code' => $order_info['currency_code'],
					'value' => $this->currency->format($sub_total, $order_info['currency_code'], $order_info['currency_value'], false)
				),
				'tax_total' => array(
					'currency_code' => $order_info['currency_code'],
					'value' => $this->currency->format($tax_total, $order_info['currency_code'], $order_info['currency_value'], false)
				),
				'shipping' => array(
					'currency_code' => $order_info['currency_code'],
					'value' => $this->currency->format($shipping_total, $order_info['currency_code'], $order_info['currency_value'], false)
				),
				'handling' => array(
					'currency_code' => $order_info['currency_code'],
					'value' => $this->currency->format($handling_total, $order_info['currency_code'], $order_info['currency_value'], false)
				),
				'discount' => array(
					'currency_code' => $order_info['currency_code'],
					'value' => $this->currency->format($discount_total, $order_info['currency_code'], $order_info['currency_value'], false)
				)
			)
		);
	
		$order_info = array(
			'intent' => strtoupper($transaction_method),
			'purchase_units' => array(
				array(
					'reference_id' => 'default',
					'description' => 'Your order ' . $order_info['order_id'],
					'invoice_id' => $order_info['order_id'],
					'shipping' => $shipping_info,
					'items' => $item_info,
					'amount' => $amount_info
				)
			),
			'application_context' => array(
				'shipping_preference' => $shipping_preference
			)
		);

		$result = $paypal->createOrder($order_info);
			
		$data['order_id'] = '';
		
		if (isset($result['id'])) {
			$data['order_id'] = $result['id'];
		}
		
		if ($paypal->hasErrors()) {
			$error_title = array();
				
			$errors = $paypal->getErrors();
								
			foreach ($errors as $error) {
				$error_title[] = $error['title'];
					
				$this->model_extension_payment_paypal->log($error['data'], $error['title']);
			}
				
			$this->error['warning'] = implode(' ', $error_title);
		}
				
		$data['error'] = $this->error;
				
		$this->response->setOutput(json_encode($data));
	}
	
	public function approveOrder() {
		$this->load->language('extension/payment/paypal');
		
		$this->load->model('extension/payment/paypal');
		
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
			
			require_once DIR_SYSTEM .'library/paypal/paypal.php';
		
			$paypal = new PayPal($client_id, $secret, $environment);
			
			$token_info = array(
				'grant_type' => 'client_credentials'
			);	
		
			$paypal->setAccessToken($token_info);
		
			if ($transaction_method == 'authorize') {
				$result = $paypal->setOrderAuthorize($order_id);
			} else {
				$result = $paypal->setOrderCapture($order_id);
			}
			
			if ($paypal->hasErrors()) {
				$error_title = array();
				
				$errors = $paypal->getErrors();
								
				foreach ($errors as $error) {
					$error_title[] = $error['title'];
					
					$this->model_extension_payment_paypal->log($error['data'], $error['title']);
				}
				
				$this->error['warning'] = implode(' ', $error_title);
			}
		
			if (!$this->error) {			
				$data['success'] = $this->url->link('checkout/success');
			}
		}
		
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function webhook() {
		$this->load->model('extension/payment/paypal');
		
		$webhook_data = json_decode(html_entity_decode(file_get_contents('php://input')), true);
		
		$this->model_extension_payment_paypal->log($webhook_data, 'Webhook');
		
		if (isset($webhook_data['event_type']) && isset($webhook_data['resource']['invoice_id'])) {
			// Setting
			$_config = new Config();
			$_config->load('paypal');
			
			$config_setting = $_config->get('paypal_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
			
			$order_id = $webhook_data['resource']['invoice_id'];
									
			if ($webhook_data['event_type'] == 'PAYMENT.AUTHORIZATION.CREATED') {
				$order_status_id = $setting['order_status']['pending']['id'];
			}
		
			if ($webhook_data['event_type'] == 'PAYMENT.AUTHORIZATION.VOIDED') {
				$order_status_id = $setting['order_status']['voided']['id'];
			}
			
			if ($webhook_data['event_type'] == 'PAYMENT.CAPTURE.COMPLETED') {
				$order_status_id = $setting['order_status']['completed']['id'];
			}
		
			if ($webhook_data['event_type'] == 'PAYMENT.CAPTURE.DENIED') {
				$order_status_id = $setting['order_status']['denied']['id'];
			}
		
			if ($webhook_data['event_type'] == 'PAYMENT.CAPTURE.PENDING') {
				$order_status_id = $setting['order_status']['pending']['id'];
			}
		
			if ($webhook_data['event_type'] == 'PAYMENT.CAPTURE.REFUNDED') {
				$order_status_id = $setting['order_status']['refunded']['id'];
			}
		
			if ($webhook_data['event_type'] == 'PAYMENT.CAPTURE.REVERSED') {
				$order_status_id = $setting['order_status']['reversed']['id'];
			}
		
			if ($webhook_data['event_type'] == 'CHECKOUT.ORDER.COMPLETED') {
				$order_status_id = $setting['order_status']['processed']['id'];
			}
		
			if ($webhook_data['event_type'] == 'CHECKOUT.ORDER.APPROVED') {
				$order_status_id = $setting['order_status']['pending']['id'];
			}
			
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
		}
	}
}