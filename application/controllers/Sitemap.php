<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sitemap extends CI_Controller {

    public function index() {
        // Chủ động load database và url helper
        $this->load->database();
        $this->load->helper('url');
        
        // Cố gắng load file chứa hàm chuyển đổi tiếng Việt (nếu có)
        if (file_exists(APPPATH.'helpers/common_helper.php')) {
            $this->load->helper('common');
        }

        // Khởi tạo mảng dữ liệu rỗng để tránh lỗi "Undefined variable"
        $data['courses']    = [];
        $data['blogs']      = [];
        $data['categories'] = [];

        // 1. Lấy dữ liệu khóa học (chỉ lấy khóa đang active)
        if ($this->db->table_exists('course')) {
            $this->db->where('status', 'active');
            $data['courses'] = $this->db->get('course')->result_array();
        }

        // 2. Lấy dữ liệu blog (Kiểm tra an toàn tên bảng)
        if ($this->db->table_exists('blogs')) {
            $this->db->where('status', 1);
            $data['blogs'] = $this->db->get('blogs')->result_array();
        } elseif ($this->db->table_exists('blog')) {
            $this->db->where('status', 1);
            $data['blogs'] = $this->db->get('blog')->result_array();
        }

        // 3. Lấy dữ liệu Danh mục (Category)
        if ($this->db->table_exists('category')) {
            $data['categories'] = $this->db->get('category')->result_array();
        }

        // Render ra file XML chuẩn của Google
        header("Content-Type: text/xml;charset=utf-8");
        $this->load->view('frontend/default/sitemap', $data);
    }
}