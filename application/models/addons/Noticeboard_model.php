<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Noticeboard_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    public function add_notice($course_id = ""){
        if($this->is_valid_user($course_id)->num_rows() > 0){
            $data['title'] = htmlspecialchars($this->input->post('notice_title'));
	        $data['description'] = htmlspecialchars($this->input->post('notice_description'));
	        $data['status'] = 1;
	        $data['date_added'] = time();
	        $data['course_id'] = $course_id;
	        $this->db->insert('noticeboard', $data);
            $insert_id = $this->db->insert_id();

	        if($this->input->post('mail_to_students') > 0){
	        	$send_mail = $this->email_model->send_notice($insert_id, $course_id);
	        }else{
                $send_mail = 0;
            }

            $response['insert_id'] = $insert_id;
	        $response['status'] = 'success';
	        $response['send_mail'] = $send_mail;        	
            $response['message'] = get_phrase('notice_added_successfully');
            return json_encode($response);
        }else{
        	$response['status'] = 'error';
        	$response['send_mail'] = $send_mail;
            $response['message'] = get_phrase('you_do_not_have_permission_to_access_this_course');
            return json_encode($response);
        }
    }

    public function edit_notice($notice_id = ""){
        $notice_details = $this->get_notices($notice_id)->row_array();
        if($this->is_valid_user($notice_details['course_id'])->num_rows() > 0){
            $data['title'] = htmlspecialchars($this->input->post('notice_title'));
            $data['description'] = htmlspecialchars($this->input->post('notice_description'));
            $data['date_updated'] = time();
            $this->db->where('id', $notice_id);
            $this->db->update('noticeboard', $data);
            
            $response['status'] = 'success';
            $response['message'] = get_phrase('notice_updated_successfully');
            return json_encode($response);
        }else{
            $response['status'] = 'error';
            $response['message'] = get_phrase('you_do_not_have_permission_to_access_this_course');
            return json_encode($response);
        }
    }

    public function is_valid_user($course_id = ""){
        if ($this->session->userdata('admin_login') == true) {
            $query = $this->db->get_where('course', array('id' => $course_id));
        }else {
            $user_id = $this->session->userdata('user_id');
            $query = $this->db->get_where('course', array('id' => $course_id, 'user_id' => $user_id));
        }
        return $query;
    }

    public function get_notices($notice_id = ""){
    	if($notice_id > 0){
    		$this->db->where('id', $notice_id);
    	}
        $this->db->order_by('id', 'desc');
    	return $this->db->get('noticeboard');
    }

    public function get_notice_by_course_id($course_id = ""){
    	if($course_id > 0){
    		$this->db->where('course_id', $course_id);
    	}
        $this->db->order_by('id', 'desc');
    	return $this->db->get('noticeboard');
    }

    public function notice_delete($notice_id = ""){
        $this->db->where('id', $notice_id);
        $this->db->delete('noticeboard');

        $response['status'] = 'success';
        $response['message'] = get_phrase('notice_deleted_successfully');
        return json_encode($response);
    }













}