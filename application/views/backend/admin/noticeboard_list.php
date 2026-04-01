<?php foreach($notice_list as $notice): ?>
	<div class="alert alert-success pb-1 pt-1" role="alert" id="notice_del_button_<?php echo $notice['id']; ?>">
	    <a href="javascript:;" onclick="showAjaxModal('<?php echo site_url('addons/noticeboard/notice_details/'.$notice['id']); ?>', '<?php echo get_phrase('notice_details') ?>')" class="text-success"><strong><i class="mdi mdi-clipboard-arrow-right-outline"></i> <?php echo $notice['title']; ?> </strong></a>
	    <div class="dropright dropright float-right">
		    <button type="button" class="border-0 bg-transparent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    	<i class="mdi mdi-dots-vertical"></i>
		    </button>
		    <ul class="dropdown-menu" x-placement="left-start" y-placement="bottom">
		        <li><a class="dropdown-item" href="javascript:;" onclick="resend_notice('<?php echo $notice['id']; ?>', '<?php echo $notice['course_id']; ?>')"><?php echo get_phrase('resend_mail'); ?></a></li>
		        <li><a class="dropdown-item" href="javascript:;" onclick="showLargeModal('<?php echo site_url('addons/noticeboard/edit_notice/'.$notice['id']); ?>', '<?php echo get_phrase('edit_notice') ?>')"><?php echo get_phrase('edit'); ?></a></li>
		        <li><a class="dropdown-item" onclick="ajax_confirm_modal('<?php echo site_url('addons/noticeboard/notice_delete/'.$notice['id']); ?>', 'notice_del_button_<?php echo $notice['id']; ?>')" href="javascript:;"><?php echo get_phrase('delete'); ?></a></li>
		    </ul>
		</div>
	    <div class="w-100 text-12 text-muted"><?php echo ellipsis(strip_tags(htmlspecialchars_decode($notice['description'])), 80); ?></div>
	</div>
<?php endforeach; ?>