<?php
class ControllerPaymentPayPal extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('payment/paypal');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('payment/paypal');
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paypal', $this->request->post);
														
			$this->session->data['success'] = $this->language->get('success_save');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_auto'] = $this->language->get('text_auto');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_general'] = $this->language->get('text_general');
		$data['text_order_status'] = $this->language->get('text_order_status');
		$data['text_checkout_express'] = $this->language->get('text_checkout_express');
		$data['text_checkout_card'] = $this->language->get('text_checkout_card');
		$data['text_checkout_message'] = $this->language->get('text_checkout_message');
		$data['text_production'] = $this->language->get('text_production');
		$data['text_sandbox'] = $this->language->get('text_sandbox');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_sale'] = $this->language->get('text_sale');
		$data['text_connect'] = $this->language->get('text_connect');
		$data['text_message'] = $this->language->get('text_message');
		$data['text_currency_aud'] = $this->language->get('text_currency_aud');
		$data['text_currency_brl'] = $this->language->get('text_currency_brl');
		$data['text_currency_cad'] = $this->language->get('text_currency_cad');
		$data['text_currency_czk'] = $this->language->get('text_currency_czk');
		$data['text_currency_dkk'] = $this->language->get('text_currency_dkk');
		$data['text_currency_eur'] = $this->language->get('text_currency_eur');
		$data['text_currency_hkd'] = $this->language->get('text_currency_hkd');
		$data['text_currency_huf'] = $this->language->get('text_currency_huf');
		$data['text_currency_inr'] = $this->language->get('text_currency_inr');
		$data['text_currency_ils'] = $this->language->get('text_currency_ils');
		$data['text_currency_jpy'] = $this->language->get('text_currency_jpy');
		$data['text_currency_myr'] = $this->language->get('text_currency_myr');
		$data['text_currency_mxn'] = $this->language->get('text_currency_mxn');
		$data['text_currency_twd'] = $this->language->get('text_currency_twd');
		$data['text_currency_nzd'] = $this->language->get('text_currency_nzd');
		$data['text_currency_nok'] = $this->language->get('text_currency_nok');
		$data['text_currency_php'] = $this->language->get('text_currency_php');
		$data['text_currency_pln'] = $this->language->get('text_currency_pln');
		$data['text_currency_gbp'] = $this->language->get('text_currency_gbp');
		$data['text_currency_rub'] = $this->language->get('text_currency_rub');
		$data['text_currency_sgd'] = $this->language->get('text_currency_sgd');
		$data['text_currency_sek'] = $this->language->get('text_currency_sek');
		$data['text_currency_chf'] = $this->language->get('text_currency_chf');
		$data['text_currency_thb'] = $this->language->get('text_currency_thb');
		$data['text_currency_usd'] = $this->language->get('text_currency_usd');
		$data['text_completed_status'] = $this->language->get('text_completed_status');
		$data['text_denied_status'] = $this->language->get('text_denied_status');
		$data['text_failed_status'] = $this->language->get('text_failed_status');
		$data['text_pending_status'] = $this->language->get('text_pending_status');
		$data['text_refunded_status'] = $this->language->get('text_refunded_status');
		$data['text_reversed_status'] = $this->language->get('text_reversed_status');
		$data['text_voided_status'] = $this->language->get('text_voided_status');
		$data['text_align_left'] = $this->language->get('text_align_left');
		$data['text_align_center'] = $this->language->get('text_align_center');
		$data['text_align_right'] = $this->language->get('text_align_right');
		$data['text_small'] = $this->language->get('text_small');
		$data['text_medium'] = $this->language->get('text_medium');
		$data['text_large'] = $this->language->get('text_large');
		$data['text_responsive'] = $this->language->get('text_responsive');
		$data['text_gold'] = $this->language->get('text_gold');
		$data['text_blue'] = $this->language->get('text_blue');
		$data['text_silver'] = $this->language->get('text_silver');
		$data['text_white'] = $this->language->get('text_white');
		$data['text_black'] = $this->language->get('text_black');
		$data['text_pill'] = $this->language->get('text_pill');
		$data['text_rect'] = $this->language->get('text_rect');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_pay'] = $this->language->get('text_pay');
		$data['text_buy_now'] = $this->language->get('text_buy_now');
		$data['text_pay_pal'] = $this->language->get('text_pay_pal');
		$data['text_installment'] = $this->language->get('text_installment');
		$data['text_card'] = $this->language->get('text_card');
		$data['text_credit'] = $this->language->get('text_credit');
		$data['text_bancontact'] = $this->language->get('text_bancontact');
		$data['text_blik'] = $this->language->get('text_blik');
		$data['text_eps'] = $this->language->get('text_eps');
		$data['text_giropay'] = $this->language->get('text_giropay');
		$data['text_ideal'] = $this->language->get('text_ideal');
		$data['text_mercadopago'] = $this->language->get('text_mercadopago');
		$data['text_mybank'] = $this->language->get('text_mybank');
		$data['text_p24'] = $this->language->get('text_p24');
		$data['text_sepa'] = $this->language->get('text_sepa');
		$data['text_sofort'] = $this->language->get('text_sofort');
		$data['text_venmo'] = $this->language->get('text_venmo');
		$data['text_paylater'] = $this->language->get('text_paylater');
		$data['text_text'] = $this->language->get('text_text');
		$data['text_flex'] = $this->language->get('text_flex');
		$data['text_accept'] = $this->language->get('text_accept');
		$data['text_decline'] = $this->language->get('text_decline');
		$data['text_recommended'] = $this->language->get('text_recommended');
		$data['text_3ds_failed_authentication'] = $this->language->get('text_3ds_failed_authentication');
		$data['text_3ds_rejected_authentication'] = $this->language->get('text_3ds_rejected_authentication');
		$data['text_3ds_attempted_authentication'] = $this->language->get('text_3ds_attempted_authentication');
		$data['text_3ds_unable_authentication'] = $this->language->get('text_3ds_unable_authentication');
		$data['text_3ds_challenge_authentication'] = $this->language->get('text_3ds_challenge_authentication');
		$data['text_3ds_card_ineligible'] = $this->language->get('text_3ds_card_ineligible');
		$data['text_3ds_system_unavailable'] = $this->language->get('text_3ds_system_unavailable');
		$data['text_3ds_system_bypassed'] = $this->language->get('text_3ds_system_bypassed');
		$data['text_confirm'] = $this->language->get('text_confirm');
		
		$data['entry_connect'] = $this->language->get('entry_connect');
		$data['entry_checkout_express_status'] = $this->language->get('entry_checkout_express_status');
		$data['entry_checkout_card_status'] = $this->language->get('entry_checkout_card_status');
		$data['entry_checkout_message_status'] = $this->language->get('entry_checkout_message_status');
		$data['entry_environment'] = $this->language->get('entry_environment');
		$data['entry_debug'] = $this->language->get('entry_debug');
		$data['entry_transaction_method'] = $this->language->get('entry_transaction_method');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_currency_code'] = $this->language->get('entry_currency_code');
		$data['entry_currency_value'] = $this->language->get('entry_currency_value');
		$data['entry_card_currency_code'] = $this->language->get('entry_card_currency_code');
		$data['entry_card_currency_value'] = $this->language->get('entry_card_currency_value');
		$data['entry_smart_button'] = $this->language->get('entry_smart_button');
		$data['entry_button_align'] = $this->language->get('entry_button_align');
		$data['entry_button_size'] = $this->language->get('entry_button_size');
		$data['entry_button_color'] = $this->language->get('entry_button_color');
		$data['entry_button_shape'] = $this->language->get('entry_button_shape');
		$data['entry_button_label'] = $this->language->get('entry_button_label');
		$data['entry_form_align'] = $this->language->get('entry_form_align');
		$data['entry_form_size'] = $this->language->get('entry_form_size');
		$data['entry_secure_status'] = $this->language->get('entry_secure_status');
		$data['entry_secure_scenario'] = $this->language->get('entry_secure_scenario');
		$data['entry_message_align'] = $this->language->get('entry_message_align');
		$data['entry_message_size'] = $this->language->get('entry_message_size');
		$data['entry_message_layout'] = $this->language->get('entry_message_layout');
		$data['entry_message_text_color'] = $this->language->get('entry_message_text_color');
		$data['entry_message_text_size'] = $this->language->get('entry_message_text_size');
		$data['entry_message_flex_color'] = $this->language->get('entry_message_flex_color');
		$data['entry_message_flex_ratio'] = $this->language->get('entry_message_flex_ratio');
		
		$data['help_checkout_express'] = $this->language->get('help_checkout_express');
		$data['help_checkout_express_status'] = $this->language->get('help_checkout_express_status');
		$data['help_checkout_card_status'] = $this->language->get('help_checkout_card_status');
		$data['help_checkout_message_status'] = $this->language->get('help_checkout_message_status');
		$data['help_total'] = $this->language->get('help_total');
		$data['help_currency_code'] = $this->language->get('help_currency_code');
		$data['help_currency_value'] = $this->language->get('help_currency_value');
		$data['help_card_currency_code'] = $this->language->get('help_card_currency_code');
		$data['help_card_currency_value'] = $this->language->get('help_card_currency_value');
		$data['help_secure_status'] = $this->language->get('help_secure_status');
		$data['help_secure_scenario'] = $this->language->get('help_secure_scenario');
				
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_connect'] = $this->language->get('button_connect');
		$data['button_disconnect'] = $this->language->get('button_disconnect');
		$data['button_smart_button'] = $this->language->get('button_smart_button');
		
		$data['breadcrumbs'] = array();

		if (VERSION >= '2.0.2.0') {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
			);
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payments'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/paypal', 'token=' . $this->session->data['token'], 'SSL')
		);
						
		$data['action'] = $this->url->link('payment/paypal', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		$data['partner_url'] = str_replace('&amp;', '%26', $this->url->link('payment/paypal', 'token=' . $this->session->data['token'], 'SSL'));
		$data['callback_url'] = str_replace('&amp;', '&', $this->url->link('payment/paypal/callback', 'token=' . $this->session->data['token'], 'SSL'));
		$data['disconnect_url'] =  str_replace('&amp;', '&', $this->url->link('payment/paypal/disconnect', 'token=' . $this->session->data['token'], 'SSL'));
		$data['configure_smart_button_url'] = $this->url->link('payment/paypal/configureSmartButton', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['server'] = HTTPS_SERVER;
			$data['catalog'] = HTTPS_CATALOG;
		} else {
			$data['server'] = HTTP_SERVER;
			$data['catalog'] = HTTP_CATALOG;
		}
		
		// Setting 		
		$_config = new Config();
		$_config->load('paypal');
		
		$data['setting'] = $_config->get('paypal_setting');
					
		if (isset($this->session->data['environment']) && isset($this->session->data['authorization_code']) && isset($this->session->data['shared_id']) && isset($this->session->data['seller_nonce']) && isset($this->request->get['merchantIdInPayPal'])) {						
			$environment = $this->session->data['environment'];
			
			require_once DIR_SYSTEM . 'library/paypal/paypal.php';
			
			$paypal_info = array(
				'client_id' => $this->session->data['shared_id'],
				'environment' => $environment,
				'partner_attribution_id' => $data['setting']['partner'][$environment]['partner_attribution_id']
			);
				
			$paypal = new PayPal($paypal_info);
			
			$token_info = array(
				'grant_type' => 'authorization_code',
				'code' => $this->session->data['authorization_code'],
				'code_verifier' => $this->session->data['seller_nonce']
			);
			
			$paypal->setAccessToken($token_info);
											
			$result = $paypal->getSellerCredentials($data['setting']['partner'][$environment]['partner_id']);
			
			$client_id = '';
			$secret = '';
			
			if (isset($result['client_id']) && isset($result['client_secret'])) {
				$client_id = $result['client_id'];
				$secret = $result['client_secret'];
			}
			
			$paypal_info = array(
				'partner_id' => $data['setting']['partner'][$environment]['partner_id'],
				'client_id' => $client_id,
				'secret' => $secret,
				'environment' => $environment,
				'partner_attribution_id' => $data['setting']['partner'][$environment]['partner_attribution_id']
			);
		
			$paypal = new PayPal($paypal_info);
			
			$token_info = array(
				'grant_type' => 'client_credentials'
			);	
		
			$paypal->setAccessToken($token_info);
						
			$webhook_info = array(
				'url' => $data['catalog'] . 'index.php?route=payment/paypal/webhook',
				'event_types' => array(
					array('name' => 'PAYMENT.AUTHORIZATION.CREATED'),
					array('name' => 'PAYMENT.AUTHORIZATION.VOIDED'),
					array('name' => 'PAYMENT.CAPTURE.COMPLETED'),
					array('name' => 'PAYMENT.CAPTURE.DENIED'),
					array('name' => 'PAYMENT.CAPTURE.PENDING'),
					array('name' => 'PAYMENT.CAPTURE.REFUNDED'),
					array('name' => 'PAYMENT.CAPTURE.REVERSED'),
					array('name' => 'CHECKOUT.ORDER.COMPLETED')
				)
			);
			
			$result = $paypal->createWebhook($webhook_info);
			
			$webhook_id = '';
		
			if (isset($result['id'])) {
				$webhook_id = $result['id'];
			}
		
			if ($paypal->hasErrors()) {
				$error_messages = array();
				
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
					
					$this->model_payment_paypal->log($error, $error['message']);
				}
				
				$this->error['warning'] = implode(' ', $error_messages);
			}
   			
			$merchant_id = $this->request->get['merchantIdInPayPal'];
			
			$setting = $this->model_setting_setting->getSetting('paypal');
						
			$setting['paypal_environment'] = $environment;
			$setting['paypal_client_id'] = $client_id;
			$setting['paypal_secret'] = $secret;
			$setting['paypal_merchant_id'] = $merchant_id;
			$setting['paypal_webhook_id'] = $webhook_id;

			$this->model_setting_setting->editSetting('paypal', $setting);
						
			unset($this->session->data['authorization_code']);
			unset($this->session->data['shared_id']);
			unset($this->session->data['seller_nonce']);
		}
		
		if (isset($environment)) {
			$data['environment'] = $environment;
		} elseif (isset($this->request->post['paypal_environment'])) {
			$data['environment'] = $this->request->post['paypal_environment'];
		} elseif ($this->config->get('paypal_environment')) {
			$data['environment'] = $this->config->get('paypal_environment');
		} else {
			$data['environment'] = 'production';
		}
				
		$data['seller_nonce'] = $this->token(50);
		
		$data['configure_url'] = array(
			'production' => array(
				'ppcp' => 'https://www.paypal.com/bizsignup/partner/entry?partnerId=' . $data['setting']['partner']['production']['partner_id'] . '&partnerClientId=' . $data['setting']['partner']['production']['client_id'] . '&features=PAYMENT,REFUND&product=ppcp&integrationType=FO&returnToPartnerUrl=' . $data['partner_url'] . '&displayMode=minibrowser&sellerNonce=' . $data['seller_nonce'],
				'express_checkout' => 'https://www.paypal.com/bizsignup/partner/entry?partnerId=' . $data['setting']['partner']['production']['partner_id'] . '&partnerClientId=' . $data['setting']['partner']['production']['client_id'] . '&features=PAYMENT,REFUND&product=EXPRESS_CHECKOUT&integrationType=FO&returnToPartnerUrl=' . $data['partner_url'] . '&displayMode=minibrowser&sellerNonce=' . $data['seller_nonce']
			),
			'sandbox' => array(
				'ppcp' => 'https://www.sandbox.paypal.com/bizsignup/partner/entry?partnerId=' . $data['setting']['partner']['sandbox']['partner_id'] . '&partnerClientId=' . $data['setting']['partner']['sandbox']['client_id'] . '&features=PAYMENT,REFUND&product=ppcp&integrationType=FO&returnToPartnerUrl=' . $data['partner_url'] . '&displayMode=minibrowser&sellerNonce=' . $data['seller_nonce'],
				'express_checkout' => 'https://www.sandbox.paypal.com/bizsignup/partner/entry?partnerId=' . $data['setting']['partner']['sandbox']['partner_id'] . '&partnerClientId=' . $data['setting']['partner']['sandbox']['client_id'] . '&features=PAYMENT,REFUND&product=EXPRESS_CHECKOUT&integrationType=FO&returnToPartnerUrl=' . $data['partner_url'] . '&displayMode=minibrowser&sellerNonce=' . $data['seller_nonce']
			)
		);
		
		$data['help_checkout_express'] = sprintf($this->language->get('help_checkout_express'), $data['configure_url'][$data['environment']]['express_checkout']);
		
		if (isset($client_id)) {
			$data['client_id'] = $client_id;
		} elseif (isset($this->request->post['paypal_client_id'])) {
			$data['client_id'] = $this->request->post['paypal_client_id'];
		} else {
			$data['client_id'] = $this->config->get('paypal_client_id');
		}

		if (isset($secret)) {
			$data['secret'] = $secret;
		} elseif (isset($this->request->post['paypal_secret'])) {
			$data['secret'] = $this->request->post['paypal_secret'];
		} else {
			$data['secret'] = $this->config->get('paypal_secret');
		}
		
		if (isset($merchant_id)) {
			$data['merchant_id'] = $merchant_id;
		} elseif (isset($this->request->post['paypal_merchant_id'])) {
			$data['merchant_id'] = $this->request->post['paypal_merchant_id'];
		} else {
			$data['merchant_id'] = $this->config->get('paypal_merchant_id');
		}
		
		$data['text_connect'] = sprintf($this->language->get('text_connect'), $data['client_id'], $data['secret'], $data['merchant_id']);
		
		if (isset($webhook_id)) {
			$data['webhook_id'] = $webhook_id;
		} elseif (isset($this->request->post['paypal_webhook_id'])) {
			$data['webhook_id'] = $this->request->post['paypal_webhook_id'];
		} else {
			$data['webhook_id'] = $this->config->get('paypal_webhook_id');
		}

		if (isset($this->request->post['paypal_debug'])) {
			$data['debug'] = $this->request->post['paypal_debug'];
		} else {
			$data['debug'] = $this->config->get('paypal_debug');
		}
								
		if (isset($this->request->post['paypal_transaction_method'])) {
			$data['transaction_method'] = $this->request->post['paypal_transaction_method'];
		} else {
			$data['transaction_method'] = $this->config->get('paypal_transaction_method');
		}

		if (isset($this->request->post['paypal_total'])) {
			$data['total'] = $this->request->post['paypal_total'];
		} else {
			$data['total'] = $this->config->get('paypal_total');
		}
		
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['paypal_geo_zone_id'])) {
			$data['geo_zone_id'] = $this->request->post['paypal_geo_zone_id'];
		} else {
			$data['geo_zone_id'] = $this->config->get('paypal_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['paypal_status'])) {
			$data['status'] = $this->request->post['paypal_status'];
		} else {
			$data['status'] = $this->config->get('paypal_status');
		}

		if (isset($this->request->post['paypal_sort_order'])) {
			$data['sort_order'] = $this->request->post['paypal_sort_order'];
		} else {
			$data['sort_order'] = $this->config->get('paypal_sort_order');
		}
		
		if (isset($this->request->post['paypal_currency_code'])) {
			$data['currency_code'] = $this->request->post['paypal_currency_code'];
		} elseif ($this->config->get('paypal_currency_value')) {
			$data['currency_code'] = $this->config->get('paypal_currency_code');
		} else {
			$data['currency_code'] = 'USD';
		}
		
		if (isset($this->request->post['paypal_currency_value'])) {
			$data['currency_value'] = $this->request->post['paypal_currency_value'];
		} elseif ($this->config->get('paypal_currency_value')) {
			$data['currency_value'] = $this->config->get('paypal_currency_value');
		} else {
			$data['currency_value'] = '1';
		}
		
		if (isset($this->request->post['paypal_card_currency_code'])) {
			$data['card_currency_code'] = $this->request->post['paypal_card_currency_code'];
		} elseif ($this->config->get('paypal_card_currency_value')) {
			$data['card_currency_code'] = $this->config->get('paypal_card_currency_code');
		} else {
			$data['card_currency_code'] = 'USD';
		}
		
		if (isset($this->request->post['paypal_card_currency_value'])) {
			$data['card_currency_value'] = $this->request->post['paypal_card_currency_value'];
		} elseif ($this->config->get('paypal_card_currency_value')) {
			$data['card_currency_value'] = $this->config->get('paypal_card_currency_value');
		} else {
			$data['card_currency_value'] = '1';
		}
						
		if (isset($this->request->post['paypal_setting'])) {
			$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->request->post['paypal_setting']);
		} else {
			$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('paypal_setting'));
		}
		
		if ($data['client_id'] && $data['secret']) {										
			require_once DIR_SYSTEM . 'library/paypal/paypal.php';
			
			$paypal_info = array(
				'client_id' => $data['client_id'],
				'secret' => $data['secret'],
				'environment' => $data['environment'],
				'partner_attribution_id' => $data['setting']['partner'][$data['environment']]['partner_attribution_id']
			);
		
			$paypal = new PayPal($paypal_info);
			
			$token_info = array(
				'grant_type' => 'client_credentials'
			);	
				
			$paypal->setAccessToken($token_info);
		
			$data['client_token'] = $paypal->getClientToken();
									
			if ($paypal->hasErrors()) {
				$error_messages = array();
				
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
					
					$this->model_payment_paypal->log($error, $error['message']);
				}
				
				$this->error['warning'] = implode(' ', $error_messages);
			}
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
					
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if (VERSION >= '2.2.0.0') {
			$this->response->setOutput($this->load->view('payment/paypal', $data));
		} else {
			$this->response->setOutput($this->load->view('payment/paypal.tpl', $data));
		}
	}
	
	public function disconnect() {
		$this->load->model('setting/setting');
		
		$setting = $this->model_setting_setting->getSetting('paypal');
						
		$setting['paypal_client_id'] = '';
		$setting['paypal_secret'] = '';
		$setting['paypal_merchant_id'] = '';
		$setting['paypal_webhook_id'] = '';
		
		$this->model_setting_setting->editSetting('paypal', $setting);
		
		$data['error'] = $this->error;
		
		$this->response->setOutput(json_encode($data));
	}
		
	public function callback() {
		if (isset($this->request->post['environment']) && isset($this->request->post['authorization_code']) && isset($this->request->post['shared_id']) && isset($this->request->post['seller_nonce'])) {
			$this->session->data['environment'] = $this->request->post['environment'];
			$this->session->data['authorization_code'] = $this->request->post['authorization_code'];
			$this->session->data['shared_id'] = $this->request->post['shared_id'];
			$this->session->data['seller_nonce'] = $this->request->post['seller_nonce'];
		}
		
		$data['error'] = $this->error;
				
		$this->response->setOutput(json_encode($data));
    }
	
	public function configureSmartButton() {
		$this->load->model('payment/paypal');
		
		$this->model_payment_paypal->configureSmartButton();
		
		$this->response->redirect($this->url->link('module/paypal_smart_button', 'token=' . $this->session->data['token'], 'SSL'));
	}
				
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/paypal')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		// Setting 		
		$_config = new Config();
		$_config->load('paypal');
		
		$setting = $_config->get('paypal_setting');
				
		require_once DIR_SYSTEM . 'library/paypal/paypal.php';
		
		$paypal_info = array(
			'client_id' => $this->request->post['paypal_client_id'],
			'secret' => $this->request->post['paypal_secret'],
			'environment' => $this->request->post['paypal_environment'],
			'partner_attribution_id' => $setting['partner'][$this->request->post['paypal_environment']]['partner_attribution_id']
		);
		
		$paypal = new PayPal($paypal_info);
		
		$token_info = array(
			'grant_type' => 'client_credentials'
		);	
							
		$paypal->setAccessToken($token_info);
				
		if ($paypal->hasErrors()) {
			$error_messages = array();
				
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
					
				$this->model_payment_paypal->log($error, $error['message']);
			}
				
			$this->error['warning'] = implode(' ', $error_messages);
		}
		
		return !$this->error;
	}
	
	private function token($length = 32) {
		// Create random token
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	
		$max = strlen($string) - 1;
	
		$token = '';
	
		for ($i = 0; $i < $length; $i++) {
			$token .= $string[mt_rand(0, $max)];
		}	
	
		return $token;
	}
}