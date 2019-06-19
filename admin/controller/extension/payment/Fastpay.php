<?php
/**
 * SSLCommerz
 * @version 4.0
 * @author Leton Miah <letoncse7@gmail.com>
 * @copyright 2019 https://www.fast-pay.cash/
 * Opencat Payment Module V.3.x
 */

class ControllerExtensionPaymentFastpay extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/Fastpay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_Fastpay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'));
		}


 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
		}
		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => false
   		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'),
			'separator' => ' :: '
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/Fastpay', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['action'] = $this->url->link('extension/payment/Fastpay', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL');
		

		if (isset($this->request->post['payment_Fastpay_merchant'])) {
			$data['payment_Fastpay_merchant'] = $this->request->post['payment_Fastpay_merchant'];
		} else {
			$data['payment_Fastpay_merchant'] = $this->config->get('payment_Fastpay_merchant');
		}

		if (isset($this->request->post['payment_Fastpay_password'])) {
			$data['payment_Fastpay_password'] = $this->request->post['payment_Fastpay_password'];
		} else {
			$data['payment_Fastpay_password'] = $this->config->get('payment_Fastpay_password');
		}

		if (isset($this->request->post['payment_Fastpay_test'])) {
			$data['payment_Fastpay_test'] = $this->request->post['payment_Fastpay_test'];
		} else {
			$data['payment_Fastpay_test'] = $this->config->get('payment_Fastpay_test');
		}

		if (isset($this->request->post['payment_Fastpay_total'])) {
			$data['payment_Fastpay_total'] = $this->request->post['payment_Fastpay_total'];
		} else {
			$data['payment_Fastpay_total'] = $this->config->get('payment_Fastpay_total');
		}

		if (isset($this->request->post['payment_Fastpay_order_status_id'])) {
			$data['payment_Fastpay_order_status_id'] = $this->request->post['payment_Fastpay_order_status_id'];
		} else {
			$data['payment_Fastpay_order_status_id'] = $this->config->get('payment_Fastpay_order_status_id');
		}
        if (isset($this->request->post['payment_Fastpay_order_fail_id'])) {
			$data['payment_Fastpay_order_fail_id'] = $this->request->post['payment_Fastpay_order_fail_id'];
		} else {
			$data['payment_Fastpay_order_fail_id'] = $this->config->get('payment_Fastpay_order_fail_id');
		}
		
		if (isset($this->request->post['payment_Fastpay_order_risk_id'])) {
			$data['payment_Fastpay_order_risk_id'] = $this->request->post['payment_Fastpay_order_risk_id'];
		} else {
			$data['payment_Fastpay_order_risk_id'] = $this->config->get('payment_Fastpay_order_risk_id');
		}
                
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_Fastpay_geo_zone_id'])) {
			$data['payment_Fastpay_geo_zone_id'] = $this->request->post['payment_Fastpay_geo_zone_id'];
		} else {
			$data['payment_Fastpay_geo_zone_id'] = $this->config->get('payment_Fastpay_geo_zone_id');
		}

		
		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_Fastpay_status'])) {
			$data['payment_Fastpay_status'] = $this->request->post['payment_Fastpay_status'];
		} else {
			$data['payment_Fastpay_status'] = $this->config->get('payment_Fastpay_status');
		}

		if (isset($this->request->post['payment_Fastpay_sort_order'])) {
			$data['payment_Fastpay_sort_order'] = $this->request->post['payment_Fastpay_sort_order'];
		} else {
			$data['payment_Fastpay_sort_order'] = $this->config->get('payment_Fastpay_sort_order');
		}
		
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
  
             /* admin/view/template/extension/payment/Fastpay.twig */
  
		$this->response->setOutput($this->load->view('extension/payment/Fastpay', $data));
		
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/Fastpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_Fastpay_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}
		if (!$this->request->post['payment_Fastpay_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>