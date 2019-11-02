<?php
/**
 * Order+.
 *
 * Order+ is an extension for CMS Opencart 3.x Admin Panel.
 * It extends displayed information in order list and e-mails, also adds product images in invoices, shopping lists and e-mails.
 *
 * @author		Andrii Burkatskyi aka underr underr.ua@gmail.com
 * @copyright	Copyright (c) 2019 Andrii Burkatskyi
 * @license		https://raw.githubusercontent.com/underr-ua/ocmod3-order-plus/master/EULA.txt End-User License Agreement
 *
 * @version		1.2
 *
 * @see			https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=37121
 * @see			https://underr.space/notes/projects/project-017.html
 * @see			https://github.com/underr-ua/ocmod3-order-plus
 */
class ControllerExtensionModuleOrderPlus extends Controller {
	private $error = [];

	public function index() {
		$this->load->language('extension/module/order_plus');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (('POST' == $this->request->server['REQUEST_METHOD']) && $this->validate()) {
			$this->model_setting_setting->editSetting('module_order_plus', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect(
				$this->url->link('marketplace/extension',
					'user_token=' . $this->session->data['user_token'] . '&type=module',
					true
				)
			);
		}

		if (isset($this->error['permission'])) {
			$data['error_permission'] = $this->error['permission'];
		} else {
			$data['error_permission'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link(
				'common/dashboard',
				'user_token=' . $this->session->data['user_token'],
				true
			),
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link(
				'marketplace/extension',
				'user_token=' . $this->session->data['user_token'] . '&type=module',
				true
			),
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link(
				'extension/module/order_plus',
				'user_token=' . $this->session->data['user_token'],
				true
			),
		];

		$data['action'] = $this->url->link(
			'extension/module/order_plus',
			'user_token=' . $this->session->data['user_token'],
			true
		);

		$data['cancel'] = $this->url->link(
			'marketplace/extension',
			'user_token=' . $this->session->data['user_token'] . '&type=module',
			true
		);

		if (isset($this->request->post['module_order_plus_status'])) {
			$data['status'] = $this->request->post['module_order_plus_status'];
		} else {
			$data['status'] = $this->config->get('module_order_plus_status');
		}

		if (isset($this->request->post['module_order_plus_image_width'])) {
			$data['image_width'] = abs((int)$this->request->post['module_order_plus_image_width']);
		} else if ($this->config->get('module_order_plus_image_width')) {
			$data['image_width'] = $this->config->get('module_order_plus_image_width');
		} else {
			$data['image_width'] =
				$this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width');
		}

		if (isset($this->request->post['module_order_plus_image_height'])) {
			$data['image_height'] = abs((int)$this->request->post['module_order_plus_image_height']);
		} elseif ($this->config->get('module_order_plus_image_height')) {
			$data['image_height'] = $this->config->get('module_order_plus_image_height');
		} else {
			$data['image_height'] =
				$this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height');
		}

		if (isset($this->request->post['module_order_plus_customer_info'])) {
			$data['customer_info'] = $this->request->post['module_order_plus_customer_info'];
		} else {
			$data['customer_info'] = $this->config->get('module_order_plus_customer_info');
		}

		if (isset($this->request->post['module_order_plus_order_image'])) {
			$data['order_image'] = $this->request->post['module_order_plus_order_image'];
		} else {
			$data['order_image'] = $this->config->get('module_order_plus_order_image');
		}

		if (isset($this->request->post['module_order_plus_invoice_image'])) {
			$data['invoice_image'] = $this->request->post['module_order_plus_invoice_image'];
		} else {
			$data['invoice_image'] = $this->config->get('module_order_plus_invoice_image');
		}

		if (isset($this->request->post['module_order_plus_shipping_image'])) {
			$data['shipping_image'] = $this->request->post['module_order_plus_shipping_image'];
		} else {
			$data['shipping_image'] = $this->config->get('module_order_plus_shipping_image');
		}

		if (isset($this->request->post['module_order_plus_account_order_image'])) {
			$data['account_order_image'] = $this->request->post['module_order_plus_account_order_image'];
		} else {
			$data['account_order_image'] = $this->config->get('module_order_plus_account_order_image');
		}

		if (isset($this->request->post['module_order_plus_account_order_link'])) {
			$data['account_order_link'] = $this->request->post['module_order_plus_account_order_link'];
		} else {
			$data['account_order_link'] = $this->config->get('module_order_plus_account_order_link');
		}

		if (isset($this->request->post['module_order_plus_admin_email'])) {
			$data['admin_email'] = $this->request->post['module_order_plus_admin_email'];
		} else {
			$data['admin_email'] = $this->config->get('module_order_plus_admin_email');
		}

		if (isset($this->request->post['module_order_plus_email_images'])) {
			$data['email_images'] = $this->request->post['module_order_plus_email_images'];
		} else {
			$data['email_images'] = $this->config->get('module_order_plus_email_images');
		}

		$data['text_payment'] = $this->language->get('tab_payment');
		$data['text_shipping'] = $this->language->get('tab_shipping');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/order_plus', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/order_plus')) {
			$this->error['permission'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
