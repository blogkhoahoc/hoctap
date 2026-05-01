<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Data_center extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');

        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');

        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');

        if(strpos(base_url(), 'demo.creativeitem.com')){
            $this->session->set_flashdata('error_message', "This feature is not available in the demo platform.");
            redirect(site_url('admin/data_center'), 'refresh');
        }

        ini_set('memory_limit', '5000M');
    }

    public function index()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(site_url('login'), 'refresh');
        if ($this->session->userdata('admin_login') == 1)
            redirect(site_url('admin/dashboard'), 'refresh');
    }

    function demo_importer()
    {
        if(empty($_FILES["data_file"]["name"])){
            $this->session->set_flashdata('error_message', get_phrase('please_select_your_zip_file'));
            redirect(site_url('admin/data_center'), 'refresh');
        }

        if (!is_dir('backups'))
            mkdir('backups', 0777, true);

        $upload_path = 'uploads.zip';
        $backup_path = 'backups/uploads_v'.get_settings('version').'_'.date('d-M-Y-H-i-s');
        $uploaded_folder_name = explode('.zip' ,$_FILES["data_file"]["name"])[0];

        if(!strpos($_FILES["data_file"]["name"], get_settings('version'))){
            $this->session->set_flashdata('error_message', get_phrase('version_mismatching').'!');
            redirect(site_url('admin/data_center'), 'refresh');
        }

        if (class_exists('ZipArchive')) {
            rename('uploads', $backup_path);
            $this->backup_sql($backup_path);
            move_uploaded_file($_FILES["data_file"]["tmp_name"], $upload_path);

            $zip = new ZipArchive;
            $res = $zip->open($upload_path);
            $zip->extractTo('./');
            $zip->close();
            unlink($upload_path);

            rename($uploaded_folder_name, 'uploads');

            $files_and_folders = glob(BASEPATH.'../*', GLOB_MARK);
            foreach ($files_and_folders as $files_and_folder) {
                $files_and_folder_arr = explode('/', $files_and_folder);
                $folder_name = str_replace("\selector", '', end($files_and_folder_arr).'selector');
                $ext_file_name = preg_replace('/\s+/', '', explode(' (', $uploaded_folder_name)[0]);
                if(strpos($folder_name, $uploaded_folder_name) > 0 || strpos($folder_name, $ext_file_name) !== false){
                    rename($folder_name, 'uploads');
                }
            }
        }else{
            $this->session->set_flashdata('error_message', get_phrase('your_server_is_unable_to_extract_the_zip_file').'. '.get_phrase('please_enable_the_zip_extension_on_your_server').', '.get_phrase('then_try_again'));
            redirect(site_url('admin/data_center'), 'refresh');
        }

        $this->run_demo_sql('./uploads/demo.sql');
        unlink('uploads/demo.sql');

        $this->session->set_flashdata('imported_message', get_phrase('created_a_backup_file_of_your_old_data'));
        $this->session->set_flashdata('flash_message', get_phrase('demo_imported_successfully'));
        redirect(site_url('admin/data_center'), 'refresh');
    }

    function backup_sql($backup_path){
        $this->load->dbutil();
        $this->load->helper('file');
        $prefs = array(
            'format' => 'zip',
            'filename' => 'demo.sql',
            'add_drop'      => TRUE,
        );
        $backup =& $this->dbutil->backup($prefs);
        $save = $backup_path.'/demo.zip';
        write_file($save, $backup);
        $this->extract_zip_file($save, $backup_path, true);        
    }

    function run_demo_sql($file_path) {
        $this->load->database();
        $templine = '';
        $lines = file($file_path);
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;
            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                $this->db->query($templine);
                $templine = '';
            }
        }
    }

    function extract_zip_file($path, $extractTo, $delete_zip_file = false){
        $zip = new ZipArchive;
        $res = $zip->open($path);
        $zip->extractTo($extractTo);
        $zip->close();
        if($delete_zip_file == true){
            unlink($path);
        }
    }

    function download_zip_file($file_name){
        $this->load->helper('download');
        $file_path = 'backups/' . $file_name;

        if (file_exists($file_path) && !is_dir($file_path)) {
            force_download($file_path, NULL);
        } else {
            $this->session->set_flashdata('error_message', 'File not found');
            redirect(site_url('admin/data_center'), 'refresh');
        }
    }

    public function delete_dir($fileName = ""){
        if($fileName == ""){
            redirect(site_url('admin/data_center'), 'refresh');
        }
        
        $file_path = 'backups/' . $fileName;

        if (file_exists($file_path) && is_file($file_path)) {
            unlink($file_path);
        } 
        elseif (file_exists($file_path) && is_dir($file_path)) {
            $this->deleteDir($file_path);
        }

        $this->session->set_flashdata('flash_message', get_phrase('backup_files_deleted_successfully'));
        redirect(site_url('admin/data_center'), 'refresh');
    }

    public static function deleteDir($dirPath) {
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if(file_exists($file.'.htaccess')){
                unlink($file.'.htaccess');
            }
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    function create_backup_file(){
        set_time_limit(0); 

        if (!is_dir('backups')) {
            mkdir('backups', 0777, true);
        }

        $zip = new ZipArchive();
        $fileName = 'full_source_v'.get_settings('version').'_'.date('d-M-Y-H-i-s') . '.zip';
        $backup_file = FCPATH . 'backups/' . $fileName;

        if ($zip->open($backup_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $rootPath = realpath(FCPATH);

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    if (strpos($relativePath, 'backups') === 0) {
                        continue;
                    }

                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();

            // ĐÃ BỎ LỆNH BACKUP DATABASE Ở ĐÂY

            $this->session->set_flashdata('flash_message', get_phrase('your_backup_file_has_been_stored_successfully'));
            redirect(site_url('admin/data_center'), 'refresh');
        } else {
            $this->session->set_flashdata('error_message', 'Cannot create backup zip file.');
            redirect(site_url('admin/data_center'), 'refresh');
        }
    }

    public static function copyAllFilesAndFolders($dirPath, $backup_path){
        if (!is_dir('backups'))
            mkdir('backups', 0777, true);
        if (!is_dir($backup_path))
            mkdir($backup_path, 0777, true);

        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            $new_path = str_replace('uploads/', '', $file);
            if (is_dir($file)) {
                if (!is_dir($backup_path.'/'.$new_path))
                    mkdir($backup_path.'/'.$new_path, 0777, true);

                if(file_exists($file.'.htaccess')){
                    copy($file.'.htaccess', $backup_path.'/'.$new_path.'.htaccess');
                }
                self::copyAllFilesAndFolders($file, $backup_path);
            } else {
                copy($file, $backup_path.'/'.$new_path);
            }
        }
    }

    function language_import(){
        $this->load->dbforge();

        foreach($_FILES['language_files']['name'] as $key => $language){
            $language_name = strtolower(preg_replace('/\s+/', '_', explode('.', $_FILES['language_files']['name'][$key])[0]));
            if (!$this->db->field_exists($language_name, 'language')) {
                $fields = array(
                    $language_name => array(
                        'type' => 'LONGTEXT',
                        'default' => null,
                        'null' => TRUE,
                        'collation' => 'utf8_unicode_ci'
                    )
                );
                $this->dbforge->add_column('language', $fields);
            }

            $language_content_arr = json_decode(file_get_contents($_FILES['language_files']['tmp_name'][$key]), true);
            if(is_array($language_content_arr)){
                move_uploaded_file($_FILES['language_files']['tmp_name'][$key], 'application/language/'.$language_name.'.json');
            }else{
                $this->session->set_flashdata('error_message', get_phrase('JSON_validation_failed').'!');
                redirect(site_url('admin/data_center'), 'refresh');
            }

            foreach($language_content_arr as $phrase_key => $phrase){
                $phrase_key = strtolower(preg_replace('/\s+/', '_', $phrase_key));
                $query = $this->db->get_where('language', ['phrase' => $phrase_key]);

                if($query->num_rows() > 0){
                    $this->db->where('phrase', $phrase_key);
                    $this->db->update('language', [$language_name => $phrase]);
                }else{
                    $this->db->insert('language', ['phrase' => $phrase_key, $language_name => $phrase]);
                }
            }
        }

        $this->session->set_flashdata('flash_message', get_phrase('language_file_imported_successfully'));
        redirect(site_url('admin/data_center'), 'refresh');
    }

    // HÀM MỚI CHUYÊN DÙNG ĐỂ XUẤT DATABASE
    function export_database() {
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');

        $prefs = array(
            'format'    => 'zip',
            'filename'  => 'database.sql',
            'add_drop'  => TRUE,
        );
        $backup =& $this->dbutil->backup($prefs);
        $db_name = 'database_backup_' . date('d-M-Y-H-i-s') . '.zip';
        
        // Tải thẳng file về trình duyệt, không lưu trữ làm nặng host
        force_download($db_name, $backup);
    }
}