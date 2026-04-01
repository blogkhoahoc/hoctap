<form action="javascript:;">
	<div class="form-group">
		<label for="edit_notice_title">
			<?php echo get_phrase('course_title'); ?><span class="required">*</span>
		</label>
		<input type="text" name="notice_title" value="<?php echo $notice['title']; ?>" id="edit_notice_title" class="form-control" placeholder="<?php echo get_phrase('enter_your_notice_title'); ?>">
	</div>

	<div class="form-group">
		<label for="edit_notice_description">
			<?php echo get_phrase('description'); ?><span class="required">*</span>
		</label>
		   <textarea name="notice_description" id="edit_notice_description" class="form-control" placeholder="<?php echo get_phrase('enter_your_notice_description'); ?>"><?php echo $notice['description']; ?></textarea>
	</div>

	<div class="form-group">
		<button type="button" class="btn btn-success" onclick="update_notice(this, '<?php echo $notice['id']; ?>')"><?php echo get_phrase('update_notice'); ?></button>
	</div>
</form>

<script type="text/javascript">
	'use strict';
	
	$(document).ready(function () {
	    initSummerNote(['#edit_notice_description']);
	});
</script>