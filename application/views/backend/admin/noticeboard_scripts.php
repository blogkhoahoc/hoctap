<script type="text/javascript">
	'use strict';

	function load_notic_list(){
		if(!$('#notice_list').html()){
			$('.ajax_loaderBar').addClass('start_ajax_loading');
	    	$.ajax({
		        url: '<?php echo site_url('addons/noticeboard/load_notice_list/'.$course_details['id']) ?>',
		        success: function(response){
		        	$('#notice_list').html(response);
		        	$('.ajax_loaderBar').removeClass('start_ajax_loading');
		        }
		    });
		}
	}

	function load_notic_form(){
    	$.ajax({
	        url: '<?php echo site_url('addons/noticeboard/load_noticeboard_form') ?>',
	        success: function(response){
	        	$('#noticeboard_form').html(response);
	        }
	    });
	}


	function add_new_notice(btn){
		//loading start
		var btn_text = $(btn).text();
		$(btn).html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span><?= get_phrase('uploading'); ?>...');
		$(btn).prop("disabled",true);
		$('.ajax_loaderBar').addClass('start_ajax_loading');

		var notice_title = $('#notice_title').val();
		var notice_description = $('#notice_description').val();

		if(document.getElementById('mail_to_students').checked){
			var mail_to_students = 1;
		}else{
			var mail_to_students = 0;
		}

		if(notice_title != "" && notice_description != "") {
		    $.ajax({
		        url: '<?php echo site_url('addons/noticeboard/add_notice/'.$course_details['id']) ?>',
		        type: 'post',
		        data: {notice_title : notice_title, notice_description : notice_description, mail_to_students : mail_to_students},                        
		        success: function(response){
		        	var response = JSON.parse(response);
		   			if(response.status == 'success'){

		   				show_added_notice(response.insert_id);
		   				load_notic_form();

		         		$.NotificationApp.send("<?php echo get_phrase('success'); ?>!", response.message ,"top-right","rgba(0,0,0,0.2)","success");
					}else{
						$.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", response.message ,"top-right","rgba(0,0,0,0.2)","error");
		   			}

					$('.ajax_loaderBar').removeClass('start_ajax_loading');
		        }
		    });
		}else{
			$(btn).html(btn_text);
			$(btn).prop("disabled",false);
			$('.ajax_loaderBar').removeClass('start_ajax_loading');
			$.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", "<?php echo get_phrase('fill_up_the_required_feilds'); ?>" ,"top-right","rgba(0,0,0,0.2)","error");
		}
	}

	function update_notice(btn, notice_id){
		//loading start
		var btn_text = $(btn).text();
		$(btn).html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span><?= get_phrase('updating'); ?>...');
		$(btn).prop("disabled",true);
		$('.ajax_loaderBar').addClass('start_ajax_loading');

		var notice_title = $('#edit_notice_title').val();
		var notice_description = $('#edit_notice_description').val();

		if(notice_title != "" && notice_description != "") {
		    $.ajax({
		        url: '<?php echo site_url('addons/noticeboard/edit_notice/') ?>' + notice_id + '/update',
		        type: 'post',
		        data: {notice_title : notice_title, notice_description : notice_description},                        
		        success: function(response){
		        	var response = JSON.parse(response);
		   			if(response.status == 'success'){
		   				$('div').modal('hide');
		   				$('#notice_list').html('');
		   				load_notic_list();
		         		$.NotificationApp.send("<?php echo get_phrase('success'); ?>!", response.message ,"top-right","rgba(0,0,0,0.2)","success");
					}else{
						$.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", response.message ,"top-right","rgba(0,0,0,0.2)","error");
		   			}

					$('.ajax_loaderBar').removeClass('start_ajax_loading');
					$(btn).html(btn_text);
					$(btn).prop("disabled",false);
		        }
		    });
		}else{
			$(btn).html(btn_text);
			$(btn).prop("disabled",false);
			$('.ajax_loaderBar').removeClass('start_ajax_loading');
			$.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", "<?php echo get_phrase('fill_up_the_required_feilds'); ?>" ,"top-right","rgba(0,0,0,0.2)","error");
		}
	}

	function show_added_notice(notice_id){
		$.ajax({
	        url: '<?php echo site_url('addons/noticeboard/load_single_notice/'); ?>' + notice_id,
	        success: function(response){
	        	$('#notice_list').prepend(response);
	        	$('.ajax_loaderBar').removeClass('start_ajax_loading');
	        }
	    });
	}

	function resend_notice(notice_id, course_id){
		$('.ajax_loaderBar').addClass('start_ajax_loading');

		$.ajax({
	        url: '<?php echo site_url('addons/noticeboard/resend_notice/'); ?>' + notice_id + '/' + course_id,
	        success: function(response){
	        	var response = JSON.parse(response);
	   			if(response.status == '1'){
	         		$.NotificationApp.send("<?php echo get_phrase('success'); ?>!", response.message ,"top-right","rgba(0,0,0,0.2)","success");
				}else{
					$.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", response.message ,"top-right","rgba(0,0,0,0.2)","error");
	   			}
	        	$('.ajax_loaderBar').removeClass('start_ajax_loading');
	        }
	    });
	}
</script>