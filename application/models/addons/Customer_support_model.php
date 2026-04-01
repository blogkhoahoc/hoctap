<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Customer_support_model extends CI_Model {

    function __construct() {
        parent::__construct();
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    function add_support_category(){
        $data['title'] = html_escape($this->input->post('title'));
        $data['status'] = 'active';
        $this->db->where('title', $data['title']);
        $query=$this->db->get('support_category');  
        if($query->num_rows()>0)
        {
            return 0;
        }
        else
        {
            $this->db->insert('support_category', $data);
            return 1;
        }
    }

    function update_support_category($id = ""){
        $data['title'] = html_escape($this->input->post('title'));
        $this->db->where('title', $data['title']);
        $query=$this->db->get('support_category');  
        if($query->num_rows()>0)
        {
            return 0;
        }
        else
        {
            $this->db->where('id', $id);
            $this->db->update('support_category', $data);
            return 1;
        }
    }

     function change_category_status($status = "", $id = ''){
        $data['status'] = $status;
        $this->db->where('id', $id);
        return $this->db->update('support_category', $data);
        
    }

    function get_support_categories($id = ''){
        if($id > 0){
            $this->db->where('id', $id);
        }
        return $this->db->get('support_category');        
    }

    function get_tickets($id = ''){
        if($id > 0){
            $this->db->where('id', $id);
        }
        return $this->db->get('tickets');
    	
    }

    function get_tickets_by_user_id($user_id = ''){
        if($user_id > 0){
            $this->db->where('user_id', $user_id);
        }
        $this->db->order_by('id', 'desc');
        return $this->db->get('tickets');       
    }

     function get_tickets_by_code($code = ''){
        if($code != null){
            $this->db->where('code', $code);
        } 
        return $this->db->get('tickets');
        
    }

    function get_tickets_by_status($status = ''){
        if($status != null){
            $this->db->where('status', $status);
        } 
        $this->db->order_by('id', 'desc');
        return $this->db->get('tickets');
        
    }

    function get_ticket_details($code = ''){
        if($code != null){
            $this->db->where('code', $code);
        } 
        $this->db->order_by('id', 'desc');
        return $this->db->get('ticket_description');
        
    }

    function change_status($status = "", $id = ''){
        $data['status'] = $status;
        $this->db->where('id', $id);
        return $this->db->update('tickets', $data);
        
    }

    function change_priority($priority = "", $id = ''){
        $data['priority'] = $priority;
        $this->db->where('id', $id);
        return $this->db->update('tickets', $data);
        
    }

    function delete_ticket($id = "")
    {
        $ticket_code = $this->get_tickets($id)->row('code');
        $this->db->where('code', $ticket_code);
        $this->db->delete('ticket_description');
        $this->db->where('id', $id);
        $this->db->delete('tickets');
    }

    function delete_support_category($id = "")
    {
        $this->db->where('id', $id);
        $this->db->delete('support_category');
    }

    function get_support_macros($id = ''){
        if($id > 0){
            $this->db->where('id', $id);
        }
        return $this->db->get('support_macro');        
    }

     function add_support_macro(){
        $data['title'] = html_escape($this->input->post('title'));
        $data['description'] = $this->input->post('description'); 
        $this->db->insert('support_macro', $data);
    }

    function update_support_macro($id = ""){
        $data['title'] = html_escape($this->input->post('title'));
        $data['description'] = $this->input->post('description');
        $this->db->where('id', $id);
        $this->db->update('support_macro', $data);   
    }

    function delete_support_macro($id = "")
    {
        $this->db->where('id', $id);
        $this->db->delete('support_macro');
    }

    function add_support_ticket(){
        $data['title'] = html_escape($this->input->post('title'));
        $data['code'] = html_escape($this->input->post('code'));
        $data['category_id'] = html_escape($this->input->post('category_id'));
        $data['user_id'] = html_escape($this->input->post('user_id'));
        $data['status'] = 'opened';
        $data['priority'] = html_escape($this->input->post('priority'));
        $data['date'] = strtotime(date('d M Y'));

        $this->db->insert('tickets', $data);

        $data1['code'] = $data['code'];
        $data1['user_id'] = $data['user_id'];
        $data1['description'] = $this->input->post('description');
        $data1['date'] = $data['date'];
        $ext = pathinfo($_FILES['support_file']['name'], PATHINFO_EXTENSION);
        $data1['file_name'] = rand(500000, 1000000).'.'.$ext;
    
        $this->db->insert('ticket_description', $data1);
        move_uploaded_file($_FILES['support_file']['tmp_name'], 'uploads/support_files/' . $data1['file_name']);  
    }

    function add_user_support_ticket(){
        $data['title'] = html_escape($this->input->post('title'));
        $data['code'] = substr(rand(500000, 1000000), 0, 6);
        $data['category_id'] = html_escape($this->input->post('category_id'));
        $data['user_id'] = $this->session->userdata('user_id'); 
        $data['status'] = 'opened';
        $data['priority'] = html_escape($this->input->post('priority'));
        $data['date'] = strtotime(date('d M Y'));

        $this->db->insert('tickets', $data);

        $data1['code'] = $data['code'];
        $data1['user_id'] = $data['user_id'];
        $data1['description'] = $this->input->post('description');
        $data1['date'] = $data['date'];
        $ext = pathinfo($_FILES['support_file']['name'], PATHINFO_EXTENSION);
        $data1['file_name'] = rand(500000, 1000000).'.'.$ext;
    
        $this->db->insert('ticket_description', $data1);
        move_uploaded_file($_FILES['support_file']['tmp_name'], 'uploads/support_files/' . $data1['file_name']);  
    }

    function support_reply(){
        $data['code'] = html_escape($this->input->post('code'));
        $data['user_id'] = $this->session->userdata('user_id'); 
        $data['description'] = $this->input->post('description');
        $data['date'] = strtotime(date('d M Y'));
        $ext = pathinfo($_FILES['support_file']['name'], PATHINFO_EXTENSION);
        $data['file_name'] = rand(500000, 1000000).'.'.$ext;
    
        $this->db->insert('ticket_description', $data);
        move_uploaded_file($_FILES['support_file']['tmp_name'], 'uploads/support_files/' . $data['file_name']);  
    }

}