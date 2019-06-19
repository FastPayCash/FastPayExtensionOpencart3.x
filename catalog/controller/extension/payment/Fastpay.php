<?php
class ControllerExtensionPaymentFastpay extends Controller {
	
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['store_id'] = $this->config->get('payment_Fastpay_merchant');
		$data['tran_id'] = $this->session->data['order_id'];
		$data['total_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		
		$data['Fastpay_password'] = $this->config->get('payment_Fastpay_password');


			$post_data = array();
			$post_data['merchant_mobile_no'] = $this->config->get('payment_Fastpay_merchant');
			$post_data['store_password'] = $this->config->get('payment_Fastpay_password');
			$post_data['order_id'] = $this->session->data['order_id'];
			$post_data['bill_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			$post_data['success_url'] = $this->url->link('extension/payment/Fastpay/callback', '', 'SSL');
			$post_data['fail_url'] = $this->url->link('checkout/failure', '', 'SSL');
			$post_data['cancel_url'] = $this->url->link('checkout/cart', '', 'SSL');

		
		if($this->config->get('payment_Fastpay_test')=='live') {
			    $direct_api_url = 'https://secure.fast-pay.cash/merchant/generate-payment-token';
				
				$data['process_url'] = 'https://secure.fast-pay.cash/merchant/payment';
			}
		else {
				$direct_api_url = 'https://dev.fast-pay.cash/merchant/generate-payment-token';
				
				$data['process_url'] = 'https://dev.fast-pay.cash/merchant/payment';
			}
			
			$handle = curl_init();
			curl_setopt($handle, CURLOPT_URL, $direct_api_url );
			curl_setopt($handle, CURLOPT_TIMEOUT, 10);
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($handle, CURLOPT_POST, 1 );
			curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);


			$content = curl_exec($handle );

			$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

			if($code == 200 && !( curl_errno($handle))) {
				curl_close( $handle);
				$response = $content;
			} else {
				curl_close( $handle);
				echo "FAILED TO CONNECT WITH FastPay  API";
				exit;
			}

			# PARSE THE JSON RESPONSE
			$decodedResponse = json_decode($response, true );
			
			$data['token'] = $decodedResponse['token'];
            

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/Fastpay')) {
			return $this->load->view($this->config->get('config_template') . '/template/extension/payment/Fastpay', $data);
		} else {
			return $this->load->view('extension/payment/Fastpay', $data);
		}
	}
	
	
	public function callback() {
                
				$Fastpay_test = $this->config->get('payment_Fastpay_test');
                $store_id = $this->config->get('payment_Fastpay_merchant');
                $store_passwd = $this->config->get('payment_Fastpay_password');
               if (isset($_POST['order_id'])) {
					$order_id = $_POST['order_id'];
							       											 
				} else {
					$order_id = 0;
				}
                if (isset($_POST['bill_amount'])) {
                    $total=$_POST['bill_amount'];
					
				}else
                	{
                    $total='';	
                   
                }
				
                
					
					
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
                                       

         if($this->config->get('payment_Fastpay_test')=='live') {
                
				$requested_url = ("https://secure.fast-pay.cash/merchant/payment/validation");
                
				} else{
					
                 $requested_url = ("https://dev.fast-pay.cash/merchant/payment/validation");  
				 
                }  
				
                $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

				$post_data = array();
				$post_data['merchant_mobile_no']= $store_id;
				$post_data['store_password']= $store_passwd;
				$post_data['order_id']= $order_id;

				$handle = curl_init();
				curl_setopt($handle, CURLOPT_URL, $requested_url );
				curl_setopt($handle, CURLOPT_TIMEOUT, 10);
				curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($handle, CURLOPT_POST, 1 );
				curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

				$result = curl_exec($handle);

				$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

				if($code == 200 && !( curl_errno($handle)))
				{
					$result = json_decode($result);
					$messages = $result->messages;
					$code = $result->code; #if $code is not 200 then something is wrong with your request.
					$data_fastpay = $result->data;
					

				} else {

					echo "Failed to connect with FastPay";
				}


         $data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_basket'),
				'href' => $this->url->link('checkout/cart')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_checkout'),
				'href' => $this->url->link('checkout/checkout', '', 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_failed'),
				'href' => $this->url->link('checkout/success')
			);

			$data['heading_title'] = $this->language->get('text_failed');

			
			$data['button_continue'] = $this->language->get('button_continue');
						
		if ($order_info && $data_fastpay->status) {
			$this->language->load('extension/payment/Fastpay');
	
			$data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
	
			if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
				$data['base'] = HTTP_SERVER;
			} else {
				$data['base'] = HTTPS_SERVER;
			}
	
			$data['language'] = $this->language->get('code');
			$data['direction'] = $this->language->get('direction');
	
			$data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
	
			$data['text_response'] = $this->language->get('text_response');
			$data['text_success'] = $this->language->get('text_success');
			$data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
			$data['text_failure'] = $this->language->get('text_failure');
			$data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));
	
	          $this->load->model('checkout/order');
			  $this->model_checkout_order->addOrderHistory($data_fastpay->order_id, $this->config->get('config_order_status_id'));
			  
	
			if (isset($data_fastpay->status) && strtoupper($data_fastpay->status) == 'SUCCESS') {
				
				
				
				   $message = '';
	
				
					$message .= 'Payment Status = ' . $data_fastpay->status . "\n";
				    
					$message .= 'Fastpay txnid = ' . $data_fastpay->transaction_id . "\n";
				   
					$message .= 'Your Oder id = ' . $data_fastpay->order_id . "\n";
					
					$message .= 'Payment Date = ' . $data_fastpay->received_at . "\n";  
				   
					$message .= 'Customer Account No = ' .$data_fastpay->customer_account_no . "\n"; 
				   
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_Fastpay_order_status_id'), $message, false);
	               $error='';
            
				$data['text_message'] = sprintf('Your payment was successfully received', $error, $this->url->link('information/contact'));
				$data['continue'] = $this->url->link('checkout/success');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');
             
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/success')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/extension/payment/success', $data));
				} else {
					$this->response->setOutput($this->load->view('extension/payment/success', $data));
				}

			}
			 else {

			$data['continue'] = $this->url->link('checkout/cart');
            $data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/Commerce_failure')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/extension/payment/Commerce_failure', $data));
			} else {
				$this->response->setOutput($this->load->view('extension/payment/Commerce_failure', $data));
			}
	

			}
		}
	}
	

}
