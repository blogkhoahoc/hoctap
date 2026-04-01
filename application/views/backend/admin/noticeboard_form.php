<form action="javascript:;">
	<div class="form-group row">
		<label class="col-md-3 col-lg-2 col-form-label" for="notice_title">
			<?php echo get_phrase('course_title'); ?><span class="required">*</span>
		</label>
		<div class="col-md-9 col-lg-10">
		    <input type="text" name="notice_title" id="notice_title" class="form-control" placeholder="<?php echo get_phrase('enter_your_notice_title'); ?>">
		</div>
	</div>

	<div class="form-group row">
		<label class="col-md-3 col-lg-2 col-form-label" for="notice_description">
			<?php echo get_phrase('description'); ?><span class="required">*</span>
		</label>
		<div class="col-md-9 col-lg-10">
		    <textarea name="notice_description" id="notice_description" class="form-control" placeholder="<?php echo get_phrase('enter_your_notice_description'); ?>"></textarea>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-md-3 col-lg-2 col-form-label"></label>
		<div class="col-md-9 col-lg-10">
		    <input type="checkbox" value="1" name="mail_to_students" id="mail_to_students"><label for="mail_to_students" class="ml-1"><?php echo get_phrase('send_mail_to_students_if_urgent'); ?></label>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-md-3 col-lg-2 col-form-label"></label>
		<div class="col-md-9 col-lg-10">
		    <button type="button" class="btn btn-success" onclick="add_new_notice(this)"><?php echo get_phrase('add_new_notice'); ?></button>
		</div>
	</div>
</form>

<script type="text/javascript">
	'use strict';
	
	$(document).ready(function () {
	    initSummerNote(['#notice_description']);
	});
</script>