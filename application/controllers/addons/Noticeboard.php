<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Noticeboard extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('addons/noticeboard_model');
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        if ($this->session->userdata('user_id') != true) {
            redirect(site_url('login'), 'refresh');
        }
    }

    public function load_noticeboard_form($course_id = ""){
        $this->load->view('backend/'.strtolower($this->session->userdata('role')).'/noticeboard_form');
    }

    public function load_notice_list($course_id = ""){
        $page_data['notice_list'] = $this->noticeboard_model->get_notice_by_course_id($course_id)->result_array();
        $this->load->view('backend/'.strtolower($this->session->userdata('role')).'/noticeboard_list', $page_data);
    }

    public function load_single_notice($notice_id = ""){
        $page_data['notice_list'] = $this->noticeboard_model->get_notices($notice_id)->result_array();
        $this->load->view('backend/'.strtolower($this->session->userdata('role')).'/noticeboard_list', $page_data);
    }

    public function add_notice($course_id = ""){
        echo $this->noticeboard_model->add_notice($course_id);
    }

    public function edit_notice($notice_id = "", $param1 = ""){
        if($param1 == 'update'):
            echo $this->noticeboard_model->edit_notice($notice_id);
        else:
            $page_data['notice'] = $this->noticeboard_model->get_notices($notice_id)->row_array();
            $this->load->view('backend/'.strtolower($this->session->userdata('role')).'/noticeboard_form_edit', $page_data);
        endif;
    }

    public function notice_details($notice_id = ""){
        $notice_description = $this->noticeboard_model->get_notices($notice_id)->row_array();
        echo '<h5>'.htmlspecialchars_decode($notice_description['title']).'</h5><small>'.htmlspecialchars_decode($notice_description['description']).'</small>';
    }

    public function notice_delete($notice_id = ""){
        echo $this->noticeboard_model->notice_delete($notice_id);
    }

    public function resend_notice($notice_id = "", $course_id = ""){
        if($this->noticeboard_model->is_valid_user($course_id)->num_rows() > 0){
            $status = $this->email_model->send_notice($notice_id, $course_id);
            $response['status'] = $status;
            $response['message'] = get_phrase('mail_sent_successfully');
        }else{
            $response['status'] = 0;
            $response['message'] = get_phrase('mail_sent_was_not_successful');
        }
        echo json_encode($response);
    }

    public function load_notices_for_lesson_page($course_id = ""){
        $page_data['course_id'] = $course_id;
        $this->load->view('lessons/noticeboard_body', $page_data);
    }





}