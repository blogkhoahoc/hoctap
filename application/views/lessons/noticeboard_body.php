<?php
	$CI	=&	get_instance();
    $CI->load->database();
    $CI->load->model('addons/noticeboard_model');
	$notifications = $CI->noticeboard_model->get_notice_by_course_id($course_id);
?>
<div class="row justify-content-center">
    <div class="col-md-7 p-0 text-center py-4">
    	<h6 class="text-left"><?php echo site_phrase('total').' '.$notifications->num_rows().' '.site_phrase('notifications');; ?></h6>
    	<hr>
    	<?php foreach($notifications->result_array() as $notification): ?>
    		<div class="card mt-3">
	    		<div class="card-body">
	    			<p class="text-left">
	    			    
	    			    <b><i class="far fa-clipboard pr-2"></i>
	    			    <?php echo htmlspecialchars_decode($notification['title']); ?></b>
	    			    
	    			    <small class="pl-3 text-muted text-12"><i class="text-dark far fa-clock"></i> <?php echo date('d M Y', $notification['date_added']); ?></small>
	    			</p>
	    			<div class="description text-left"><?php echo htmlspecialchars_decode($notification['description']); ?></div>
	    		</div>
	    	</div>
		<?php endforeach; ?>
	</div>
</div>
