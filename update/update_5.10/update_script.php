<?php
$CI = get_instance();
$CI->load->database();
$CI->load->dbforge();


//Rename course thumbnails
$courses = $CI->db->get_where('course')->result_array();
foreach($courses as $course){
    if (file_exists('uploads/thumbnails/course_thumbnails/'. 'course_thumbnail_' .get_frontend_settings('theme') . '_' . $course['id']. '.jpg')) {

        rename('uploads/thumbnails/course_thumbnails/'. 'course_thumbnail_' .get_frontend_settings('theme') . '_' . $course['id']. '.jpg', 'uploads/thumbnails/course_thumbnails/'. 'course_thumbnail_' .get_frontend_settings('theme') . '_' . $course['id'].$course['last_modified'] . '.jpg');
    }

    if (file_exists('uploads/thumbnails/course_thumbnails/optimized/'. 'course_thumbnail_' .get_frontend_settings('theme') . '_' . $course['id']. '.jpg')) {

        rename('uploads/thumbnails/course_thumbnails/optimized/'. 'course_thumbnail_' .get_frontend_settings('theme') . '_' . $course['id']. '.jpg', 'uploads/thumbnails/course_thumbnails/optimized/'. 'course_thumbnail_' .get_frontend_settings('theme') . '_' . $course['id'].$course['last_modified'] . '.jpg');
    }
}


//ADD cloud_video_id COLUMN IN lesson TABLE 
$cloud_video_id = array(
    'cloud_video_id' => array(
        'type' => 'INT',
        'null' => TRUE,
        'collation' => 'utf8_unicode_ci'
    )
);
$CI->dbforge->add_column('lesson', $cloud_video_id);

//ADD current_duration COLUMN IN watched_duration TABLE 
$current_duration = array(
    'current_duration' => array(
        'type' => 'INT',
        'null' => TRUE,
        'collation' => 'utf8_unicode_ci'
    )
);
$CI->dbforge->add_column('watched_duration', $current_duration);

//ADD caption COLUMN IN lesson TABLE 
$caption = array(
    'caption' => array(
        'type' => 'varchar',
        'constraint' => 255,
        'null' => TRUE,
        'collation' => 'utf8_unicode_ci'
    )
);
$CI->dbforge->add_column('lesson', $caption);

//ADD tax COLUMN IN payment TABLE 
$tax = array(
    'tax' => array(
        'type' => 'double',
        'null' => TRUE,
        'collation' => 'utf8_unicode_ci'
    )
);
$CI->dbforge->add_column('payment', $tax);

if($CI->db->get_where('settings', ['key' => 'course_selling_tax'])->num_rows() == 0){
	$CI->db->insert('settings', ['key' => 'course_selling_tax', 'value' => 0]);
}

if($CI->db->get_where('settings', ['key' => 'academy_cloud_access_token'])->num_rows() == 0){
	$CI->db->insert('settings', ['key' => 'academy_cloud_access_token', 'value' => 'enter-your-cloud-access-token']);
}


// update VERSION NUMBER INSIDE SETTINGS TABLE
$settings_data = array( 'value' => '5.10');
$CI->db->where('key', 'version');
$CI->db->update('settings', $settings_data);
