<?php
$CI = get_instance();
$CI->load->database();
$CI->load->dbforge();


//ADD faqs COLUMN IN lesson TABLE 
$faqs = array(
    'faqs' => array(
        'type' => 'text',
        'collation' => 'utf8_unicode_ci'
    )
);
$CI->dbforge->add_column('course', $faqs);

// update VERSION NUMBER INSIDE SETTINGS TABLE
$settings_data = array( 'value' => '5.11');
$CI->db->where('key', 'version');
$CI->db->update('settings', $settings_data);



//language store to database
$files = scandir(APPPATH . '/language');
foreach ($files as $key => $file) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if($ext == 'json'){
        $language = explode('.',$file)[0];

        if (!$CI->db->field_exists($language, 'language')) {
            $fields = array(
                $language => array(
                    'type' => 'LONGTEXT',
                    'default' => null,
                    'null' => TRUE,
                    'collation' => 'utf8_unicode_ci'
                )
            );
            $CI->dbforge->add_column('language', $fields);
        }


        $path = 'application/language/'.$file;
        $phrase_arr = json_decode(file_get_contents($path), true);
        foreach($phrase_arr as $phrase_key => $phrase):
            $phrase_key = strtolower(preg_replace('/\s+/', '_', $phrase_key));
            $query = $CI->db->get_where('language', ['phrase' => $phrase_key]);

            if($query->num_rows() > 0){
                $CI->db->where('phrase', $phrase_key);
                $CI->db->update('language', [$language => $phrase]);
            }else{
                $CI->db->insert('language', ['phrase' => $phrase_key, $language => $phrase]);
            }
        endforeach;
    }
}
