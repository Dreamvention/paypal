<?php
namespace Opencart\Catalog\Controller\Extension\PayPal\Module;
class PayPalSmartButton extends \Opencart\System\Engine\Controller {
	private $error = [];
		
	public function __construct($registry) {
		parent::__construct($registry);

		if (version_compare(phpversion(), '7.1', '>=')) {
			ini_set('precision', 14);
			ini_set('serialize_precision', 14);
		}
	}
	
	public function index(): string {	
		if ($this->config->get('payment_paypal_status') && $this->config->get('payment_paypal_client_id') && $this->config->get('payment_paypal_secret') && isset($this->request->get['route'])) {						
			$status = false;
			
			// Setting
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
			$_config->load('paypal');
			
			$paypal_setting = $_config->get('paypal_setting');
				
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
			$_config->load('paypal_smart_button');
						
			$config_setting = $_config->get('paypal_smart_button_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_paypal_smart_button_setting'));
			
			$currency_code = $this->session->data['currency'];
			$currency_value = $this->currency->getValue($this->session->data['currency']);
			
			if (empty($paypal_setting['currency'][$currency_code]['express_status'])) {
				$currency_code = $this->config->get('payment_paypal_currency_code');
				$currency_value = $this->config->get('payment_paypal_currency_value');
			}
			
			$decimal_place = $paypal_setting['currency'][$currency_code]['decimal_place'];
						
			if ($setting['page']['product']['status'] && ($this->request->get['route'] == 'product/product') && isset($this->request->get['product_id'])) {
				$data['insert_tag'] = html_entity_decode($setting['page']['product']['insert_tag']);
				$data['insert_type'] = $setting['page']['product']['insert_type'];
				$data['button_align'] = $setting['page']['product']['button_align'];
				$data['button_size'] = $setting['page']['product']['button_size'];
				$data['button_color'] = $setting['page']['product']['button_color'];
				$data['button_shape'] = $setting['page']['product']['button_shape'];
				$data['button_label'] = $setting['page']['product']['button_label'];
				$data['button_tagline'] = $setting['page']['product']['button_tagline'];	
				
				$data['button_enable_funding'] = [];
				$data['button_disable_funding'] = [];
				
				foreach ($setting['button_funding'] as $button_funding) {
					if ($setting['page']['product']['button_funding'][$button_funding['code']] == 1) {
						$data['button_enable_funding'][] = $button_funding['code'];
					} 
				
					if ($setting['page']['product']['button_funding'][$button_funding['code']] == 2) {
						$data['button_disable_funding'][] = $button_funding['code'];
					}
				}
				
				$data['message_status'] = $setting['page']['product']['message_status'];
				$data['message_align'] = $setting['page']['product']['message_align'];
				$data['message_size'] = $setting['page']['product']['message_size'];
				$data['message_layout'] = $setting['page']['product']['message_layout'];
				$data['message_text_color'] = $setting['page']['product']['message_text_color'];
				$data['message_text_size'] = $setting['page']['product']['message_text_size'];
				$data['message_flex_color'] = $setting['page']['product']['message_flex_color'];
				$data['message_flex_ratio'] = $setting['page']['product']['message_flex_ratio'];
				$data['message_placement'] = 'product';
				
				$product_id = (int)$this->request->get['product_id'];
		
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						if ((float)$product_info['special']) {
							$product_price = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], true);
						} else {
							$product_price = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], true);
						}
						
						$data['message_amount'] = number_format($product_price * $currency_value, $decimal_place, '.', '');
					} 			
				}
				
				$status = true;
			}
			
			if ($setting['page']['cart']['status'] && ($this->request->get['route'] == 'checkout/cart') && $this->cart->getTotal()) {
				$data['insert_tag'] = html_entity_decode($setting['page']['cart']['insert_tag']);
				$data['insert_type'] = $setting['page']['cart']['insert_type'];
				$data['button_align'] = $setting['page']['cart']['button_align'];
				$data['button_size'] = $setting['page']['cart']['button_size'];
				$data['button_color'] = $setting['page']['cart']['button_color'];
				$data['button_shape'] = $setting['page']['cart']['button_shape'];
				$data['button_label'] = $setting['page']['cart']['button_label'];
				$data['button_tagline'] = $setting['page']['cart']['button_tagline'];
				
				$data['button_enable_funding'] = [];
				$data['button_disable_funding'] = [];
				
				foreach ($setting['button_funding'] as $button_funding) {
					if ($setting['page']['cart']['button_funding'][$button_funding['code']] == 1) {
						$data['button_enable_funding'][] = $button_funding['code'];
					} 
				
					if ($setting['page']['cart']['button_funding'][$button_funding['code']] == 2) {
						$data['button_disable_funding'][] = $button_funding['code'];
					}
				}
				
				$data['message_status'] = $setting['page']['cart']['message_status'];
				$data['message_align'] = $setting['page']['cart']['message_align'];
				$data['message_size'] = $setting['page']['cart']['message_size'];
				$data['message_layout'] = $setting['page']['cart']['message_layout'];
				$data['message_text_color'] = $setting['page']['cart']['message_text_color'];
				$data['message_text_size'] = $setting['page']['cart']['message_text_size'];
				$data['message_flex_color'] = $setting['page']['cart']['message_flex_color'];
				$data['message_flex_ratio'] = $setting['page']['cart']['message_flex_ratio'];
				$data['message_placement'] = 'cart';
				
				$item_total = 0;
								
				foreach ($this->cart->getProducts() as $product) {
					$product_price = $this->tax->calculate($product['price'], $product['tax_class_id'], true);
									
					$item_total += $product_price * $product['quantity'];
				}
			
				$data['message_amount'] = number_format($item_total * $currency_value, $decimal_place, '.', '');
					
				$status = true;
			}
			
			if ($status) {				
				$this->load->model('localisation/country');
		
				$country = $this->model_localisation_country->getCountry($this->config->get('config_country_id'));
		
				$data['client_id'] = $this->config->get('payment_paypal_client_id');
				$data['merchant_id'] = $this->config->get('payment_paypal_merchant_id');
				$data['environment'] = $this->config->get('payment_paypal_environment');
				$data['partner_id'] = $paypal_setting['partner'][$data['environment']]['partner_id'];
				$data['partner_attribution_id'] = $paypal_setting['partner'][$data['environment']]['partner_attribution_id'];
				$data['transaction_method'] = $this->config->get('payment_paypal_transaction_method');					
				$data['locale'] = preg_replace('/-(.+?)+/', '', $this->config->get('config_language')) . '_' . $country['iso_code_2'];
				$data['currency_code'] = $currency_code;
						
				$data['button_width'] = $setting['button_width'][$data['button_size']];
				$data['message_width'] = $setting['message_width'][$data['message_size']];
				
				$data['language'] = $this->config->get('config_language');
												
				return $this->load->view('extension/paypal/module/paypal_smart_button', $data);
			}
		}
		
		return '';
	}
	
	public function createOrder(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');
		
		$this->load->model('extension/paypal/module/paypal_smart_button');
		
		$errors = [];
		
		$data['order_id'] = '';
		
		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				if (isset($this->request->post['quantity'])) {
					$quantity = (int)$this->request->post['quantity'];
				} else {
					$quantity = 1;
				}

				if (isset($this->request->post['option'])) {
					$option = array_filter($this->request->post['option']);
				} else {
					$option = [];
				}

				$product_options = $this->model_catalog_product->getOptions($this->request->post['product_id']);

				foreach ($product_options as $product_option) {
					if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
						$errors[] = sprintf($this->language->get('error_required'), $product_option['name']);
					}
				}
				
				if (isset($this->request->post['subscription_plan_id'])) {
					$subscription_plan_id = (int)$this->request->post['subscription_plan_id'];
				} else {
					$subscription_plan_id = 0;
				}

				$subscriptions = $this->model_catalog_product->getSubscriptions($product_info['product_id']);

				if ($subscriptions) {
					$subscription_plan_ids = [];

					foreach ($subscriptions as $subscription) {
						$subscription_plan_ids[] = $subscription['subscription_plan_id'];
					}

					if (!in_array($subscription_plan_id, $subscription_plan_ids)) {
						$errors[] = $this->language->get('error_subscription');
					}
				}

				if (!$errors) {					
					if (!$this->model_extension_paypal_module_paypal_smart_button->hasProductInCart($this->request->post['product_id'], $option, $subscription_plan_id)) {
						$this->cart->add($this->request->post['product_id'], $quantity, $option, $subscription_plan_id);
					}
					
					// Unset all shipping and payment methods
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				}					
			}
		}
		
		if (!$errors) {					
			// Setting
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
			$_config->load('paypal');
			
			$config_setting = $_config->get('paypal_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
		
			$client_id = $this->config->get('payment_paypal_client_id');
			$secret = $this->config->get('payment_paypal_secret');
			$environment = $this->config->get('payment_paypal_environment');
			$partner_id = $setting['partner'][$environment]['partner_id'];
			$partner_attribution_id = $setting['partner'][$environment]['partner_attribution_id'];
			$transaction_method = $this->config->get('payment_paypal_transaction_method');	
						
			$currency_code = $this->session->data['currency'];
			$currency_value = $this->currency->getValue($this->session->data['currency']);
				
			if (empty($setting['currency'][$currency_code]['express_status'])) {
				$currency_code = $this->config->get('payment_paypal_currency_code');
				$currency_value = $this->config->get('payment_paypal_currency_value');
			}
			
			$decimal_place = $setting['currency'][$currency_code]['decimal_place'];
					
			require_once DIR_EXTENSION . 'paypal/system/library/paypal.php';
		
			$paypal_info = [
				'partner_id' => $partner_id,
				'client_id' => $client_id,
				'secret' => $secret,
				'environment' => $environment,
				'partner_attribution_id' => $partner_attribution_id
			];
		
			$paypal = new \Opencart\System\Library\PayPal($paypal_info);
			
			$token_info = [
				'grant_type' => 'client_credentials'
			];	
				
			$paypal->setAccessToken($token_info);
						
			$item_info = [];
			
			$item_total = 0;
			$tax_total = 0;
				
			foreach ($this->cart->getProducts() as $product) {
				$product_price = number_format($product['price'] * $currency_value, $decimal_place, '.', '');
				
				$item_info[] = [
					'name' => $product['name'],
					'sku' => $product['model'],
					'url' => $this->url->link('language=' . $this->config->get('config_language') . '&product/product', 'product_id=' . $product['product_id']),
					'quantity' => $product['quantity'],
					'unit_amount' => [
						'currency_code' => $currency_code,
						'value' => $product_price
					]
				];
				
				$item_total += $product_price * $product['quantity'];
				
				if ($product['tax_class_id']) {
					$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

					foreach ($tax_rates as $tax_rate) {
						$tax_total += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
			
			$item_total = number_format($item_total, $decimal_place, '.', '');
			$tax_total = number_format($tax_total * $currency_value, $decimal_place, '.', '');
			$order_total = number_format($item_total + $tax_total, $decimal_place, '.', '');
						
			$amount_info = [
				'currency_code' => $currency_code,
				'value' => $order_total,
				'breakdown' => [
					'item_total' => [
						'currency_code' => $currency_code,
						'value' => $item_total
					],
					'tax_total' => [
						'currency_code' => $currency_code,
						'value' => $tax_total
					]
				]
			];
			
			if ($this->cart->hasShipping()) {			
				$shipping_preference = 'GET_FROM_FILE';
			} else {
				$shipping_preference = 'NO_SHIPPING';
			}
				
			$paypal_order_info = [
				'intent' => strtoupper($transaction_method),
				'purchase_units' => [
					[
						'reference_id' => 'default',
						'items' => $item_info,
						'amount' => $amount_info
					]
				],
				'application_context' => [
					'shipping_preference' => $shipping_preference
				]
			];
					
			$result = $paypal->createOrder($paypal_order_info);
			
			if ($paypal->hasErrors()) {
				$error_messages = [];
				
				$errors = $paypal->getErrors();
								
				foreach ($errors as $error) {
					if (isset($error['name']) && ($error['name'] == 'CURLE_OPERATION_TIMEOUTED')) {
						$error['message'] = $this->language->get('error_timeout');
					}
				
					if (isset($error['details'][0]['description'])) {
						$error_messages[] = $error['details'][0]['description'];
					} else {
						$error_messages[] = $error['message'];
					}
					
					$this->model_extension_paypal_module_paypal_smart_button->log($error, $error['message']);
				}
				
				$this->error['warning'] = implode(' ', $error_messages);
			}
		
			if ($this->error && isset($this->error['warning'])) {
				$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
			}
						
			$data['paypal_order_id'] = '';
		
			if (isset($result['id']) && isset($result['status']) && !$this->error) {
				$this->model_extension_paypal_module_paypal_smart_button->log($result, 'Create Order');
			
				if ($result['status'] == 'VOIDED') {
					$this->error['warning'] = sprintf($this->language->get('error_order_voided'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
				}
			
				if ($result['status'] == 'COMPLETED') {
					$this->error['warning'] = sprintf($this->language->get('error_order_completed'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
				}
			
				if (!$this->error) {
					$data['paypal_order_id'] = $result['id'];
				}
			}
		} else {
			$this->error['warning'] = implode(' ', $errors);
		}
		
		$data['language'] = $this->config->get('config_language');
				
		$data['error'] = $this->error;
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function approveOrder(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');
		
		$this->load->model('extension/paypal/module/paypal_smart_button');
		
		if (isset($this->request->post['paypal_order_id'])) {
			$this->session->data['paypal_order_id'] = $this->request->post['paypal_order_id'];
		} else {	
			$data['url'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($data));
		}
		
		// check checkout can continue due to stock checks or vouchers
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$data['url'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($data));
		}

		// If not guest checkout disabled, login require price or cart has downloads
		if (!$this->customer->isLogged() && (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price') || $this->cart->hasDownload() || $this->cart->hasSubscription())) {
			$data['url'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($data));
		}
		
		// Setting
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
		$_config->load('paypal');
			
		$config_setting = $_config->get('paypal_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
				
		$client_id = $this->config->get('payment_paypal_client_id');
		$secret = $this->config->get('payment_paypal_secret');
		$environment = $this->config->get('payment_paypal_environment');
		$partner_id = $setting['partner'][$environment]['partner_id'];
		$partner_attribution_id = $setting['partner'][$environment]['partner_attribution_id'];
		$transaction_method = $this->config->get('payment_paypal_transaction_method');

		$paypal_order_id = $this->session->data['paypal_order_id'];

		require_once DIR_EXTENSION . 'paypal/system/library/paypal.php';
		
		$paypal_info = [
			'partner_id' => $partner_id,
			'client_id' => $client_id,
			'secret' => $secret,
			'environment' => $environment,
			'partner_attribution_id' => $partner_attribution_id
		];
		
		$paypal = new \Opencart\System\Library\PayPal($paypal_info);
		
		$token_info = [
			'grant_type' => 'client_credentials'
		];	
						
		$paypal->setAccessToken($token_info);
			
		$paypal_order_info = $paypal->getOrder($paypal_order_id);
								
		if ($paypal->hasErrors()) {
			$error_messages = [];
				
			$errors = $paypal->getErrors();
								
			foreach ($errors as $error) {
				if (isset($error['name']) && ($error['name'] == 'CURLE_OPERATION_TIMEOUTED')) {
					$error['message'] = $this->language->get('error_timeout');
				}
					
				if (isset($error['details'][0]['description'])) {
					$error_messages[] = $error['details'][0]['description'];
				} else {
					$error_messages[] = $error['message'];
				}
					
				$this->model_extension_paypal_module_paypal_smart_button->log($error, $error['message']);
			}
				
			$this->error['warning'] = implode(' ', $error_messages);
		}
		
		if ($this->error && isset($this->error['warning'])) {
			$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
		}
		
		if ($paypal_order_info && !$this->error) {
			$this->load->model('account/customer');
			$this->load->model('account/address');
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			
			if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

				$this->session->data['customer']['customer_id'] = $this->customer->getId();
				$this->session->data['customer']['customer_group_id'] = $customer_info['customer_group_id'];
				$this->session->data['customer']['firstname'] = $customer_info['firstname'];
				$this->session->data['customer']['lastname'] = $customer_info['lastname'];
				$this->session->data['customer']['email'] = $customer_info['email'];
				$this->session->data['customer']['telephone'] = $customer_info['telephone'];
				$this->session->data['customer']['custom_field'] = json_decode($customer_info['custom_field'], true);
			} else {
				$this->session->data['customer']['customer_id'] = 0;
				$this->session->data['customer']['customer_group_id'] = $this->config->get('config_customer_group_id');
				$this->session->data['customer']['firstname'] = (isset($paypal_order_info['payer']['name']['given_name']) ? $paypal_order_info['payer']['name']['given_name'] : '');
				$this->session->data['customer']['lastname'] = (isset($paypal_order_info['payer']['name']['surname']) ? $paypal_order_info['payer']['name']['surname'] : '');
				$this->session->data['customer']['email'] = (isset($paypal_order_info['payer']['email_address']) ? $paypal_order_info['payer']['email_address'] : '');
				$this->session->data['customer']['telephone'] = '';
				$this->session->data['customer']['custom_field'] = [];
			}
								
			if ($this->customer->isLogged() && $this->customer->getAddressId()) {
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			} else {
				$this->session->data['payment_address']['firstname'] = (isset($paypal_order_info['payer']['name']['given_name']) ? $paypal_order_info['payer']['name']['given_name'] : '');
				$this->session->data['payment_address']['lastname'] = (isset($paypal_order_info['payer']['name']['surname']) ? $paypal_order_info['payer']['name']['surname'] : '');
				$this->session->data['payment_address']['company'] = '';
				$this->session->data['payment_address']['address_1'] = '';
				$this->session->data['payment_address']['address_2'] = '';
				$this->session->data['payment_address']['city'] = '';
				$this->session->data['payment_address']['postcode'] = '';
				$this->session->data['payment_address']['country'] = '';
				$this->session->data['payment_address']['country_id'] = '';
				$this->session->data['payment_address']['address_format'] = '';
				$this->session->data['payment_address']['zone'] = '';
				$this->session->data['payment_address']['zone_id'] = '';
				$this->session->data['payment_address']['custom_field'] = [];
			
				if (isset($paypal_order_info['payer']['address']['country_code'])) {
					$country_info = $this->model_extension_paypal_module_paypal_smart_button->getCountryByCode($paypal_order_info['payer']['address']['country_code']);
			
					if ($country_info) {
						$this->session->data['payment_address']['country'] = $country_info['name'];
						$this->session->data['payment_address']['country_id'] = $country_info['country_id'];
					}
				}
			}
				
			if ($this->cart->hasShipping()) {
				if ($this->customer->isLogged() && $this->customer->getAddressId()) {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				} else {
					if (isset($paypal_order_info['purchase_units'][0]['shipping']['name']['full_name'])) {
						$shipping_name = explode(' ', $paypal_order_info['purchase_units'][0]['shipping']['name']['full_name']);
						$shipping_firstname = $shipping_name[0];
						unset($shipping_name[0]);
						$shipping_lastname = implode(' ', $shipping_name);
					}
					
					$this->session->data['shipping_address']['firstname'] = (isset($shipping_firstname) ? $shipping_firstname : '');
					$this->session->data['shipping_address']['lastname'] = (isset($shipping_lastname) ? $shipping_lastname : '');
					$this->session->data['shipping_address']['company'] = '';
					$this->session->data['shipping_address']['address_1'] = (isset($paypal_order_info['purchase_units'][0]['shipping']['address']['address_line_1']) ? $paypal_order_info['purchase_units'][0]['shipping']['address']['address_line_1'] : '');
					$this->session->data['shipping_address']['address_2'] = (isset($paypal_order_info['purchase_units'][0]['shipping']['address']['address_line_2']) ? $paypal_order_info['purchase_units'][0]['shipping']['address']['address_line_2'] : '');
					$this->session->data['shipping_address']['city'] = (isset($paypal_order_info['purchase_units'][0]['shipping']['address']['admin_area_2']) ? $paypal_order_info['purchase_units'][0]['shipping']['address']['admin_area_2'] : '');
					$this->session->data['shipping_address']['postcode'] = (isset($paypal_order_info['purchase_units'][0]['shipping']['address']['postal_code']) ? $paypal_order_info['purchase_units'][0]['shipping']['address']['postal_code'] : '');
					$this->session->data['shipping_address']['country'] = '';
					$this->session->data['shipping_address']['country_id'] = '';
					$this->session->data['shipping_address']['address_format'] = '';
					$this->session->data['shipping_address']['zone'] = '';
					$this->session->data['shipping_address']['zone_id'] = '';
					$this->session->data['shipping_address']['custom_field'] = [];
									
					if (isset($paypal_order_info['purchase_units'][0]['shipping']['address']['country_code'])) {
						$country_info = $this->model_extension_paypal_module_paypal_smart_button->getCountryByCode($paypal_order_info['purchase_units'][0]['shipping']['address']['country_code']);
			
						if ($country_info) {
							$this->session->data['shipping_address']['country_id'] = $country_info['country_id'];
							$this->session->data['shipping_address']['country'] = $country_info['name'];
							$this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
													
							if (isset($paypal_order_info['purchase_units'][0]['shipping']['address']['admin_area_1'])) {
								$zone_info = $this->model_extension_paypal_module_paypal_smart_button->getZoneByCode($country_info['country_id'], $paypal_order_info['purchase_units'][0]['shipping']['address']['admin_area_1']);
			
								if ($zone_info) {
									$this->session->data['shipping_address']['zone_id'] = $zone_info['zone_id'];
									$this->session->data['shipping_address']['zone'] = $zone_info['name'];
								}
							}
						}
					}
				}
			}

			$data['url'] = $this->url->link('extension/paypal/module/paypal_smart_button|confirmOrder', 'language=' . $this->config->get('config_language'));			
		}
		
		$data['language'] = $this->config->get('config_language');
		
		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
	
	public function confirmOrder(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');
		$this->load->language('checkout/cart');

		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		
		if (!isset($this->session->data['paypal_order_id'])) {
			$this->response->redirect($this->url->link('checkout/cart', 'language=' . $this->config->get('config_language')));
		}
			
		// Coupon
		if (isset($this->request->post['coupon']) && $this->validateCoupon()) {
			$this->session->data['coupon'] = $this->request->post['coupon'];

			$this->session->data['success'] = $this->language->get('text_coupon');

			$this->response->redirect($this->url->link('extension/paypal/module/paypal_smart_button|confirmOrder', 'language=' . $this->config->get('config_language')));
		}

		// Voucher
		if (isset($this->request->post['voucher']) && $this->validateVoucher()) {
			$this->session->data['voucher'] = $this->request->post['voucher'];

			$this->session->data['success'] = $this->language->get('text_voucher');

			$this->response->redirect($this->url->link('extension/paypal/module/paypal_smart_button|confirmOrder', 'language=' . $this->config->get('config_language')));
		}

		// Reward
		if (isset($this->request->post['reward']) && $this->validateReward()) {
			$this->session->data['reward'] = abs($this->request->post['reward']);

			$this->session->data['success'] = $this->language->get('text_reward');

			$this->response->redirect($this->url->link('extension/paypal/module/paypal_smart_button|confirmOrder', 'language=' . $this->config->get('config_language')));
		}
		
		$this->document->setTitle($this->language->get('text_title'));
		
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/daterangepicker.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/daterangepicker.css');

		$data['heading_title'] = $this->language->get('text_title');

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'))
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('extension/paypal/module/paypal_smart_button|confirmOrder', 'language=' . $this->config->get('config_language'))
		];

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}
		
		if (isset($this->request->post['next'])) {
			$data['next'] = $this->request->post['next'];
		} else {
			$data['next'] = '';
		}
	
		$products = $this->cart->getProducts();

		if (empty($products)) {
			$this->response->redirect($this->url->link('checkout/cart', 'language=' . $this->config->get('config_language')));
		}

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

			if ($product['image']) {
				$image = $this->model_tool_image->resize(html_entity_decode($product['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			}

			$option_data = [];

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = [
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				];
			}

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

				$price = $this->currency->format($unit_price, $this->session->data['currency']);
				$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
			} else {
				$price = false;
				$total = false;
			}
			
			$description = '';

			if ($product['subscription']) {
				$trial_price = $this->currency->format($this->tax->calculate($product['subscription']['trial_price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$trial_cycle = $product['subscription']['trial_cycle'];
				$trial_frequency = $this->language->get('text_' . $product['subscription']['trial_frequency']);
				$trial_duration = $product['subscription']['trial_duration'];

				if ($product['subscription']['trial_status']) {
					$description .= sprintf($this->language->get('text_subscription_trial'), $trial_price, $trial_cycle, $trial_frequency, $trial_duration);
				}

				$price = $this->currency->format($this->tax->calculate($product['subscription']['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$cycle = $product['subscription']['cycle'];
				$frequency = $this->language->get('text_' . $product['subscription']['frequency']);
				$duration = $product['subscription']['duration'];

				if ($duration) {
					$description .= sprintf($this->language->get('text_subscription_duration'), $price, $cycle, $frequency, $duration);
				} else {
					$description .= sprintf($this->language->get('text_subscription_cancel'), $price, $cycle, $frequency);
				}
			}

			$data['products'][] = [
				'cart_id'               => $product['cart_id'],
				'thumb'                 => $image,
				'name'                  => $product['name'],
				'model'                 => $product['model'],
				'option'                => $option_data,
				'subscription' 			=> $description,
				'quantity'              => $product['quantity'],
				'stock'                 => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				'reward'                => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
				'price'                 => $price,
				'total'                 => $total,
				'href'                  => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $product['product_id'])
			];
		}

		// Gift Voucher
		$data['vouchers'] = [];
		
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$data['vouchers'][] = [
					'code'             => $voucher['code'],
					'description'      => $voucher['description'],
					'from_name'        => $voucher['from_name'],
					'from_email'       => $voucher['from_email'],
					'to_name'          => $voucher['to_name'],
					'to_email'         => $voucher['to_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'],
					'message'          => $voucher['message'],
					'amount'           => $voucher['amount']
				];
			}
		}
		
		$this->load->model('setting/extension');
		
		if ($this->cart->hasShipping()) {
			$data['has_shipping'] = true;
			
			$data['shipping_address'] = isset($this->session->data['shipping_address']) ? $this->session->data['shipping_address'] : [];
			
			if (!empty($data['shipping_address'])) {
				// Shipping Methods				
				$method_data = [];
				
				$results = $this->model_setting_extension->getExtensionsByType('shipping');

				if (!empty($results)) {
					foreach ($results as $result) {
						if ($this->config->get('shipping_' . $result['code'] . '_status')) {
							$this->load->model('extension/' . $result['extension'] . '/shipping/' . $result['code']);

							$quote = $this->{'model_extension_' . $result['extension'] . '_shipping_' . $result['code']}->getQuote($data['shipping_address']);

							if ($quote) {
								$method_data[$result['code']] = [
									'title'      => $quote['title'],
									'quote'      => $quote['quote'],
									'sort_order' => $quote['sort_order'],
									'error'      => $quote['error']
								];
							}
						}
					}

					if (!empty($method_data)) {
						$sort_order = [];

						foreach ($method_data as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}
						
						array_multisort($sort_order, SORT_ASC, $method_data);

						$this->session->data['shipping_methods'] = $method_data;
						$data['shipping_methods'] = $method_data;

						if (!isset($this->session->data['shipping_method'])) {
							//default the shipping to the very first option.
							$key1 = key($method_data);
							$key2 = key($method_data[$key1]['quote']);
							$this->session->data['shipping_method'] = $method_data[$key1]['quote'][$key2]['code'];
						}

						$data['code'] = $this->session->data['shipping_method'];
						$data['action_shipping'] = $this->url->link('extension/paypal/module/paypal_smart_button|confirmShipping', 'language=' . $this->config->get('config_language'));
					}
				} else {
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['shipping_method']);
					
					$data['error_no_shipping'] = $this->language->get('error_no_shipping');
				}
			}
		} else {
			$data['has_shipping'] = false;
		}
				
		$data['customer'] = isset($this->session->data['customer']) ? $this->session->data['customer'] : [];
		$data['payment_address'] = isset($this->session->data['payment_address']) ? $this->session->data['payment_address'] : [];	
		
		/**
		 * Payment methods
		 */
		$method_data = [];

		$results = $this->model_setting_extension->getExtensionsByType('payment');
		
		foreach ($results as $result) {
			if ($this->config->get('payment_' . $result['code'] . '_status')) {
				$this->load->model('extension/' . $result['extension'] . '/payment/' . $result['code']);

				$payment_method = $this->{'model_extension_' . $result['extension'] . '_payment_' . $result['code']}->getMethod($data['payment_address']);

				if ($payment_method) {
					$method_data[$result['code']] = $payment_method;
				}
			}
		}

		$sort_order = [];

		foreach ($method_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $method_data);
		
		$this->session->data['payment_methods'] = $method_data;
		$data['payment_methods'] = $method_data;

		if (!isset($method_data['paypal'])) {
			$this->session->data['error_warning'] = $this->language->get('error_unavailable');
			
			$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));
		}

		$this->session->data['payment_methods'] = $method_data;
		$this->session->data['payment_method'] = $method_data['paypal']['code'];
		
		// Custom Fields
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields();

		// Totals
		$totals = [];
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Display prices
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$sort_order = [];

			$results = $this->model_setting_extension->getExtensionsByType('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/' . $result['extension'] . '/total/' . $result['code']);

					// __call can not pass-by-reference so we get PHP to call it as an anonymous function.
					($this->{'model_extension_' . $result['extension'] . '_total_' . $result['code']}->getTotal)($totals, $taxes, $total);
				}
			}

			$sort_order = [];

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);
		}

		$data['totals'] = [];

		foreach ($totals as $total) {
			$data['totals'][] = [
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
			];
		}

		$data['action_confirm'] = $this->url->link('extension/paypal/module/paypal_smart_button|completeOrder', 'language=' . $this->config->get('config_language'));

		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->session->data['attention'])) {
			$data['attention'] = $this->session->data['attention'];
			unset($this->session->data['attention']);
		} else {
			$data['attention'] = '';
		}

		$data['modules'] = array();

		$extensions = $this->model_setting_extension->getExtensionsByType('total');

		foreach ($extensions as $extension) {
		    $result = $this->load->controller('extension/' . $extension['extension'] . '/total/' . $extension['code']);

			if (!$result instanceof \Exception) {
				$data['modules'][] = $result;
			}
		}
		
		$data['language'] = $this->config->get('config_language');
		
		$data['config_telephone_display'] = $this->config->get('config_telephone_display');
		$data['config_telephone_required'] = $this->config->get('config_telephone_required');
			
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/paypal/module/paypal_smart_button/confirm', $data));
	}
	
	public function completeOrder(): void {		
		$this->load->language('extension/paypal/module/paypal_smart_button');
						
		$this->load->model('extension/paypal/module/paypal_smart_button');
				
		// Validate if payment address has been set.
		if (empty($this->session->data['payment_address'])) {
			$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));
		}

		// Validate if payment method has been set.
		if (!isset($this->session->data['payment_method'])) {
			$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));
		}
		
		if ($this->cart->hasShipping()) {
			// Validate if shipping address has been set.
			if (empty($this->session->data['shipping_address'])) {
				$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));
			}

			// Validate if shipping method has been set.
			if (!isset($this->session->data['shipping_method'])) {
				$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));
			}
		} else {
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->response->redirect($this->url->link('checkout/cart', 'language=' . $this->config->get('config_language')));
		}
		
		if (isset($this->session->data['paypal_order_id'])) {			
			$order_data = [];

			// Totals
			$totals = [];
			$taxes = $this->cart->getTaxes();
			$total = 0;
		
			$sort_order = [];

			$results = $this->model_setting_extension->getExtensionsByType('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/' . $result['extension'] . '/total/' . $result['code']);

					// __call can not pass-by-reference so we get PHP to call it as an anonymous function.
					($this->{'model_extension_' . $result['extension'] . '_total_' . $result['code']}->getTotal)($totals, $taxes, $total);
				}
			}

			$sort_order = [];

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);
				
			$order_data['totals'] = $totals;

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');
			$order_data['store_url'] = $this->config->get('config_url');
										
			$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
			$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
			$order_data['firstname'] = $this->session->data['customer']['firstname'];
			$order_data['lastname'] = $this->session->data['customer']['lastname'];
			$order_data['email'] = $this->session->data['customer']['email'];
			$order_data['telephone'] = $this->session->data['customer']['telephone'];
			$order_data['custom_field'] = $this->session->data['customer']['custom_field'];
						
			$order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
			$order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
			$order_data['payment_company'] = $this->session->data['payment_address']['company'];
			$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
			$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
			$order_data['payment_city'] = $this->session->data['payment_address']['city'];
			$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
			$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
			$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
			$order_data['payment_country'] = $this->session->data['payment_address']['country'];
			$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
			$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
			$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : []);

			if (isset($this->session->data['payment_methods'][$this->session->data['payment_method']])) {
				$payment_method_info = $this->session->data['payment_methods'][$this->session->data['payment_method']];
			}
							
			if (isset($payment_method_info['title'])) {
				$order_data['payment_method'] = $payment_method_info['title'];
			} else {
				$order_data['payment_method'] = '';
			}

			if (isset($payment_method_info['code'])) {
				$order_data['payment_code'] = $payment_method_info['code'];
			} else {
				$order_data['payment_code'] = '';
			}			

			if ($this->cart->hasShipping()) {
				$order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
				$order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
				$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
				$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
				$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
				$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
				$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
				$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
				$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
				$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
				$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
				$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
				$order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : []);
				
				if (isset($this->session->data['shipping_method'])) {
					$shipping = explode('.', $this->session->data['shipping_method']);

					if (isset($shipping[0]) && isset($shipping[1]) && isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
						$shipping_method_info = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
					}
				}

				if (isset($shipping_method_info['title'])) {
					$order_data['shipping_method'] = $shipping_method_info['title'];
				} else {
					$order_data['shipping_method'] = '';
				}

				if (isset($shipping_method_info['code'])) {
					$order_data['shipping_code'] = $shipping_method_info['code'];
				} else {
					$order_data['shipping_code'] = '';
				}
			} else {
				$order_data['shipping_firstname'] = '';
				$order_data['shipping_lastname'] = '';
				$order_data['shipping_company'] = '';
				$order_data['shipping_address_1'] = '';
				$order_data['shipping_address_2'] = '';
				$order_data['shipping_city'] = '';
				$order_data['shipping_postcode'] = '';
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
				$order_data['shipping_address_format'] = '';
				$order_data['shipping_custom_field'] = [];
				$order_data['shipping_method'] = '';
				$order_data['shipping_code'] = '';
			}

			$order_data['products'] = [];

			foreach ($this->cart->getProducts() as $product) {
				$option_data = [];

				foreach ($product['option'] as $option) {
					$option_data[] = [
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					];
				}

				$order_data['products'][] = [
					'product_id' 	=> $product['product_id'],
					'master_id'  	=> $product['master_id'],
					'name'       	=> $product['name'],
					'model'      	=> $product['model'],
					'option'     	=> $option_data,
					'subscription' 	=> $product['subscription'],
					'download'   	=> $product['download'],
					'quantity'   	=> $product['quantity'],
					'subtract'   	=> $product['subtract'],
					'price'      	=> $product['price'],
					'total'      	=> $product['total'],
					'tax'       	=> $this->tax->getTax($product['price'], $product['tax_class_id']),
					'reward'     	=> $product['reward']
				];
			}

			// Gift Voucher
			$order_data['vouchers'] = [];

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$order_data['vouchers'][] = [
						'description'      => $voucher['description'],
						'code'             => token(10),
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $voucher['amount']
					];
				}
			}

			$order_data['comment'] = (isset($this->session->data['comment']) ? $this->session->data['comment'] : '');
			$order_data['total'] = $total;
			
			$order_data['affiliate_id'] = 0;
			$order_data['commission'] = 0;
			$order_data['marketing_id'] = 0;
			$order_data['tracking'] = '';
			
			if ($this->config->get('config_affiliate_status') && isset($this->session->data['tracking'])) {
				$subtotal = $this->cart->getSubTotal();

				// Affiliate
				$this->load->model('account/affiliate');

				$affiliate_info = $this->model_account_affiliate->getAffiliateByTracking($this->session->data['tracking']);

				if ($affiliate_info) {
					$order_data['affiliate_id'] = $affiliate_info['customer_id'];
					$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					$order_data['tracking'] = $this->session->data['tracking'];
				}
			}
			
			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['language_code'] = $this->config->get('config_language');
			
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
			
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}
			
			$this->load->model('checkout/order');

			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);
			
			// Setting
			$_config = new \Opencart\System\Engine\Config();
			$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
			$_config->load('paypal');
			
			$config_setting = $_config->get('paypal_setting');
		
			$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('payment_paypal_setting'));
			
			$client_id = $this->config->get('payment_paypal_client_id');
			$secret = $this->config->get('payment_paypal_secret');
			$environment = $this->config->get('payment_paypal_environment');
			$partner_id = $setting['partner'][$environment]['partner_id'];
			$partner_attribution_id = $setting['partner'][$environment]['partner_attribution_id'];
			$transaction_method = $this->config->get('payment_paypal_transaction_method');
			
			$currency_code = $this->session->data['currency'];
			$currency_value = $this->currency->getValue($this->session->data['currency']);
				
			if (empty($setting['currency'][$currency_code]['express_status'])) {
				$currency_code = $this->config->get('payment_paypal_currency_code');
				$currency_value = $this->config->get('payment_paypal_currency_value');
			}
			
			$decimal_place = $setting['currency'][$currency_code]['decimal_place'];
			
			require_once DIR_EXTENSION . 'paypal/system/library/paypal.php';
		
			$paypal_info = [
				'partner_id' => $partner_id,
				'client_id' => $client_id,
				'secret' => $secret,
				'environment' => $environment,
				'partner_attribution_id' => $partner_attribution_id
			];
		
			$paypal = new \Opencart\System\Library\PayPal($paypal_info);
			
			$token_info = [
				'grant_type' => 'client_credentials'
			];	
				
			$paypal->setAccessToken($token_info);
			
			$paypal_order_id = $this->session->data['paypal_order_id'];
			
			$paypal_order_info = [];
			
			$paypal_order_info[] = [
				'op' => 'add',
				'path' => '/purchase_units/@reference_id==\'default\'/description',
				'value' => 'Your order ' . $this->session->data['order_id']
			];
			
			$paypal_order_info[] = [
				'op' => 'add',
				'path' => '/purchase_units/@reference_id==\'default\'/invoice_id',
				'value' => $this->session->data['order_id']
			];
						
			$shipping_info = [];

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
				
				$paypal_order_info[] = [
					'op' => 'replace',
					'path' => '/purchase_units/@reference_id==\'default\'/shipping/name',
					'value' => $shipping_info['name']
				];
				
				$paypal_order_info[] = [
					'op' => 'replace',
					'path' => '/purchase_units/@reference_id==\'default\'/shipping/address',
					'value' => $shipping_info['address']
				];
			}
												
			$item_total = 0;
			$tax_total = 0;
				
			foreach ($this->cart->getProducts() as $product) {
				$product_price = number_format($product['price'] * $currency_value, $decimal_place, '.', '');
				
				$item_total += $product_price * $product['quantity'];
				
				if ($product['tax_class_id']) {
					$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

					foreach ($tax_rates as $tax_rate) {
						$tax_total += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
												
			$item_total = number_format($item_total, $decimal_place, '.', '');
			$tax_total = number_format($tax_total * $currency_value, $decimal_place, '.', '');
						
			$discount_total = 0;
			$handling_total = 0;
			$shipping_total = 0;
		
			if (isset($this->session->data['shipping_method'])) {
				$shipping = explode('.', $this->session->data['shipping_method']);

				if (isset($shipping[0]) && isset($shipping[1]) && isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
					$shipping_method_info = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
					
					$shipping_total = $this->tax->calculate($shipping_method_info['cost'], $shipping_method_info['tax_class_id'], true);
					$shipping_total = number_format($shipping_total * $currency_value, $decimal_place, '.', '');
				}
			}
		
			$order_total = number_format($order_data['total'] * $currency_value, $decimal_place, '.', '');
		
			$rebate = number_format($item_total + $tax_total + $shipping_total - $order_total, $decimal_place, '.', '');
		
			if ($rebate > 0) {
				$discount_total = $rebate;
			} elseif ($rebate < 0) {
				$handling_total = -$rebate;
			}

			$amount_info = [
				'currency_code' => $currency_code,
				'value' => $order_total,
				'breakdown' => [
					'item_total' => [
						'currency_code' => $currency_code,
						'value' => $item_total
					],
					'tax_total' => [
						'currency_code' => $currency_code,
						'value' => $tax_total
					],
					'shipping' => [
						'currency_code' => $currency_code,
						'value' => $shipping_total
					],
					'handling' => [
						'currency_code' => $currency_code,
						'value' => $handling_total
					],
					'discount' => [
						'currency_code' => $currency_code,
						'value' => $discount_total
					]
				]
			];
			
			$paypal_order_info[] = [
				'op' => 'replace',
				'path' => '/purchase_units/@reference_id==\'default\'/amount',
				'value' => $amount_info
			];
					
			$result = $paypal->updateOrder($paypal_order_id, $paypal_order_info);
			
			if ($paypal->hasErrors()) {
				$error_messages = [];
				
				$errors = $paypal->getErrors();
							
				foreach ($errors as $error) {
					if (isset($error['name']) && ($error['name'] == 'CURLE_OPERATION_TIMEOUTED')) {
						$error['message'] = $this->language->get('error_timeout');
					}
					
					if (isset($error['details'][0]['description'])) {
						$error_messages[] = $error['details'][0]['description'];
					} else {
						$error_messages[] = $error['message'];
					}
					
					$this->model_extension_paypal_module_paypal_smart_button->log($error, $error['message']);
				}
				
				$this->error['warning'] = implode(' ', $error_messages);
			}
			
			if ($this->error && isset($this->error['warning'])) {
				$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
			}
						
			if ($paypal_order_id && !$this->error) {				
				if ($transaction_method == 'authorize') {
					$result = $paypal->setOrderAuthorize($paypal_order_id);
				} else {
					$result = $paypal->setOrderCapture($paypal_order_id);
				}
			
				if ($paypal->hasErrors()) {
					$error_messages = [];
				
					$errors = $paypal->getErrors();
								
					foreach ($errors as $error) {
						if (isset($error['details'][0]['issue']) && ($error['details'][0]['issue'] == 'INSTRUMENT_DECLINED')) {
							$data['restart'] = true;
						}
					
						if (isset($error['name']) && ($error['name'] == 'CURLE_OPERATION_TIMEOUTED')) {
							$error['message'] = $this->language->get('error_timeout');
						}
					
						if (isset($error['details'][0]['description'])) {
							$error_messages[] = $error['details'][0]['description'];
						} else {
							$error_messages[] = $error['message'];
						}
					
						$this->model_extension_paypal_module_paypal_smart_button->log($error, $error['message']);
					}
				
					$this->error['warning'] = implode(' ', $error_messages);
				}
			
				if ($this->error && isset($this->error['warning'])) {
					$this->error['warning'] .= ' ' . sprintf($this->language->get('error_payment'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
				}
			
				if (!$this->error) {				
					if ($transaction_method == 'authorize') {
						$this->model_extension_paypal_module_paypal_smart_button->log($result, 'Authorize Order');
			
						if (isset($result['purchase_units'][0]['payments']['authorizations'][0]['status']) && isset($result['purchase_units'][0]['payments']['authorizations'][0]['seller_protection']['status'])) {
							$authorization_status = $result['purchase_units'][0]['payments']['authorizations'][0]['status'];
							$seller_protection_status = $result['purchase_units'][0]['payments']['authorizations'][0]['seller_protection']['status'];
							$order_status_id = 0;
						
							if (!$this->cart->hasShipping()) {
								$seller_protection_status = 'NOT_ELIGIBLE';
							}
						
							if ($authorization_status == 'CREATED') {
								$order_status_id = $setting['order_status']['pending']['id'];
							}

							if ($authorization_status == 'CAPTURED') {
								$this->error['warning'] = sprintf($this->language->get('error_authorization_captured'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
							}
						
							if ($authorization_status == 'DENIED') {
								$order_status_id = $setting['order_status']['denied']['id'];
							
								$this->error['warning'] = $this->language->get('error_authorization_denied');
							}
						
							if ($authorization_status == 'EXPIRED') {
								$this->error['warning'] = sprintf($this->language->get('error_authorization_expired'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
							}
						
							if ($authorization_status == 'PENDING') {
								$order_status_id = $setting['order_status']['pending']['id'];
							}
						
							if (($authorization_status == 'CREATED') || ($authorization_status == 'DENIED') || ($authorization_status == 'PENDING')) {
								$message = sprintf($this->language->get('text_order_message'), $seller_protection_status);
				
								$this->model_checkout_order->addHistory($this->session->data['order_id'], $order_status_id, $message);
							}
						
							if (($authorization_status == 'CREATED') || ($authorization_status == 'PARTIALLY_CAPTURED') || ($authorization_status == 'PARTIALLY_CREATED') || ($authorization_status == 'VOIDED') || ($authorization_status == 'PENDING')) {
								$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));
							}
						}
					} else {
						$this->model_extension_paypal_module_paypal_smart_button->log($result, 'Capture Order');
					
						if (isset($result['purchase_units'][0]['payments']['captures'][0]['status']) && isset($result['purchase_units'][0]['payments']['captures'][0]['seller_protection']['status'])) {
							$capture_status = $result['purchase_units'][0]['payments']['captures'][0]['status'];
							$seller_protection_status = $result['purchase_units'][0]['payments']['captures'][0]['seller_protection']['status'];
							$order_status_id = 0;

							if (!$this->cart->hasShipping()) {
								$seller_protection_status = 'NOT_ELIGIBLE';
							}
						
							if ($capture_status == 'COMPLETED') {
								$order_status_id = $setting['order_status']['completed']['id'];
							}
						
							if ($capture_status == 'DECLINED') {
								$order_status_id = $setting['order_status']['denied']['id'];
							
								$this->error['warning'] = $this->language->get('error_capture_declined');
							}
						
							if ($capture_status == 'FAILED') {
								$this->error['warning'] = sprintf($this->language->get('error_capture_failed'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
							}
						
							if ($capture_status == 'PENDING') {
								$order_status_id = $setting['order_status']['pending']['id'];
							}
						
							if (($capture_status == 'COMPLETED') || ($capture_status == 'DECLINED') || ($capture_status == 'PENDING')) {
								$message = sprintf($this->language->get('text_order_message'), $seller_protection_status);
				
								$this->model_checkout_order->addHistory($this->session->data['order_id'], $order_status_id, $message);
							}
						
							if (($capture_status == 'COMPLETED') || ($capture_status == 'PARTIALLY_REFUNDED') || ($capture_status == 'REFUNDED') || ($capture_status == 'PENDING')) {
								$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));
							}
						}
					}
				}
			}
		
			unset($this->session->data['paypal_order_id']);
			
			if ($this->error) {								
				$this->session->data['error'] = $this->error['warning'];
								
				$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));
			}
		}	
		
		$this->response->redirect($this->url->link('checkout/cart', 'language=' . $this->config->get('config_language')));
	}
	
	public function paymentAddress(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');
		
		$data['language'] = $this->config->get('config_language');
		
		$data['customer'] = isset($this->session->data['customer']) ? $this->session->data['customer'] : [];
		$data['payment_address'] = isset($this->session->data['payment_address']) ? $this->session->data['payment_address'] : [];
				
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();
		
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields();
		
		$this->response->setOutput($this->load->view('extension/paypal/module/paypal_smart_button/payment_address', $data));
	}
	
	public function shippingAddress(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');
		
		$data['language'] = $this->config->get('config_language');
		
		$data['shipping_address'] = isset($this->session->data['shipping_address']) ? $this->session->data['shipping_address'] : [];
				
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();
		
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields();
		
		$this->response->setOutput($this->load->view('extension/paypal/module/paypal_smart_button/shipping_address', $data));
	}
	
	public function confirmShipping(): void {
		$this->validateShipping($this->request->post['shipping_method']);

		$this->response->redirect($this->url->link('extension/paypal/module/paypal_smart_button|confirmOrder', 'language=' . $this->config->get('config_language')));
	}
	
	public function confirmPaymentAddress(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');
		
		$data['url'] = '';
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatePaymentAddress()) {			
			$this->session->data['customer']['firstname'] = $this->request->post['firstname'];
			$this->session->data['customer']['lastname'] = $this->request->post['lastname'];
			$this->session->data['customer']['email'] = $this->request->post['email'];
			$this->session->data['customer']['telephone'] = $this->request->post['telephone'];

			if (isset($this->request->post['custom_field']['account'])) {
				$this->session->data['customer']['custom_field'] = $this->request->post['custom_field']['account'];
			} else {
				$this->session->data['customer']['custom_field'] = [];
			}

			$this->session->data['payment_address']['firstname'] = $this->request->post['firstname'];
			$this->session->data['payment_address']['lastname'] = $this->request->post['lastname'];
			$this->session->data['payment_address']['company'] = $this->request->post['company'];
			$this->session->data['payment_address']['address_1'] = $this->request->post['address_1'];
			$this->session->data['payment_address']['address_2'] = $this->request->post['address_2'];
			$this->session->data['payment_address']['postcode'] = $this->request->post['postcode'];
			$this->session->data['payment_address']['city'] = $this->request->post['city'];
			$this->session->data['payment_address']['country_id'] = $this->request->post['country_id'];
			$this->session->data['payment_address']['zone_id'] = $this->request->post['zone_id'];

			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

			if ($country_info) {
				$this->session->data['payment_address']['country'] = $country_info['name'];
				$this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['payment_address']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['payment_address']['country'] = '';
				$this->session->data['payment_address']['iso_code_2'] = '';
				$this->session->data['payment_address']['iso_code_3'] = '';
				$this->session->data['payment_address']['address_format'] = '';
			}

			if (isset($this->request->post['custom_field']['address'])) {
				$this->session->data['payment_address']['custom_field'] = $this->request->post['custom_field']['address'];
			} else {
				$this->session->data['payment_address']['custom_field'] = [];
			}

			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

			if ($zone_info) {
				$this->session->data['payment_address']['zone'] = $zone_info['name'];
				$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['payment_address']['zone'] = '';
				$this->session->data['payment_address']['zone_code'] = '';
			}
			
			$data['url'] = $this->url->link('extension/paypal/module/paypal_smart_button|confirmOrder', 'language=' . $this->config->get('config_language'));
		}

		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
	
	public function confirmShippingAddress(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateShippingAddress()) {			
			$this->session->data['shipping_address']['firstname'] = $this->request->post['firstname'];
			$this->session->data['shipping_address']['lastname'] = $this->request->post['lastname'];
			$this->session->data['shipping_address']['company'] = $this->request->post['company'];
			$this->session->data['shipping_address']['address_1'] = $this->request->post['address_1'];
			$this->session->data['shipping_address']['address_2'] = $this->request->post['address_2'];
			$this->session->data['shipping_address']['postcode'] = $this->request->post['postcode'];
			$this->session->data['shipping_address']['city'] = $this->request->post['city'];
			$this->session->data['shipping_address']['country_id'] = $this->request->post['country_id'];
			$this->session->data['shipping_address']['zone_id'] = $this->request->post['zone_id'];

			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

			if ($country_info) {
				$this->session->data['shipping_address']['country'] = $country_info['name'];
				$this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['shipping_address']['country'] = '';
				$this->session->data['shipping_address']['iso_code_2'] = '';
				$this->session->data['shipping_address']['iso_code_3'] = '';
				$this->session->data['shipping_address']['address_format'] = '';
			}

			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

			if ($zone_info) {
				$this->session->data['shipping_address']['zone'] = $zone_info['name'];
				$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['shipping_address']['zone'] = '';
				$this->session->data['shipping_address']['zone_code'] = '';
			}

			if (isset($this->request->post['custom_field'])) {
				$this->session->data['shipping_address']['custom_field'] = $this->request->post['custom_field']['address'];
			} else {
				$this->session->data['shipping_address']['custom_field'] = [];
			}
			
			$data['url'] = $this->url->link('extension/paypal/module/paypal_smart_button|confirmOrder', 'language=' . $this->config->get('config_language'));
		}
		
		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
		
	private function validateShipping($code): bool {
		$this->load->language('checkout/cart');
		$this->load->language('extension/paypal/module/paypal_smart_button');

		if (empty($code)) {
			$this->session->data['error_warning'] = $this->language->get('error_shipping');
			
			return false;
		} else {
			$shipping = explode('.', $code);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$this->session->data['error_warning'] = $this->language->get('error_shipping');
				
				return false;
			} else {
				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]['code'];
				$this->session->data['success'] = $this->language->get('text_shipping_updated');
				
				return true;
			}
		}
	}
	
	private function validatePaymentAddress(): bool {
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ($this->config->get('config_telephone_required') && ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32))) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
			$this->error['address_1'] = $this->language->get('error_address_1');
		}

		if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}

		if ($this->request->post['country_id'] == '') {
			$this->error['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
			$this->error['zone'] = $this->language->get('error_zone');
		}
				
		$customer_group_id = $this->customer->getGroupId();
		
		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);
		
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
				$this->error['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['custom_field'][$custom_field['custom_field_id']])) {
				$this->error['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
			}
		}
		
		return !$this->error;
	}
	
	private function validateShippingAddress(): bool {
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
			$this->error['address_1'] = $this->language->get('error_address_1');
		}

		if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}

		if ($this->request->post['country_id'] == '') {
			$this->error['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
			$this->error['zone'] = $this->language->get('error_zone');
		}
		
		$customer_group_id = $this->customer->getGroupId();
		
		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
					$this->error['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['custom_field'][$custom_field['custom_field_id']])) {
					$this->error['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
				}
			}
		}
		
		return !$this->error;
	}
		
	private function validateCoupon(): bool {
		$this->load->model('marketing/coupon');

		$coupon_info = $this->model_marketing_coupon->getCoupon($this->request->post['coupon']);

		if ($coupon_info) {
			return true;
		} else {
			$this->session->data['error_warning'] = $this->language->get('error_coupon');
			
			return false;
		}
	}

	private function validateVoucher(): bool {
		$this->load->model('checkout/voucher');

		$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);
		
		if ($voucher_info) {
			return true;
		} else {
			$this->session->data['error_warning'] = $this->language->get('error_voucher');
			
			return false;
		}
	}

	private function validateReward(): bool {
		$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		$error = '';

		if (empty($this->request->post['reward'])) {
			$error = $this->language->get('error_reward');
		}

		if ($this->request->post['reward'] > $points) {
			$error = sprintf($this->language->get('error_points'), $this->request->post['reward']);
		}

		if ($this->request->post['reward'] > $points_total) {
			$error = sprintf($this->language->get('error_maximum'), $points_total);
		}

		if (!$error) {
			return true;
		} else {
			$this->session->data['error_warning'] = $error;
			
			return false;
		}
	}
}
