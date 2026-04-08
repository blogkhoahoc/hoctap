<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Offline_payment_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	//Offline checkout User panel
	public function attach_payment_document($file_extension = "")
	{
	    // --- BẮT ĐẦU LỚP BẢO MẬT 2: KIỂM TRA CHỐT HẠ & TRỪ SỐ LƯỢNG MÃ ---
        $applied_coupon = $this->session->userdata('applied_coupon');
        if (!empty($applied_coupon)) {
            // Lục lại database xem mã còn sống không
            $is_valid = $this->crud_model->check_coupon_validity($applied_coupon);
            
            if (!$is_valid) {
                // Nếu có người nhanh tay dùng mất mã -> Hủy session, báo lỗi và đá văng ra giỏ hàng
                $this->session->unset_userdata('applied_coupon');
                $this->session->set_userdata('total_price_of_checking_out', '');
                $this->session->set_flashdata('error_message', 'Thanh toán thất bại! Mã giảm giá vừa có người sử dụng hết.');
                redirect('home/shopping_cart', 'refresh');
                exit; // ⚠️ Lệnh exit này sẽ chặt đứt luồng chạy, chặn đứng việc lưu data và upload ảnh ở bên dưới
            } else {
                // Nếu mã vẫn hợp lệ -> Trừ ngay đi 1 lượt sử dụng
                $this->db->where('code', $applied_coupon);
                $this->db->where('quantity >', 0);
                $this->db->set('quantity', 'quantity - 1', FALSE);
                $this->db->update('coupons');
            }
        }
        // --- KẾT THÚC LỚP BẢO MẬT 2 ---
		$total_amount = $this->session->userdata('total_price_of_checking_out');
		$user_id = $this->session->userdata('user_id');
		$curse_id = json_encode($this->session->userdata('cart_items'));

		$data['user_id'] = $user_id;
		$data['amount'] = $total_amount;
		$data['course_id'] = $curse_id;
		$data['document_image'] = rand(6000, 10000000) . '.' . $file_extension;
		$data['timestamp'] = strtotime(date('H:i'));
		$data['status'] = 0;

		$this->db->insert('offline_payment', $data);
		
		// --- BẮT ĐẦU: GỌI HÀM GỬI EMAIL CHO ADMIN ---
		$this->load->model('email_model');
		$this->email_model->send_offline_payment_notification_to_admin();
		// --- KẾT THÚC ---
		
		move_uploaded_file($_FILES['payment_document']['tmp_name'], 'uploads/payment_document/' . $data['document_image']);

		$this->session->set_userdata('cart_items', array());
		$this->session->unset_userdata('applied_coupon');
		$this->session->set_userdata('total_price_of_checking_out', '');
	}

	//User panel
	public function pending_offline_payment($user_id = "")
	{
		if ($user_id > 0) {
			$this->db->where('user_id', $user_id);
		}
		$this->db->where('status', 0);
		return $this->db->get('offline_payment');
	}

	//Admin panel
	public function offline_payment_all_data($offline_payment_id = "")
	{
		if ($offline_payment_id > 0) {
			$this->db->where('id', $offline_payment_id);
		}
		return $this->db->get('offline_payment');
	}
	public function offline_payment_pending($offline_payment_id = "")
	{
		if ($offline_payment_id > 0) {
			$this->db->where('id', $offline_payment_id);
		}
		$this->db->order_by('id', 'ASC');
		$this->db->where('status', 0);
		return $this->db->get('offline_payment');
	}
	public function offline_payment_approve($offline_payment_id = "")
	{
		if ($offline_payment_id > 0) {
			$this->db->where('id', $offline_payment_id);
		}
		$this->db->order_by('id', 'ASC');
		$this->db->where('status', 1);
		return $this->db->get('offline_payment');
	}
	public function offline_payment_suspended($offline_payment_id = "")
	{
		if ($offline_payment_id > 0) {
			$this->db->where('id', $offline_payment_id);
		}
		$this->db->order_by('id', 'ASC');
		$this->db->where('status', 2);
		return $this->db->get('offline_payment');
	}


	public function approve_offline_payment($param1 = "")
	{
		$this->db->where('id', $param1);
		$this->db->update('offline_payment', array('status' => 1));
	}
	public function suspended_offline_payment($param1 = "")
	{
		$this->db->where('id', $param1);
		$this->db->update('offline_payment', array('status' => 2));
	}
	public function delete_offline_payment($param1 = "")
	{
		$this->db->where('id', $param1);
		$this->db->delete('offline_payment');
	}



	// CHECK WHETHER A COURSE IS IN OFFLINE PAYMENT TABLE AS PENDING STATUS
	public function get_course_status($user_id = "", $course_id = "")
	{
		$offline_payment_courses = $this->db->get_where('offline_payment', array('user_id' => $user_id))->result_array();
		foreach ($offline_payment_courses as $row) {
			$course_ids = json_decode($row['course_id'], true);
			if (in_array($course_id, $course_ids)) {
				if ($row['status'] == 0) {
					return "pending";
				} elseif ($row['status'] == 1) {
					return "approved";
				}
			}
		}
		return false;
	}


	public function settings()
	{
		$data['value'] = htmlspecialchars($this->input->post('bank_information', false));
		if($this->db->get_where('settings', ['key' => 'offline_bank_information'])->num_rows() > 0){
        	$this->db->where('key', 'offline_bank_information');
        	$this->db->update('settings', $data);
		}else{
			$data['key'] = 'offline_bank_information';
        	$this->db->insert('settings', $data);
		}
	}
}
