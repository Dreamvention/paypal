<?php
namespace Opencart\Admin\Controller\Extension\PayPal\Module;
class PayPalSmartButton extends \Opencart\System\Engine\Controller {
	private $error = [];
	
	public function index(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');

		$this->load->model('extension/paypal/module/paypal_smart_button');
		
		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/opencart/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/paypal/module/paypal_smart_button', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/paypal/module/paypal_smart_button|save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
						
		if (isset($this->request->post['module_paypal_smart_button_status'])) {
			$data['status'] = $this->request->post['module_paypal_smart_button_status'];
		} else {
			$data['status'] = $this->config->get('module_paypal_smart_button_status');
		}
		
		// Setting 		
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'paypal/system/config/');
		$_config->load('paypal_smart_button');
		
		$data['setting'] = $_config->get('paypal_smart_button_setting');
		
		if (isset($this->request->post['module_paypal_smart_button_setting'])) {
			$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->request->post['module_paypal_smart_button_setting']);
		} else {
			$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('module_paypal_smart_button_setting'));
		}
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/paypal/module/paypal_smart_button', $data));
	}
	
	public function save(): void {
		$this->load->language('extension/paypal/module/paypal_smart_button');
		
		$this->load->model('extension/paypal/module/paypal_smart_button');
				
		if (!$this->user->hasPermission('modify', 'extension/paypal/module/paypal_smart_button')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_paypal_smart_button', $this->request->post);
			
			$data['success'] = $this->language->get('success_save');
		}
		
		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}

	public function install(): void {
		$this->load->model('extension/paypal/module/paypal_smart_button');
		$this->load->model('setting/setting');

		$this->model_extension_paypal_module_paypal_smart_button->install();
		
		$setting['module_paypal_smart_button_status'] = 0;
		
		$this->model_setting_setting->editSetting('module_paypal_smart_button', $setting);
	}
}
